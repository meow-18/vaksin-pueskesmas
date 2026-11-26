<?php

namespace App\Console\Commands;

use App\Models\Batch;
use Illuminate\Console\Command;

class SegarkanStatusBatch extends Command
{
    protected $signature = 'vaksin:segarkan-status';
    protected $description = 'Hitung ulang status batch (Tersedia/Terbatas/Habis/Kadaluwarsa) berdasarkan stok dan tanggal.';

    public function handle(): int
    {
        $diubah = 0;
        Batch::with('jenisVaksin')->chunkById(200, function ($kumpulan) use (&$diubah) {
            foreach ($kumpulan as $batch) {
                $baru = $batch->hitungStatus();
                if ($batch->status !== $baru) {
                    $batch->status = $baru;
                    $batch->save();
                    $diubah++;
                }
            }
        }, 'id_batch');

        $this->info("Status diperbarui pada {$diubah} batch.");

        return self::SUCCESS;
    }
}