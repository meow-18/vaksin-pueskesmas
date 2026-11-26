<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Distribusi extends Model
{
    public const DRAFT = 'Draft';
    public const FINAL = 'Final';
    public const DIBATALKAN = 'Dibatalkan';

    protected $table = 'distribusi';
    protected $primaryKey = 'id_distribusi';
    public $timestamps = false;
    protected $guarded = [];

    protected function casts(): array
    {
        return ['tanggal_transaksi' => 'datetime', 'waktu_batal' => 'datetime'];
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function pembatal(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna_pembatal', 'id_pengguna');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailDistribusi::class, 'id_distribusi', 'id_distribusi');
    }

    public function nota(): HasOne
    {
        return $this->hasOne(Nota::class, 'id_distribusi', 'id_distribusi');
    }
}