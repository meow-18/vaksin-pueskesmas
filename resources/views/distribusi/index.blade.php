@extends('layouts.app')
@section('title', 'Riwayat Distribusi')
@section('heading', 'Riwayat Distribusi')

@section('content')
<form method="GET" class="mb-4 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-3 lg:grid-cols-6">
    <x-input name="dari" label="Dari" type="date" :value="request('dari')" />
    <x-input name="sampai" label="Sampai" type="date" :value="request('sampai')" />
    <x-select name="id_fasilitas" label="Fasilitas" :options="$fasilitas->pluck('nama_fasilitas', 'id_fasilitas')" :value="request('id_fasilitas')" placeholder="Semua" />
    <x-select name="id_jenis_vaksin" label="Vaksin" :options="$vaksin->pluck('nama_vaksin', 'id_jenis_vaksin')" :value="request('id_jenis_vaksin')" placeholder="Semua" />
    <x-input name="nomor_batch" label="Nomor batch" :value="request('nomor_batch')" />
    <x-select name="status" label="Status" :options="['Draft' => 'Draft', 'Final' => 'Final', 'Dibatalkan' => 'Dibatalkan']" :value="request('status')" placeholder="Semua" />
    <div class="flex items-end gap-2 sm:col-span-3 lg:col-span-6">
        <button class="rounded-md bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-900">Terapkan</button>
        <a href="{{ route('distribusi.laporan', request()->query()) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Unduh laporan PDF</a>
        <a href="{{ route('distribusi.create') }}" class="ml-auto rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Distribusi baru</a>
    </div>
</form>

<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Tanggal</th><th class="px-4 py-2">No. nota</th><th class="px-4 py-2">Fasilitas</th><th class="px-4 py-2">Pengambil</th><th class="px-4 py-2">Status</th><th class="px-4 py-2"></th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($daftar as $d)
            <tr>
                <td class="whitespace-nowrap px-4 py-2">{{ $d->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-2">{{ $d->nota?->no_nota ?? '-' }}</td>
                <td class="px-4 py-2">{{ $d->fasilitas->nama_fasilitas }}</td>
                <td class="px-4 py-2">{{ $d->nama_pengambil }}</td>
                <td class="px-4 py-2"><x-badge :status="$d->status" /></td>
                <td class="px-4 py-2 text-right"><a class="text-sky-700 hover:underline" href="{{ route('distribusi.show', $d) }}">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Tidak ada transaksi.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $daftar->links() }}</div>
@endsection