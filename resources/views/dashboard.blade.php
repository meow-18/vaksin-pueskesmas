@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($kartu as $k)
        <div class="rounded-lg border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-500">{{ $k['label'] }}</p>
            <p class="mt-1 text-2xl font-semibold">{{ $k['nilai'] }}</p>
        </div>
    @endforeach
</div>

@unless ($admin)
<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="rounded-lg border border-slate-200 bg-white">
        <h2 class="border-b border-slate-200 px-4 py-3 text-sm font-semibold">Batch mendekati kedaluwarsa</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Vaksin</th><th class="px-4 py-2">Batch</th><th class="px-4 py-2">Kedaluwarsa</th><th class="px-4 py-2 text-right">Stok (vial)</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($perhatian as $b)
                    <tr><td class="px-4 py-2">{{ $b->jenisVaksin->nama_vaksin }}</td><td class="px-4 py-2">{{ $b->nomor_batch }}</td><td class="px-4 py-2">{{ $b->tanggal_kadaluwarsa->format('d/m/Y') }}</td><td class="px-4 py-2 text-right">{{ $b->stok_saat_ini }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Tidak ada batch yang mendekati kedaluwarsa.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
            <h2 class="text-sm font-semibold">Transaksi terakhir</h2>
            <a href="{{ route('distribusi.create') }}" class="rounded-md bg-sky-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-sky-700">Distribusi baru</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                @forelse ($terakhir as $d)
                    <tr>
                        <td class="px-4 py-2"><a class="text-sky-700 hover:underline" href="{{ route('distribusi.show', $d) }}">{{ $d->nota?->no_nota ?? 'Draft #' . $d->id_distribusi }}</a></td>
                        <td class="px-4 py-2">{{ $d->fasilitas->nama_fasilitas }}</td>
                        <td class="px-4 py-2"><x-badge :status="$d->status" /></td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-center text-slate-500">Belum ada transaksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endunless
@endsection