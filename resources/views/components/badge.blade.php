@props(['status'])
@php
    $warna = [
        'Tersedia' => 'bg-emerald-100 text-emerald-800',
        'Terbatas' => 'bg-amber-100 text-amber-800',
        'Habis' => 'bg-slate-200 text-slate-700',
        'Kadaluwarsa' => 'bg-red-100 text-red-800',
        'Draft' => 'bg-amber-100 text-amber-800',
        'Final' => 'bg-emerald-100 text-emerald-800',
        'Dibatalkan' => 'bg-red-100 text-red-800',
        'Aktif' => 'bg-emerald-100 text-emerald-800',
        'Nonaktif' => 'bg-slate-200 text-slate-700',
    ][$status] ?? 'bg-slate-100 text-slate-700';
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $warna }}">{{ $status }}</span>