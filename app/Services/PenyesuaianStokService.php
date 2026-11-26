<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\PenyesuaianStok;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;

class PenyesuaianStokService
{
    /** FR-11: koreksi stok dengan alasan wajib dan jejak audit. */
    public function sesuaikan(Pengguna $user, int $idBatch, int $stokSesudah, string $alasan): PenyesuaianStok
    {
        return DB::transaction(function () use ($user, $idBatch, $stokSesudah, $alasan) {
            $batch = Batch::with('jenisVaksin')->lockForUpdate()->findOrFail($idBatch);
            $sebelum = $batch->stok_saat_ini;

            $batch->stok_saat_ini = $stokSesudah;
            $batch->status = $batch->hitungStatus();
            $batch->save();

            $catatan = PenyesuaianStok::create([
                'id_batch' => $batch->id_batch,
                'id_pengguna' => $user->id_pengguna,
                'tanggal' => now(),
                'stok_sebelum' => $sebelum,
                'stok_sesudah' => $stokSesudah,
                'alasan' => $alasan,
            ]);

            AuditLogger::catat($user, 'stok.penyesuaian', 'batch', $batch->id_batch, [
                'sebelum' => $sebelum,
                'sesudah' => $stokSesudah,
                'alasan' => $alasan,
            ]);

            return $catatan;
        });
    }
}