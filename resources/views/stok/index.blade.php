@extends('layouts.app')
@section('title', 'Stok per Batch')
@section('heading', 'Stok per Batch')

@section('content')
<form method="GET" class="mb-4 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-4">
    <x-select name="id_jenis_vaksin" label="Jenis vaksin" :options="$vaksin->pluck('nama_vaksin', 'id_jenis_vaksin')" :value="request('id_jenis_vaksin')" placeholder="Semua" />
    <x-input name="nomor_batch" label="Nomor batch" :value="request('nomor_batch')" />
    <label class="flex items-end gap-2 pb-2 text-sm"><input type="checkbox" name="semua" value="1" @checked(request()->boolean('semua'))> Tampilkan juga batch habis/kedaluwarsa</label>
    <div class="flex items-end"><button class="rounded-md bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-900">Terapkan</button></div>
</form>

<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr>
            <th class="px-4 py-2">Vaksin</th><th class="px-4 py-2">Batch</th><th class="px-4 py-2">Kedaluwarsa</th>
            <th class="px-4 py-2 text-right">Stok (vial)</th><th class="px-4 py-2">Akumulasi</th><th class="px-4 py-2">Status</th><th class="px-4 py-2"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($daftar as $b)
            <tr>
                <td class="px-4 py-2">{{ $b->jenisVaksin->nama_vaksin }}</td>
                <td class="px-4 py-2 font-medium">{{ $b->nomor_batch }}</td>
                <td class="px-4 py-2">{{ $b->tanggal_kadaluwarsa->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-right">{{ $b->stok_saat_ini }}</td>
                <td class="px-4 py-2 text-slate-500">{{ $b->ringkasKotak() }}</td>
                <td class="px-4 py-2"><x-badge :status="$b->hitungStatus()" /></td>
                <td class="px-4 py-2 text-right"><a class="text-sky-700 hover:underline" href="{{ route('penyesuaian.create', ['id_batch' => $b->id_batch]) }}">Sesuaikan</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Tidak ada batch.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $daftar->links() }}</div>
@endsection