<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    protected $table = 'nota';
    protected $primaryKey = 'id_nota';
    public $timestamps = false;
    protected $guarded = [];

    protected function casts(): array
    {
        return ['tanggal_terbit' => 'datetime'];
    }

    public function distribusi(): BelongsTo
    {
        return $this->belongsTo(Distribusi::class, 'id_distribusi', 'id_distribusi');
    }
}