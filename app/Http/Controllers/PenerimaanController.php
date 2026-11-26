<?php

namespace App\Http\Controllers;

use App\Models\JenisVaksin;
use App\Models\Penerimaan;
use App\Services\PenerimaanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** FR-03: penerimaan vaksin per batch. */
class PenerimaanController extends Controller
{
    public function index(): View
    {
        $daftar = Penerimaan::with(['pengguna'])->withCount('batch')->orderByDesc('id_penerimaan')->paginate(15);

        return view('penerimaan.index', compact('daftar'));
    }

    public function create(): View
    {
        return view('penerimaan.create', [
            'vaksin' => JenisVaksin::where('aktif', true)->orderBy('nama_vaksin')->get(),
        ]);
    }

    public function store(Request $request, PenerimaanService $service): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date', 'before_or_equal:today'],
            'sumber_penerimaan' => ['required', 'string', 'max:150'],
            'no_dokumen_pengiriman' => ['required', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_jenis_vaksin' => ['required', 'integer', 'exists:jenis_vaksin,id_jenis_vaksin'],
            'items.*.nomor_batch' => ['required', 'string', 'max:50'],
            'items.*.tanggal_kadaluwarsa' => ['required', 'date', 'after:today'],
            'items.*.jumlah_diterima' => ['required', 'integer', 'min:1'],
        ]);

        // Nomor batch ganda di dalam satu formulir juga ditolak.
        $terlihat = [];
        foreach ($data['items'] as $i => $item) {
            $kunci = $item['id_jenis_vaksin'] . '|' . mb_strtolower(trim($item['nomor_batch']));
            if (isset($terlihat[$kunci])) {
                throw ValidationException::withMessages(["items.{$i}.nomor_batch" => 'Nomor batch ganda pada formulir yang sama.']);
            }
            $terlihat[$kunci] = true;
            $data['items'][$i]['nomor_batch'] = trim($item['nomor_batch']);
        }

        $service->simpan($request->user(), $data, $data['items']);

        return redirect()->route('stok.index')->with('sukses', 'Penerimaan vaksin tersimpan dan stok diperbarui.');
    }
}