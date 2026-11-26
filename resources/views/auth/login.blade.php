<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · {{ config('vaksin.nama_puskesmas') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 p-4 text-slate-800 antialiased">
<div class="w-full max-w-sm rounded-xl bg-white p-6 shadow">
    <p class="text-xs uppercase tracking-wide text-slate-500">Aplikasi Distribusi Vaksin</p>
    <h1 class="mt-1 text-lg font-semibold">{{ config('vaksin.nama_puskesmas') }}</h1>

    <form method="POST" action="{{ route('login.proses') }}" class="mt-5 space-y-4">
        @csrf
        <x-input name="username" label="Username" autofocus autocomplete="username" required />
        <x-input name="password" label="Kata sandi" type="password" autocomplete="current-password" required />
        <button class="w-full rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Masuk</button>
    </form>
</div>
</body>
</html>