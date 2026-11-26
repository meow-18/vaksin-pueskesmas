<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Distribusi;
use App\Models\Fasilitas;
use App\Models\JenisVaksin;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->adalahAdmin()) {
            return view('dashboard', [
                'admin' => true,
                'kartu' => [
                    ['label' => 'Pengguna aktif', 'nilai' => Pengguna::where('aktif', true)->count()],
                    ['label' => 'Jenis vaksin aktif', 'nilai' => JenisVaksin::where('aktif', true)->count()],
                    ['label' => 'Fasilitas penerima aktif', 'nilai' => Fasilitas::where('aktif', true)->count()],
                ],
                'terakhir' => collect(),
                'perhatian' => collect(),
            ]);
        }

        $hari = (int) config('vaksin.hari_peringatan_kedaluwarsa', 30);

        $mendekati = Batch::with('jenisVaksin')
            ->untukDistribusi()
            ->whereDate('tanggal_kadaluwarsa', '<=', today()->addDays($hari)->toDateString())
            ->fefo()
            ->get();

        $terbatas = Batch::with('jenisVaksin')->untukDistribusi()->get()
            ->filter(fn (Batch $b) => $b->hitungStatus() === Batch::TERBATAS);

        return view('dashboard', [
            'admin' => false,
            'kartu' => [
                ['label' => 'Total stok tersedia (vial)', 'nilai' => number_format((int) Batch::untukDistribusi()->sum('stok_saat_ini'), 0, ',', '.')],
                ['label' => "Batch mendekati kedaluwarsa (≤ {$hari} hari)", 'nilai' => $mendekati->count()],
                ['label' => 'Batch berstatus Terbatas', 'nilai' => $terbatas->count()],
                ['label' => 'Transaksi final hari ini', 'nilai' => Distribusi::where('status', Distribusi::FINAL)->whereDate('tanggal_transaksi', today())->count()],
            ],
            'perhatian' => $mendekati->take(8),
            'terakhir' => Distribusi::with(['fasilitas', 'nota'])->latest('id_distribusi')->take(6)->get(),
        ]);
    }
}