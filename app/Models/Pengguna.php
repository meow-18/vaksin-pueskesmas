<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = ['password_hash'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'created_at' => 'datetime'];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    // Tabel tidak memiliki kolom remember_token.
    public function getRememberTokenName()
    {
        return '';
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function adalahAdmin(): bool
    {
        return $this->role?->nama_role === Role::ADMIN;
    }

    public function adalahApoteker(): bool
    {
        return $this->role?->nama_role === Role::APOTEKER;
    }
}