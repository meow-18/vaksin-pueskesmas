@extends('layouts.app')
@section('title', $item->exists ? 'Ubah Jenis Vaksin' : 'Tambah Jenis Vaksin')
@section('heading', $item->exists ? 'Ubah Jenis Vaksin' : 'Tambah Jenis Vaksin')

@section('content')
<form method="POST" action="{{ $item->exists ? route('jenis-vaksin.update', $item) : route('jenis-vaksin.store') }}" class="max-w-3xl space-y-4 rounded-lg border border-slate-200 bg-white p-5">
    @csrf
    @if ($item->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <x-input name="kode" label="Kode" :value="$item->kode" required />
        <x-input name="nama_vaksin" label="Nama vaksin" :value="$item->nama_vaksin" required />
        <x-input name="nama_generik" label="Nama generik / merek" :value="$item->nama_generik" />
        <x-input name="produsen" label="Produsen" :value="$item->produsen" />
        <x-input name="bentuk_sediaan" label="Bentuk sediaan" :value="$item->bentuk_sediaan" />
        <x-input name="dosis_per_vial" label="Dosis per vial" :value="$item->dosis_per_vial" />
        <x-input name="vial_per_kotak" label="Jumlah vial per kotak" type="number" min="1" :value="$item->vial_per_kotak" required hint="Dipakai untuk akumulasi kotak. Stok dicatat per vial." />
        <x-input name="stok_minimum" label="Stok minimum (vial)" type="number" min="0" :value="$item->stok_minimum" required hint="Batch berstatus Terbatas jika stok ≤ nilai ini." />
        <x-input name="suhu_simpan" label="Suhu penyimpanan" :value="$item->suhu_simpan" />
    </div>

    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="aktif" value="1" @checked(old('aktif', $item->aktif))> Aktif</label>

    <div class="flex gap-2">
        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Simpan</button>
        <a href="{{ route('jenis-vaksin.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Batal</a>
    </div>
</form>
@endsection