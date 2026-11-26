<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Role;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** FR-01 / FR-13: kelola pengguna dan peran (Admin). Tidak ada akun bersama. */
class PenggunaController extends Controller
{
    public function index(): View
    {
        return view('pengguna.index', ['daftar' => Pengguna::with('role')->orderBy('nama_pengguna')->paginate(15)]);
    }

    public function create(): View
    {
        return view('pengguna.form', ['item' => new Pengguna(['aktif' => true]), 'roles' => Role::orderBy('id_role')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:pengguna,username'],
            'id_role' => ['required', 'exists:role,id_role'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $item = Pengguna::create([
            'nama_pengguna' => $data['nama_pengguna'],
            'username' => $data['username'],
            'id_role' => $data['id_role'],
            'password_hash' => Hash::make($data['password']),
            'aktif' => $request->boolean('aktif'),
        ]);
        AuditLogger::catat($request->user(), 'pengguna.tambah', 'pengguna', $item->id_pengguna, ['username' => $item->username]);

        return redirect()->route('pengguna.index')->with('sukses', 'Pengguna ditambahkan.');
    }

    public function edit(Pengguna $pengguna): View
    {
        return view('pengguna.form', ['item' => $pengguna, 'roles' => Role::orderBy('id_role')->get()]);
    }

    public function update(Request $request, Pengguna $pengguna): RedirectResponse
    {
        $data = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('pengguna', 'username')->ignore($pengguna->id_pengguna, 'id_pengguna')],
            'id_role' => ['required', 'exists:role,id_role'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $aktif = $request->boolean('aktif');
        if ($pengguna->id_pengguna === $request->user()->id_pengguna && (! $aktif || (int) $data['id_role'] !== (int) $pengguna->id_role)) {
            throw ValidationException::withMessages(['aktif' => 'Anda tidak dapat menonaktifkan atau mengubah peran akun Anda sendiri.']);
        }

        $pengguna->fill([
            'nama_pengguna' => $data['nama_pengguna'],
            'username' => $data['username'],
            'id_role' => $data['id_role'],
            'aktif' => $aktif,
        ]);
        if (! empty($data['password'])) {
            $pengguna->password_hash = Hash::make($data['password']);
        }
        $pengguna->save();

        AuditLogger::catat($request->user(), 'pengguna.ubah', 'pengguna', $pengguna->id_pengguna, ['username' => $pengguna->username]);

        return redirect()->route('pengguna.index')->with('sukses', 'Pengguna diperbarui.');
    }
}