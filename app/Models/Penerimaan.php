<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $primaryKey = 'id_penerimaan';
    public $timestamps = false;
    protected $guarded = [];

    protected function casts(): array
    {
        return ['tanggal_penerimaan' => 'date', 'created_at' => 'datetime'];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function batch(): HasMany
    {
        return $this->hasMany(Batch::class, 'id_penerimaan', 'id_penerimaan');
    }
}