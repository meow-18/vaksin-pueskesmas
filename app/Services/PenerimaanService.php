<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\JenisVaksin;
use App\Models\Penerimaan;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenerimaanService
{
    /**
     * FR-03: satu pengiriman dapat memuat beberapa batch.
     *
     * @param array{tanggal_penerimaan:string,sumber_penerimaan:string,no_dokumen_pengiriman:string} $header
     * @param array<int, array{id_jenis_vaksin:int,nomor_batch:string,tanggal_kadaluwarsa:string,jumlah_diterima:int}> $items
     */
    public function simpan(Pengguna $user, array $header, array $items): Penerimaan
    {
        return DB::transaction(function () use ($user, $header, $items) {
            $penerimaan = Penerimaan::create([
                'id_pengguna' => $user->id_pengguna,
                'tanggal_penerimaan' => $header['tanggal_penerimaan'],
                'sumber_penerimaan' => $header['sumber_penerimaan'],
                'no_dokumen_pengiriman' => $header['no_dokumen_pengiriman'],
            ]);

            foreach ($items as $i => $item) {
                // BR-13: nomor batch unik per jenis vaksin.
                $ada = Batch::where('id_jenis_vaksin', $item['id_jenis_vaksin'])
                    ->where('nomor_batch', $item['nomor_batch'])
                    ->lockForUpdate()
                    ->exists();
                if ($ada) {
                    throw ValidationException::withMessages([
                        "items.{$i}.nomor_batch" => "Nomor batch {$item['nomor_batch']} sudah terdaftar untuk vaksin ini.",
                    ]);
                }

                $batch = new Batch([
                    'id_jenis_vaksin' => $item['id_jenis_vaksin'],
                    'id_penerimaan' => $penerimaan->id_penerimaan,
                    'nomor_batch' => $item['nomor_batch'],
                    'tanggal_kadaluwarsa' => $item['tanggal_kadaluwarsa'],
                    'jumlah_diterima' => $item['jumlah_diterima'],
                    'stok_saat_ini' => $item['jumlah_diterima'],
                ]);
                $batch->setRelation('jenisVaksin', JenisVaksin::findOrFail($item['id_jenis_vaksin']));
                $batch->status = $batch->hitungStatus();
                $batch->save();
            }

            AuditLogger::catat($user, 'penerimaan.simpan', 'penerimaan', $penerimaan->id_penerimaan, [
                'no_dokumen' => $header['no_dokumen_pengiriman'],
                'jumlah_batch' => count($items),
            ]);

            return $penerimaan;
        });
    }
}