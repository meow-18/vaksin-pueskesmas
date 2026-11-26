<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    public const TERSEDIA = 'Tersedia';
    public const TERBATAS = 'Terbatas';
    public const HABIS = 'Habis';
    public const KADALUWARSA = 'Kadaluwarsa';

    protected $table = 'batch';
    protected $primaryKey = 'id_batch';
    public $timestamps = false;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_kadaluwarsa' => 'date',
            'jumlah_diterima' => 'integer',
            'stok_saat_ini' => 'integer',
        ];
    }

    public function jenisVaksin(): BelongsTo
    {
        return $this->belongsTo(JenisVaksin::class, 'id_jenis_vaksin', 'id_jenis_vaksin');
    }

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    /** Batch berlaku sampai akhir tanggal kedaluwarsanya (BR-02). */
    public function kedaluwarsa(): bool
    {
        return $this->tanggal_kadaluwarsa->lt(today());
    }

    /** BR-14: status ditentukan sistem dari stok, stok minimum, dan tanggal kedaluwarsa. */
    public function hitungStatus(): string
    {
        if ($this->kedaluwarsa()) {
            return self::KADALUWARSA;
        }
        if ($this->stok_saat_ini <= 0) {
            return self::HABIS;
        }
        if ($this->stok_saat_ini <= (int) $this->jenisVaksin->stok_minimum) {
            return self::TERBATAS;
        }

        return self::TERSEDIA;
    }

    /** Akumulasi kotak dari stok vial (BR-09), mis. "2 kotak + 3 vial". */
    public function ringkasKotak(): string
    {
        $per = max(1, (int) $this->jenisVaksin->vial_per_kotak);
        $kotak = intdiv($this->stok_saat_ini, $per);
        $sisa = $this->stok_saat_ini % $per;

        return $kotak . ' kotak' . ($sisa > 0 ? " + {$sisa} vial" : '');
    }

    /** Batch yang boleh dipilih pada distribusi: stok ada dan belum kedaluwarsa. */
    public function scopeUntukDistribusi(Builder $query): Builder
    {
        return $query->where('stok_saat_ini', '>', 0)
            ->whereDate('tanggal_kadaluwarsa', '>=', today()->toDateString());
    }

    /** Urutan FEFO: kedaluwarsa terdekat lebih dulu. */
    public function scopeFefo(Builder $query): Builder
    {
        return $query->orderBy('tanggal_kadaluwarsa')->orderBy('id_batch');
    }
}