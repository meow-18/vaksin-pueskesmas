@extends('layouts.app')
@section('title', 'Distribusi Baru')
@section('heading', 'Distribusi Baru')

@section('content')
@php $rows = old('items', [[]]); @endphp
<form method="POST" action="{{ route('distribusi.store') }}" class="space-y-5">
    @csrf
    <section class="rounded-lg border border-slate-200 bg-white p-5">
        <h2 class="mb-3 text-sm font-semibold">Penerima</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-select name="id_fasilitas" label="Fasilitas penerima" :options="$fasilitas->pluck('nama_fasilitas', 'id_fasilitas')" placeholder="Pilih fasilitas terdaftar" required />
            <x-input name="nama_pengambil" label="Nama pengambil" required />
        </div>
        @if ($fasilitas->isEmpty())
            <p class="mt-3 text-sm text-amber-700">Belum ada fasilitas aktif. Minta Admin mendaftarkan fasilitas penerima terlebih dahulu.</p>
        @endif
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5">
        <h2 class="mb-1 text-sm font-semibold">Vaksin yang diminta</h2>
        <p class="mb-3 text-xs text-slate-500">Sistem memeriksa stok dan menyarankan batch berdasarkan FEFO. Stok baru berkurang setelah verifikasi batch dan finalisasi.</p>
        <div id="daftar" class="space-y-3">
            @foreach ($rows as $i => $row)
                @include('partials.distribusi-baris', ['i' => $i, 'row' => $row, 'vaksin' => $vaksin])
            @endforeach
        </div>
        <button type="button" id="tambah" class="mt-3 rounded-md border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100">+ Tambah vaksin</button>
    </section>

    <div class="flex gap-2">
        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Periksa stok dan buat draft</button>
        <a href="{{ route('distribusi.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Batal</a>
    </div>
</form>

<template id="templat">
    @include('partials.distribusi-baris', ['i' => '__i__', 'row' => [], 'vaksin' => $vaksin])
</template>
@endsection

@push('scripts')
<script>
    (function () {
        const daftar = document.getElementById('daftar');
        let indeks = daftar.querySelectorAll('[data-baris]').length + 100;
        document.getElementById('tambah').addEventListener('click', function () {
            daftar.insertAdjacentHTML('beforeend', document.getElementById('templat').innerHTML.replaceAll('__i__', indeks++));
        });
        daftar.addEventListener('click', function (e) {
            if (e.target.matches('[data-hapus]') && daftar.querySelectorAll('[data-baris]').length > 1) {
                e.target.closest('[data-baris]').remove();
            }
        });
    })();
</script>
@endpush