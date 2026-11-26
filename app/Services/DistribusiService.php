<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\DetailDistribusi;
use App\Models\Distribusi;
use App\Models\Fasilitas;
use App\Models\Nota;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DistribusiService
{
    public function __construct(private StokService $stok)
    {
    }

    /**
     * Alur distribusi: Draft (lolos validasi + saran FEFO) -> Final (setelah verifikasi batch)
     * -> Dibatalkan (hanya mengembalikan stok). Draft tidak mengurangi stok.
     *
     * @param array{id_fasilitas:int,nama_pengambil:string} $data
     * @param array<int, array{id_jenis_vaksin:int,jumlah:int}> $items
     */
    public function buatDraft(Pengguna $user, array $data, array $items): Distribusi
    {
        $fasilitas = Fasilitas::find($data['id_fasilitas']);
        if (! $fasilitas || ! $fasilitas->aktif) {
            throw ValidationException::withMessages(['id_fasilitas' => 'Fasilitas penerima belum terdaftar atau tidak aktif.']);
        }

        // Gabungkan baris dengan jenis vaksin yang sama.
        $perJenis = [];
        foreach ($items as $item) {
            $id = (int) $item['id_jenis_vaksin'];
            $perJenis[$id] = ($perJenis[$id] ?? 0) + (int) $item['jumlah'];
        }

        $alokasi = [];
        foreach ($perJenis as $idJenis => $jumlah) {
            foreach ($this->stok->alokasiFefo($idJenis, $jumlah) as $a) {
                $alokasi[] = $a;
            }
        }

        return DB::transaction(function () use ($user, $data, $alokasi) {
            $distribusi = Distribusi::create([
                'id_fasilitas' => $data['id_fasilitas'],
                'id_pengguna' => $user->id_pengguna,
                'tanggal_transaksi' => now(),
                'nama_pengambil' => $data['nama_pengambil'],
                'status' => Distribusi::DRAFT,
            ]);

            foreach ($alokasi as $a) {
                DetailDistribusi::create([
                    'id_distribusi' => $distribusi->id_distribusi,
                    'id_batch' => $a['batch']->id_batch,
                    'jumlah' => $a['jumlah'],
                    'batch_terverifikasi' => false,
                ]);
            }

            AuditLogger::catat($user, 'distribusi.draft', 'distribusi', $distribusi->id_distribusi, [
                'id_fasilitas' => $data['id_fasilitas'],
                'jumlah_item' => count($alokasi),
            ]);

            return $distribusi;
        });
    }

    /**
     * FR-06, FR-07, FR-08: validasi ulang stok, verifikasi batch, kurangi stok, terbitkan nota.
     * Semua dalam satu transaksi database dengan penguncian baris batch.
     *
     * @param array<int, array{id_batch?:int,terverifikasi?:mixed}> $baris Dikunci oleh id_detail_distribusi.
     */
    public function finalisasi(Distribusi $distribusi, Pengguna $user, array $baris, bool $konfirmasiNonFefo): Distribusi
    {
        return DB::transaction(function () use ($distribusi, $user, $baris, $konfirmasiNonFefo) {
            $d = Distribusi::lockForUpdate()->findOrFail($distribusi->id_distribusi);
            if ($d->status !== Distribusi::DRAFT) {
                throw ValidationException::withMessages(['status' => 'Hanya transaksi berstatus Draft yang dapat difinalisasi.']);
            }

            $details = DetailDistribusi::with('batch')->where('id_distribusi', $d->id_distribusi)->get();
            if ($details->isEmpty()) {
                throw ValidationException::withMessages(['status' => 'Transaksi tidak memiliki item.']);
            }

            // BR-04: setiap batch wajib dicocokkan dengan kemasan fisik.
            $pilihan = [];
            foreach ($details as $det) {
                $in = $baris[$det->id_detail_distribusi] ?? [];
                if (empty($in['terverifikasi'])) {
                    throw ValidationException::withMessages([
                        'verifikasi' => 'Semua batch harus dicocokkan dengan kemasan fisik dan dicentang sebelum finalisasi.',
                    ]);
                }
                $pilihan[$det->id_detail_distribusi] = (int) ($in['id_batch'] ?? $det->id_batch);
            }
            if (count($pilihan) !== count(array_unique($pilihan))) {
                throw ValidationException::withMessages(['verifikasi' => 'Satu batch tidak boleh dipakai dua kali pada transaksi yang sama.']);
            }

            $batches = Batch::with('jenisVaksin')
                ->whereIn('id_batch', array_values($pilihan))
                ->lockForUpdate()
                ->get()
                ->keyBy('id_batch');

            // Cek ulang stok dan kedaluwarsa (BR-01, BR-02).
            foreach ($details as $det) {
                $batch = $batches[$pilihan[$det->id_detail_distribusi]] ?? null;
                if (! $batch || (int) $batch->id_jenis_vaksin !== (int) $det->batch->id_jenis_vaksin) {
                    throw ValidationException::withMessages(['verifikasi' => 'Batch pengganti harus berasal dari jenis vaksin yang sama.']);
                }
                if ($batch->kedaluwarsa()) {
                    throw ValidationException::withMessages(['verifikasi' => "Batch {$batch->nomor_batch} sudah kedaluwarsa."]);
                }
                if ($batch->stok_saat_ini < $det->jumlah) {
                    throw ValidationException::withMessages([
                        'verifikasi' => "Stok batch {$batch->nomor_batch} tinggal {$batch->stok_saat_ini} vial, kurang dari {$det->jumlah} vial yang diminta.",
                    ]);
                }
            }

            // BR-03: penyimpangan dari FEFO harus dikonfirmasi dan tercatat.
            $konsumsi = [];
            foreach ($details as $det) {
                $konsumsi[$pilihan[$det->id_detail_distribusi]] = $det->jumlah;
            }
            $nonFefo = [];
            foreach ($details as $det) {
                $batch = $batches[$pilihan[$det->id_detail_distribusi]];
                $tgl = $batch->tanggal_kadaluwarsa->toDateString();
                $lebihAwal = Batch::where('id_jenis_vaksin', $batch->id_jenis_vaksin)
                    ->untukDistribusi()
                    ->where('id_batch', '!=', $batch->id_batch)
                    ->where(function ($q) use ($tgl, $batch) {
                        $q->whereDate('tanggal_kadaluwarsa', '<', $tgl)
                            ->orWhere(function ($q2) use ($tgl, $batch) {
                                $q2->whereDate('tanggal_kadaluwarsa', $tgl)->where('id_batch', '<', $batch->id_batch);
                            });
                    })
                    ->get();
                foreach ($lebihAwal as $x) {
                    if ($x->stok_saat_ini - ($konsumsi[$x->id_batch] ?? 0) > 0) {
                        $nonFefo[] = "{$batch->nomor_batch} (masih ada {$x->nomor_batch} yang lebih dulu kedaluwarsa)";
                        break;
                    }
                }
            }
            if ($nonFefo && ! $konfirmasiNonFefo) {
                throw ValidationException::withMessages([
                    'fefo' => 'Batch berikut bukan prioritas FEFO: ' . implode('; ', $nonFefo) . '. Centang konfirmasi untuk melanjutkan.',
                ]);
            }

            foreach ($details as $det) {
                $batch = $batches[$pilihan[$det->id_detail_distribusi]];

                $det->id_batch = $batch->id_batch;
                $det->batch_terverifikasi = true;
                $det->waktu_terverifikasi = now();
                $det->save();

                $batch->stok_saat_ini -= $det->jumlah;
                $batch->status = $batch->hitungStatus();
                $batch->save();
            }

            $d->status = Distribusi::FINAL;
            $d->save();

            $nota = Nota::create([
                'id_distribusi' => $d->id_distribusi,
                'no_nota' => $this->nomorNotaBerikut(),
                'tanggal_terbit' => now(),
            ]);

            AuditLogger::catat($user, 'distribusi.final', 'distribusi', $d->id_distribusi, [
                'no_nota' => $nota->no_nota,
                'non_fefo_dikonfirmasi' => $nonFefo,
            ]);

            return $d;
        });
    }

    /** FR-14: pembatalan hanya mengembalikan stok ke batch asal; transaksi tidak dihapus. */
    public function batalkan(Distribusi $distribusi, Pengguna $user, string $alasan): Distribusi
    {
        return DB::transaction(function () use ($distribusi, $user, $alasan) {
            $d = Distribusi::lockForUpdate()->findOrFail($distribusi->id_distribusi);
            if ($d->status !== Distribusi::FINAL) {
                throw ValidationException::withMessages(['status' => 'Hanya transaksi berstatus Final yang dapat dibatalkan.']);
            }

            $details = DetailDistribusi::where('id_distribusi', $d->id_distribusi)->get();
            $batches = Batch::with('jenisVaksin')
                ->whereIn('id_batch', $details->pluck('id_batch'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id_batch');

            foreach ($details as $det) {
                $batch = $batches[$det->id_batch];
                $batch->stok_saat_ini += $det->jumlah;
                $batch->status = $batch->hitungStatus();
                $batch->save();
            }

            $d->status = Distribusi::DIBATALKAN;
            $d->alasan_batal = $alasan;
            $d->waktu_batal = now();
            $d->id_pengguna_pembatal = $user->id_pengguna;
            $d->save();

            AuditLogger::catat($user, 'distribusi.batal', 'distribusi', $d->id_distribusi, ['alasan' => $alasan]);

            return $d;
        });
    }

    /** Draft belum memengaruhi stok dan belum bernota, sehingga boleh dibuang. */
    public function hapusDraft(Distribusi $distribusi, Pengguna $user): void
    {
        DB::transaction(function () use ($distribusi, $user) {
            $d = Distribusi::lockForUpdate()->findOrFail($distribusi->id_distribusi);
            if ($d->status !== Distribusi::DRAFT) {
                throw ValidationException::withMessages(['status' => 'Hanya Draft yang dapat dibuang.']);
            }

            DetailDistribusi::where('id_distribusi', $d->id_distribusi)->delete();
            $d->delete();

            AuditLogger::catat($user, 'distribusi.draft_dibuang', 'distribusi', $distribusi->id_distribusi);
        });
    }

    /** Format usulan: NOTA/2026/11/0001. Dijaga oleh indeks unik pada nota.no_nota. */
    private function nomorNotaBerikut(): string
    {
        $prefix = config('vaksin.prefix_nota', 'NOTA') . '/' . now()->format('Y/m');

        $terakhir = Nota::where('no_nota', 'like', $prefix . '/%')
            ->lockForUpdate()
            ->orderByDesc('id_nota')
            ->value('no_nota');

        $urut = $terakhir ? ((int) substr($terakhir, strrpos($terakhir, '/') + 1)) + 1 : 1;

        return sprintf('%s/%04d', $prefix, $urut);
    }
}