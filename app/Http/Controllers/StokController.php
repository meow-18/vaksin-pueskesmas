<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\JenisVaksin;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** FR-04: stok per jenis vaksin dan per batch. */
class StokController extends Controller
{
    public function index(Request $request): View
    {
        $daftar = Batch::with('jenisVaksin')
            ->when($request->filled('id_jenis_vaksin'), fn ($q) => $q->where('id_jenis_vaksin', $request->integer('id_jenis_vaksin')))
            ->when($request->filled('nomor_batch'), fn ($q) => $q->where('nomor_batch', 'like', '%' . $request->string('nomor_batch') . '%'))
            ->when(! $request->boolean('semua'), fn ($q) => $q->where(fn ($w) => $w->where('stok_saat_ini', '>', 0)->orWhereDate('tanggal_kadaluwarsa', '>=', today()->toDateString())))
            ->fefo()
            ->paginate(20)
            ->withQueryString();

        return view('stok.index', [
            'daftar' => $daftar,
            'vaksin' => JenisVaksin::orderBy('nama_vaksin')->get(),
        ]);
    }
}