<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\JenisVaksin;
use Illuminate\Validation\ValidationException;

class StokService
{
    /**
     * Alokasi FEFO: ambil batch dengan kedaluwarsa terdekat lebih dulu (FR-05).
     * Jika satu batch tidak cukup, sisanya diambil dari batch berikutnya.
     *
     * @return array<int, array{batch: Batch, jumlah: int}>
     */
    public function alokasiFefo(int $idJenisVaksin, int $jumlah): array
    {
        $batches = Batch::with('jenisVaksin')
            ->where('id_jenis_vaksin', $idJenisVaksin)
            ->untukDistribusi()
            ->fefo()
            ->get();

        $tersedia = (int) $batches->sum('stok_saat_ini');
        if ($tersedia < $jumlah) {
            $nama = JenisVaksin::find($idJenisVaksin)?->nama_vaksin ?? 'vaksin';
            throw ValidationException::withMessages([
                'items' => "Stok {$nama} tidak mencukupi: diminta {$jumlah} vial, tersedia {$tersedia} vial (batch kedaluwarsa tidak dihitung).",
            ]);
        }

        $sisa = $jumlah;
        $hasil = [];
        foreach ($batches as $batch) {
            if ($sisa <= 0) {
                break;
            }
            $ambil = min($sisa, $batch->stok_saat_ini);
            $hasil[] = ['batch' => $batch, 'jumlah' => $ambil];
            $sisa -= $ambil;
        }

        return $hasil;
    }

    public function perbaruiStatus(Batch $batch): void
    {
        $batch->status = $batch->hitungStatus();
        $batch->save();
    }
}