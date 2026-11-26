@extends('layouts.app')
@section('title', $item->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')
@section('heading', $item->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('content')
<form method="POST" action="{{ $item->exists ? route('pengguna.update', $item) : route('pengguna.store') }}" class="max-w-xl space-y-4 rounded-lg border border-slate-200 bg-white p-5">
    @csrf
    @if ($item->exists) @method('PUT') @endif

    <x-input name="nama_pengguna" label="Nama lengkap" :value="$item->nama_pengguna" required />
    <x-input name="username" label="Username" :value="$item->username" required autocomplete="off" />
    <x-select name="id_role" label="Peran" :options="$roles->pluck('nama_role', 'id_role')" :value="$item->id_role" placeholder="Pilih peran" required />
    <x-input name="password" label="Kata sandi" type="password" autocomplete="new-password" :required="! $item->exists" :hint="($item->exists ? 'Kosongkan jika tidak diganti. ' : '') . 'Minimal 8 karakter.'" />
    <x-input name="password_confirmation" label="Ulangi kata sandi" type="password" autocomplete="new-password" />
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="aktif" value="1" @checked(old('aktif', $item->aktif))> Akun aktif</label>

    <div class="flex gap-2">
        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Simpan</button>
        <a href="{{ route('pengguna.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Batal</a>
    </div>
</form>
@endsection