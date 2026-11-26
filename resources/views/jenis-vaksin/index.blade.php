@extends('layouts.app')
@section('title', 'Jenis Vaksin')
@section('heading', 'Jenis Vaksin')

@section('content')
<div class="mb-4"><a href="{{ route('jenis-vaksin.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Tambah jenis vaksin</a></div>
<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr>
            <th class="px-4 py-2">Kode</th><th class="px-4 py-2">Nama</th><th class="px-4 py-2">Produsen</th>
            <th class="px-4 py-2 text-right">Vial/kotak</th><th class="px-4 py-2 text-right">Stok minimum</th><th class="px-4 py-2">Status</th><th class="px-4 py-2"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($daftar as $v)
            <tr>
                <td class="px-4 py-2">{{ $v->kode }}</td>
                <td class="px-4 py-2">{{ $v->nama_vaksin }}</td>
                <td class="px-4 py-2">{{ $v->produsen ?? '-' }}</td>
                <td class="px-4 py-2 text-right">{{ $v->vial_per_kotak }}</td>
                <td class="px-4 py-2 text-right">{{ $v->stok_minimum }}</td>
                <td class="px-4 py-2"><x-badge :status="$v->aktif ? 'Aktif' : 'Nonaktif'" /></td>
                <td class="px-4 py-2 text-right"><a class="text-sky-700 hover:underline" href="{{ route('jenis-vaksin.edit', $v) }}">Ubah</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada jenis vaksin.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $daftar->links() }}</div>
@endsection