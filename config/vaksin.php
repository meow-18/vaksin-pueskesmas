<?php

return [
    // Nama fasilitas pemilik data, tampil di kop nota dan laporan.
    'nama_puskesmas' => env('PUSKESMAS_NAMA', 'Puskesmas (isi nama di .env)'),
    'alamat_puskesmas' => env('PUSKESMAS_ALAMAT', ''),

    // Awalan nomor nota. Format akhir: NOTA/2026/11/0001 (usulan, belum ditetapkan puskesmas).
    'prefix_nota' => env('NOTA_PREFIX', 'NOTA'),

    // Batas hari "mendekati kedaluwarsa" pada dashboard (asumsi, perlu dikonfirmasi puskesmas).
    'hari_peringatan_kedaluwarsa' => 30,
];