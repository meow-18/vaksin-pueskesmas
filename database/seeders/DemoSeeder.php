<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\JenisVaksin;
use App\Models\Pengguna;
use App\Services\PenerimaanService;
use Illuminate\Database\Seeder;

/**
 * Data DUMMY untuk pengembangan dan uji coba (bukan data nyata puskesmas).
 * Jalankan: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $vaksin = [
            ['kode' => 'VK-001', 'nama_vaksin' => 'Vaksin Dummy A', 'vial_per_kotak' => 10, 'stok_minimum' => 20],
            ['kode' => 'VK-002', 'nama_vaksin' => 'Vaksin Dummy B', 'vial_per_kotak' => 5, 'stok_minimum' => 10],
            ['kode' => 'VK-003', 'nama_vaksin' => 'Vaksin Dummy C', 'vial_per_kotak' => 20, 'stok_minimum' => 30],
        ];
        foreach ($vaksin as $v) {
            JenisVaksin::firstOrCreate(['kode' => $v['kode']], $v + ['satuan' => 'vial', 'aktif' => true]);
        }

        foreach ([
            ['nama_fasilitas' => 'RS Dummy Sejahtera', 'jenis' => 'Rumah Sakit', 'alamat' => 'Jl. Contoh No. 1'],
            ['nama_fasilitas' => 'Klinik Dummy Sehat', 'jenis' => 'Klinik', 'alamat' => 'Jl. Contoh No. 2'],
        ] as $f) {
            Fasilitas::firstOrCreate(['nama_fasilitas' => $f['nama_fasilitas']], $f + ['aktif' => true]);
        }

        $apoteker = Pengguna::where('username', 'apoteker')->firstOrFail();
        $ids = JenisVaksin::orderBy('id_jenis_vaksin')->pluck('id_jenis_vaksin')->all();
        $stamp = now()->format('ymdHis');

        app(PenerimaanService::class)->simpan($apoteker, [
            'tanggal_penerimaan' => now()->toDateString(),
            'sumber_penerimaan' => 'Dinas Kesehatan (dummy)',
            'no_dokumen_pengiriman' => 'DUMMY-' . $stamp,
        ], [
            ['id_jenis_vaksin' => $ids[0], 'nomor_batch' => "A-{$stamp}-1", 'tanggal_kadaluwarsa' => now()->addDays(45)->toDateString(), 'jumlah_diterima' => 100],
            ['id_jenis_vaksin' => $ids[0], 'nomor_batch' => "A-{$stamp}-2", 'tanggal_kadaluwarsa' => now()->addDays(120)->toDateString(), 'jumlah_diterima' => 200],
            ['id_jenis_vaksin' => $ids[1], 'nomor_batch' => "B-{$stamp}-1", 'tanggal_kadaluwarsa' => now()->addDays(90)->toDateString(), 'jumlah_diterima' => 60],
            ['id_jenis_vaksin' => $ids[2], 'nomor_batch' => "C-{$stamp}-1", 'tanggal_kadaluwarsa' => now()->addDays(20)->toDateString(), 'jumlah_diterima' => 25],
        ]);
    }
}