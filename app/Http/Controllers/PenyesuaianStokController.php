<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\PenyesuaianStok;
use App\Services\PenyesuaianStokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** FR-11: penyesuaian stok / stock opname oleh Apoteker. */
class PenyesuaianStokController extends Controller
{
    public function create(Request $request): View
    {
        return view('penyesuaian.create', [
            'batches' => Batch::with('jenisVaksin')->fefo()->get(),
            'terpilih' => $request->integer('id_batch') ?: null,
            'riwayat' => PenyesuaianStok::with(['batch.jenisVaksin', 'pengguna'])->orderByDesc('id_penyesuaian_stok')->take(15)->get(),
        ]);
    }

    public function store(Request $request, PenyesuaianStokService $service): RedirectResponse
    {
        $data = $request->validate([
            'id_batch' => ['required', 'integer', 'exists:batch,id_batch'],
            'stok_sesudah' => ['required', 'integer', 'min:0'],
            'alasan' => ['required', 'string', 'max:255'],
        ]);

        $service->sesuaikan($request->user(), (int) $data['id_batch'], (int) $data['stok_sesudah'], $data['alasan']);

        return redirect()->route('penyesuaian.create')->with('sukses', 'Stok disesuaikan dan tercatat di audit log.');
    }
}