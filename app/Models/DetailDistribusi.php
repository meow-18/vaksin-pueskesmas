<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailDistribusi extends Model
{
    protected $table = 'detail_distribusi';
    protected $primaryKey = 'id_detail_distribusi';
    public $timestamps = false;
    protected $guarded = [];

    protected function casts(): array
    {
        return ['batch_terverifikasi' => 'boolean', 'waktu_terverifikasi' => 'datetime', 'jumlah' => 'integer'];
    }

    public function distribusi(): BelongsTo
    {
        return $this->belongsTo(Distribusi::class, 'id_distribusi', 'id_distribusi');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'id_batch', 'id_batch');
    }
}