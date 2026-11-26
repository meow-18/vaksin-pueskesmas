<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun awal untuk pengembangan. Ganti kata sandi lewat .env sebelum dipakai di server.
 */
class AkunAwalSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['username' => 'admin', 'nama' => 'Admin Sistem', 'role' => Role::ADMIN, 'env' => 'SEED_ADMIN_PASSWORD'],
            ['username' => 'apoteker', 'nama' => 'Petugas Apoteker', 'role' => Role::APOTEKER, 'env' => 'SEED_APOTEKER_PASSWORD'],
        ];

        foreach ($akun as $a) {
            Pengguna::firstOrCreate(
                ['username' => $a['username']],
                [
                    'id_role' => Role::where('nama_role', $a['role'])->value('id_role'),
                    'nama_pengguna' => $a['nama'],
                    'password_hash' => Hash::make(env($a['env'], 'ganti-password-ini')),
                    'aktif' => true,
                ]
            );
        }
    }
}