<?php

namespace App\Http\Controllers;

use App\Models\JenisVaksin;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** FR-02: master data vaksin (Admin). Data dinonaktifkan, tidak dihapus. */
class JenisVaksinController extends Controller
{
    public function index(): View
    {
        return view('jenis-vaksin.index', ['daftar' => JenisVaksin::orderBy('nama_vaksin')->paginate(15)]);
    }

    public function create(): View
    {
        return view('jenis-vaksin.form', ['item' => new JenisVaksin(['vial_per_kotak' => 1, 'stok_minimum' => 0, 'aktif' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $item = JenisVaksin::create($this->data($request));
        AuditLogger::catat($request->user(), 'jenis_vaksin.tambah', 'jenis_vaksin', $item->id_jenis_vaksin, ['kode' => $item->kode]);

        return redirect()->route('jenis-vaksin.index')->with('sukses', 'Jenis vaksin disimpan.');
    }

    public function edit(JenisVaksin $jenisVaksin): View
    {
        return view('jenis-vaksin.form', ['item' => $jenisVaksin]);
    }

    public function update(Request $request, JenisVaksin $jenisVaksin): RedirectResponse
    {
        $jenisVaksin->update($this->data($request, $jenisVaksin));
        AuditLogger::catat($request->user(), 'jenis_vaksin.ubah', 'jenis_vaksin', $jenisVaksin->id_jenis_vaksin, ['kode' => $jenisVaksin->kode]);

        return redirect()->route('jenis-vaksin.index')->with('sukses', 'Jenis vaksin diperbarui.');
    }

    private function data(Request $request, ?JenisVaksin $ada = null): array
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('jenis_vaksin', 'kode')->ignore($ada?->id_jenis_vaksin, 'id_jenis_vaksin')],
            'nama_vaksin' => ['required', 'string', 'max:150'],
            'nama_generik' => ['nullable', 'string', 'max:150'],
            'produsen' => ['nullable', 'string', 'max:150'],
            'bentuk_sediaan' => ['nullable', 'string', 'max:50'],
            'dosis_per_vial' => ['nullable', 'string', 'max:50'],
            'vial_per_kotak' => ['required', 'integer', 'min:1', 'max:65535'],
            'suhu_simpan' => ['nullable', 'string', 'max:50'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
        ]);

        $data['satuan'] = 'vial'; // BR-09: stok dicatat per vial.
        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }
}