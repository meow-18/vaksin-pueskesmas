@extends('layouts.app')
@section('title', 'Catat Penerimaan')
@section('heading', 'Catat Penerimaan Vaksin')

@section('content')
@php $rows = old('items', [[]]); @endphp
<form method="POST" action="{{ route('penerimaan.store') }}" class="space-y-5">
    @csrf
    <section class="rounded-lg border border-slate-200 bg-white p-5">
        <h2 class="mb-3 text-sm font-semibold">Data pengiriman</h2>
        <div class="grid gap-4 sm:grid-cols-3">
            <x-input name="tanggal_penerimaan" label="Tanggal penerimaan" type="date" :value="now()->toDateString()" required />
            <x-input name="sumber_penerimaan" label="Sumber" required />
            <x-input name="no_dokumen_pengiriman" label="Nomor dokumen pengiriman" required />
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5">
        <h2 class="mb-1 text-sm font-semibold">Batch yang diterima</h2>
        <p class="mb-3 text-xs text-slate-500">Satu pengiriman dapat memuat beberapa batch. Nomor batch harus unik per jenis vaksin dan tanggal kedaluwarsa harus setelah hari ini.</p>
        <div id="daftar" class="space-y-3">
            @foreach ($rows as $i => $row)
                @include('partials.penerimaan-baris', ['i' => $i, 'row' => $row, 'vaksin' => $vaksin])
            @endforeach
        </div>
        <button type="button" id="tambah" class="mt-3 rounded-md border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100">+ Tambah batch</button>
    </section>

    <div class="flex gap-2">
        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Simpan penerimaan</button>
        <a href="{{ route('penerimaan.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Batal</a>
    </div>
</form>

<template id="templat">
    @include('partials.penerimaan-baris', ['i' => '__i__', 'row' => [], 'vaksin' => $vaksin])
</template>
@endsection

@push('scripts')
<script>
    (function () {
        const daftar = document.getElementById('daftar');
        let indeks = daftar.querySelectorAll('[data-baris]').length + 100;
        document.getElementById('tambah').addEventListener('click', function () {
            const html = document.getElementById('templat').innerHTML.replaceAll('__i__', indeks++);
            daftar.insertAdjacentHTML('beforeend', html);
        });
        daftar.addEventListener('click', function (e) {
            if (e.target.matches('[data-hapus]') && daftar.querySelectorAll('[data-baris]').length > 1) {
                e.target.closest('[data-baris]').remove();
            }
        });
    })();
</script>
@endpush