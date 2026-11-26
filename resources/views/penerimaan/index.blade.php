@extends('layouts.app')
@section('title', 'Penerimaan Vaksin')
@section('heading', 'Penerimaan Vaksin')

@section('content')
<div class="mb-4"><a href="{{ route('penerimaan.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Catat penerimaan</a></div>
<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Tanggal</th><th class="px-4 py-2">Sumber</th><th class="px-4 py-2">No. dokumen</th><th class="px-4 py-2 text-right">Batch</th><th class="px-4 py-2">Petugas</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($daftar as $p)
            <tr>
                <td class="px-4 py-2">{{ $p->tanggal_penerimaan->format('d/m/Y') }}</td><td class="px-4 py-2">{{ $p->sumber_penerimaan }}</td>
                <td class="px-4 py-2">{{ $p->no_dokumen_pengiriman }}</td><td class="px-4 py-2 text-right">{{ $p->batch_count }}</td><td class="px-4 py-2">{{ $p->pengguna->nama_pengguna }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada penerimaan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $daftar->links() }}</div>
@endsection