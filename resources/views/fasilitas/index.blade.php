@extends('layouts.app')
@section('title', 'Fasilitas Penerima')
@section('heading', 'Fasilitas Penerima')

@section('content')
<div class="mb-4"><a href="{{ route('fasilitas.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Daftarkan fasilitas</a></div>
<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Nama</th><th class="px-4 py-2">Jenis</th><th class="px-4 py-2">Alamat</th><th class="px-4 py-2">Kontak</th><th class="px-4 py-2">Status</th><th class="px-4 py-2"></th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($daftar as $f)
            <tr>
                <td class="px-4 py-2">{{ $f->nama_fasilitas }}</td><td class="px-4 py-2">{{ $f->jenis }}</td>
                <td class="px-4 py-2">{{ $f->alamat }}</td><td class="px-4 py-2">{{ $f->kontak ?? '-' }}</td>
                <td class="px-4 py-2"><x-badge :status="$f->aktif ? 'Aktif' : 'Nonaktif'" /></td>
                <td class="px-4 py-2 text-right"><a class="text-sky-700 hover:underline" href="{{ route('fasilitas.edit', $f) }}">Ubah</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada fasilitas terdaftar.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $daftar->links() }}</div>
@endsection