@extends('layouts.app')
@section('title', 'Penyesuaian Stok')
@section('heading', 'Penyesuaian Stok / Stock Opname')

@section('content')
<form method="POST" action="{{ route('penyesuaian.store') }}" class="mb-6 max-w-2xl space-y-4 rounded-lg border border-slate-200 bg-white p-5">
    @csrf
    <x-select name="id_batch" label="Batch" :options="$batches->mapWithKeys(fn ($b) => [$b->id_batch => $b->jenisVaksin->nama_vaksin . ' · ' . $b->nomor_batch . ' (stok ' . $b->stok_saat_ini . ' vial)'])" :value="$terpilih" placeholder="Pilih batch" required />
    <x-input name="stok_sesudah" label="Stok fisik hasil hitung (vial)" type="number" min="0" required hint="Stok sistem akan diganti dengan angka ini; perubahan dan alasan tercatat." />
    <x-input name="alasan" label="Alasan penyesuaian" required placeholder="mis. hasil stock opname, vial rusak" />
    <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Simpan penyesuaian</button>
</form>

<h2 class="mb-2 text-sm font-semibold">Penyesuaian terakhir</h2>
<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Waktu</th><th class="px-4 py-2">Batch</th><th class="px-4 py-2 text-right">Sebelum</th><th class="px-4 py-2 text-right">Sesudah</th><th class="px-4 py-2">Alasan</th><th class="px-4 py-2">Petugas</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($riwayat as $r)
            <tr>
                <td class="whitespace-nowrap px-4 py-2">{{ $r->tanggal->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-2">{{ $r->batch->jenisVaksin->nama_vaksin }} · {{ $r->batch->nomor_batch }}</td>
                <td class="px-4 py-2 text-right">{{ $r->stok_sebelum }}</td><td class="px-4 py-2 text-right">{{ $r->stok_sesudah }}</td>
                <td class="px-4 py-2">{{ $r->alasan }}</td><td class="px-4 py-2">{{ $r->pengguna->nama_pengguna }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada penyesuaian.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection