@extends('layouts.app')
@section('title', 'Pengguna')
@section('heading', 'Pengguna dan Hak Akses')

@section('content')
<div class="mb-4"><a href="{{ route('pengguna.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Tambah pengguna</a></div>
<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Nama</th><th class="px-4 py-2">Username</th><th class="px-4 py-2">Peran</th><th class="px-4 py-2">Status</th><th class="px-4 py-2"></th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @foreach ($daftar as $p)
            <tr>
                <td class="px-4 py-2">{{ $p->nama_pengguna }}</td><td class="px-4 py-2">{{ $p->username }}</td><td class="px-4 py-2">{{ $p->role->nama_role }}</td>
                <td class="px-4 py-2"><x-badge :status="$p->aktif ? 'Aktif' : 'Nonaktif'" /></td>
                <td class="px-4 py-2 text-right"><a class="text-sky-700 hover:underline" href="{{ route('pengguna.edit', $p) }}">Ubah</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $daftar->links() }}</div>
@endsection