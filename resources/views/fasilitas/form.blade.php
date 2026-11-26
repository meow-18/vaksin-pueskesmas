@extends('layouts.app')
@section('title', $item->exists ? 'Ubah Fasilitas' : 'Daftarkan Fasilitas')
@section('heading', $item->exists ? 'Ubah Fasilitas' : 'Daftarkan Fasilitas Penerima')

@section('content')
<form method="POST" action="{{ $item->exists ? route('fasilitas.update', $item) : route('fasilitas.store') }}" class="max-w-2xl space-y-4 rounded-lg border border-slate-200 bg-white p-5">
    @csrf
    @if ($item->exists) @method('PUT') @endif

    <x-input name="nama_fasilitas" label="Nama fasilitas" :value="$item->nama_fasilitas" required />
    <x-select name="jenis" label="Jenis" :options="['Rumah Sakit' => 'Rumah Sakit', 'Klinik' => 'Klinik']" :value="$item->jenis" placeholder="Pilih jenis" required />
    <x-input name="alamat" label="Alamat" :value="$item->alamat" required />
    <x-input name="kontak" label="Kontak" :value="$item->kontak" />
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="aktif" value="1" @checked(old('aktif', $item->aktif))> Aktif (dapat dipilih pada transaksi)</label>

    <div class="flex gap-2">
        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Simpan</button>
        <a href="{{ route('fasilitas.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Batal</a>
    </div>
</form>
@endsection