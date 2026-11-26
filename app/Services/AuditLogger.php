<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Pengguna;

/** FR-10 / SR-01: mencatat login, perubahan stok, finalisasi, dan koreksi data. */
class AuditLogger
{
    public static function catat(
        ?Pengguna $pengguna,
        string $aktivitas,
        ?string $objek = null,
        ?int $idObjek = null,
        array|string|null $detail = null,
    ): void {
        AuditLog::create([
            'id_pengguna' => $pengguna?->id_pengguna,
            'waktu' => now(),
            'aktivitas' => $aktivitas,
            'objek' => $objek,
            'id_objek' => $idObjek,
            'detail' => is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : $detail,
        ]);
    }
}