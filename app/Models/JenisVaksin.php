<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisVaksin extends Model
{
    protected $table = 'jenis_vaksin';
    protected $primaryKey = 'id_jenis_vaksin';
    public $timestamps = false;
    protected $guarded = [];

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'vial_per_kotak' => 'integer', 'stok_minimum' => 'integer'];
    }

    public function batch(): HasMany
    {
        return $this->hasMany(Batch::class, 'id_jenis_vaksin', 'id_jenis_vaksin');
    }
}