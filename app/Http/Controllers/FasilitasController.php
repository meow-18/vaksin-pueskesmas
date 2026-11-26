<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Pendaftaran fasilitas penerima oleh Admin (mitra yang memiliki kerja sama). */
class FasilitasController extends Controller
{
    public function index(): View
    {
        return view('fasilitas.index', ['daftar' => Fasilitas::orderBy('nama_fasilitas')->paginate(15)]);
    }

    public function create(): View
    {
        return view('fasilitas.form', ['item' => new Fasilitas(['aktif' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $item = Fasilitas::create($this->data($request));
        AuditLogger::catat($request->user(), 'fasilitas.tambah', 'fasilitas', $item->id_fasilitas, ['nama' => $item->nama_fasilitas]);

        return redirect()->route('fasilitas.index')->with('sukses', 'Fasilitas penerima didaftarkan.');
    }

    public function edit(Fasilitas $fasilitas): View
    {
        return view('fasilitas.form', ['item' => $fasilitas]);
    }

    public function update(Request $request, Fasilitas $fasilitas): RedirectResponse
    {
        $fasilitas->update($this->data($request));
        AuditLogger::catat($request->user(), 'fasilitas.ubah', 'fasilitas', $fasilitas->id_fasilitas, ['nama' => $fasilitas->nama_fasilitas]);

        return redirect()->route('fasilitas.index')->with('sukses', 'Fasilitas diperbarui.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'nama_fasilitas' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:Rumah Sakit,Klinik'],
            'alamat' => ['required', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:50'],
        ]);
        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }
}