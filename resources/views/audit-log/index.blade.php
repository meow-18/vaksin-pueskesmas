@extends('layouts.app')
@section('title', 'Audit Log')
@section('heading', 'Audit Log')

@section('content')
<form method="GET" class="mb-4 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-4">
    <x-input name="aktivitas" label="Aktivitas diawali" :value="request('aktivitas')" placeholder="mis. distribusi" />
    <x-input name="dari" label="Dari tanggal" type="date" :value="request('dari')" />
    <x-input name="sampai" label="Sampai tanggal" type="date" :value="request('sampai')" />
    <div class="flex items-end"><button class="rounded-md bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-900">Terapkan</button></div>
</form>
<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Waktu</th><th class="px-4 py-2">Pengguna</th><th class="px-4 py-2">Aktivitas</th><th class="px-4 py-2">Objek</th><th class="px-4 py-2">Detail</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($daftar as $l)
            <tr>
                <td class="whitespace-nowrap px-4 py-2">{{ $l->waktu->format('d/m/Y H:i:s') }}</td>
                <td class="px-4 py-2">{{ $l->pengguna?->username ?? '-' }}</td>
                <td class="px-4 py-2">{{ $l->aktivitas }}</td>
                <td class="px-4 py-2">{{ $l->objek }} {{ $l->id_objek ? '#' . $l->id_objek : '' }}</td>
                <td class="max-w-xs truncate px-4 py-2 text-slate-500" title="{{ $l->detail }}">{{ $l->detail }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Tidak ada catatan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $daftar->links() }}</div>
@endsection