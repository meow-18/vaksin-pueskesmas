<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Distribusi Vaksin') · {{ config('vaksin.nama_puskesmas') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
@php
    $user = auth()->user();
    $menu = $user->adalahAdmin()
        ? [
            ['Dashboard', 'dashboard', 'dashboard'],
            ['Jenis Vaksin', 'jenis-vaksin.index', 'jenis-vaksin.*'],
            ['Fasilitas Penerima', 'fasilitas.index', 'fasilitas.*'],
            ['Pengguna', 'pengguna.index', 'pengguna.*'],
            ['Audit Log', 'audit-log.index', 'audit-log.*'],
        ]
        : [
            ['Dashboard', 'dashboard', 'dashboard'],
            ['Stok per Batch', 'stok.index', 'stok.*'],
            ['Penerimaan Vaksin', 'penerimaan.index', 'penerimaan.*'],
            ['Distribusi Baru', 'distribusi.create', 'distribusi.create'],
            ['Riwayat Distribusi', 'distribusi.index', ['distribusi.index', 'distribusi.show']],
            ['Penyesuaian Stok', 'penyesuaian.create', 'penyesuaian.*'],
        ];
@endphp
<div class="min-h-screen lg:flex">
    <aside id="sidebar" class="hidden shrink-0 bg-slate-900 text-slate-200 lg:block lg:w-64">
        <div class="px-5 py-5">
            <p class="text-xs uppercase tracking-wide text-slate-400">Distribusi Vaksin</p>
            <p class="mt-1 text-sm font-semibold leading-snug text-white">{{ config('vaksin.nama_puskesmas') }}</p>
        </div>
        <nav class="space-y-1 px-3 pb-6">
            @foreach ($menu as [$teks, $rute, $pola])
                <a href="{{ route($rute) }}"
                   class="block rounded-md px-3 py-2 text-sm {{ request()->routeIs($pola) ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">{{ $teks }}</a>
            @endforeach
        </nav>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3">
            <button type="button" class="rounded-md border border-slate-300 px-3 py-1.5 text-sm lg:hidden"
                    onclick="document.getElementById('sidebar').classList.toggle('hidden')">Menu</button>
            <div class="hidden lg:block"></div>
            <div class="flex items-center gap-3 text-sm">
                <div class="text-right">
                    <p class="font-medium leading-tight">{{ $user->nama_pengguna }}</p>
                    <p class="text-xs text-slate-500">{{ $user->role->nama_role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="rounded-md border border-slate-300 px-3 py-1.5 hover:bg-slate-100">Keluar</button>
                </form>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 p-4 sm:p-6">
            <h1 class="mb-4 text-xl font-semibold">@yield('heading')</h1>

            @if (session('sukses'))
                <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('sukses') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-medium">Periksa kembali isian Anda:</p>
                    <ul class="mt-1 list-disc pl-5">
                        @foreach (collect($errors->all())->unique() as $pesan)<li>{{ $pesan }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
