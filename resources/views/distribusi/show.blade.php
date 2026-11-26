@extends('layouts.app')
@section('title', 'Detail Distribusi')
@section('heading', 'Detail Distribusi')

@section('content')
<section class="mb-5 rounded-lg border border-slate-200 bg-white p-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">{{ $distribusi->nota?->no_nota ?? 'Draft #' . $distribusi->id_distribusi }}</p>
            <p class="text-lg font-semibold">{{ $distribusi->fasilitas->nama_fasilitas }}</p>
        </div>
        <x-badge :status="$distribusi->status" />
    </div>
    <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
        <div><dt class="text-slate-500">Tanggal</dt><dd>{{ $distribusi->tanggal_transaksi->format('d/m/Y H:i') }}</dd></div>
        <div><dt class="text-slate-500">Nama pengambil</dt><dd>{{ $distribusi->nama_pengambil }}</dd></div>
        <div><dt class="text-slate-500">Petugas</dt><dd>{{ $distribusi->pengguna->nama_pengguna }}</dd></div>
    </dl>
    @if ($distribusi->status === 'Dibatalkan')
        <div class="mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
            Dibatalkan {{ $distribusi->waktu_batal?->format('d/m/Y H:i') }} oleh {{ $distribusi->pembatal?->nama_pengguna }}. Alasan: {{ $distribusi->alasan_batal }}. Stok telah dikembalikan ke batch asal.
        </div>
    @endif
</section>

@if ($distribusi->status === 'Draft')
    <form method="POST" action="{{ route('distribusi.finalisasi', $distribusi) }}" class="space-y-4">
        @csrf
        <section class="rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-semibold">Verifikasi batch</h2>
            <p class="mb-4 text-xs text-slate-500">Cocokkan nomor batch dan tanggal kedaluwarsa di bawah dengan label kemasan fisik, lalu centang. Jika tidak cocok, pilih batch lain atau buang draft.</p>

            <div class="space-y-4">
            @foreach ($distribusi->detail as $det)
                @php $idJenis = $det->batch->id_jenis_vaksin; @endphp
                <div class="rounded-md border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">{{ $det->batch->jenisVaksin->nama_vaksin }} · {{ $det->jumlah }} vial</p>
                    <p class="mt-1 text-2xl font-semibold tracking-wide">{{ $det->batch->nomor_batch }}</p>
                    <p class="text-sm text-slate-600">Kedaluwarsa: <strong>{{ $det->batch->tanggal_kadaluwarsa->format('d/m/Y') }}</strong></p>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Batch yang diambil</label>
                            <select name="detail[{{ $det->id_detail_distribusi }}][id_batch]" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                @foreach ($alternatif[$det->id_detail_distribusi] as $b)
                                    <option value="{{ $b->id_batch }}" @selected($b->id_batch === $det->id_batch)>
                                        {{ $b->nomor_batch }} · exp {{ $b->tanggal_kadaluwarsa->format('d/m/Y') }} · stok {{ $b->stok_saat_ini }}{{ $b->id_batch === ($fefoPertama[$idJenis] ?? null) ? ' · saran FEFO' : ' · bukan prioritas FEFO' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label class="flex items-end gap-2 pb-2 text-sm">
                            <input type="checkbox" name="detail[{{ $det->id_detail_distribusi }}][terverifikasi]" value="1" class="h-4 w-4">
                            Nomor batch dan expiry sudah cocok dengan kemasan fisik
                        </label>
                    </div>
                </div>
            @endforeach
            </div>

            <label class="mt-4 flex items-start gap-2 text-sm text-slate-600">
                <input type="checkbox" name="konfirmasi_non_fefo" value="1" class="mt-1 h-4 w-4">
                Saya sengaja memilih batch yang bukan prioritas FEFO (hanya perlu dicentang jika ada peringatan FEFO).
            </label>
        </section>

        <div class="flex flex-wrap gap-2">
            <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Finalisasi transaksi</button>
        </div>
    </form>

    <form method="POST" action="{{ route('distribusi.destroy', $distribusi) }}" class="mt-3" onsubmit="return confirm('Buang draft ini?')">
        @csrf @method('DELETE')
        <button class="rounded-md border border-red-300 px-4 py-2 text-sm text-red-700 hover:bg-red-50">Buang draft</button>
    </form>
@else
    <section class="mb-5 overflow-x-auto rounded-lg border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Vaksin</th><th class="px-4 py-2">Batch</th><th class="px-4 py-2">Kedaluwarsa</th><th class="px-4 py-2 text-right">Jumlah (vial)</th><th class="px-4 py-2">Verifikasi</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @foreach ($distribusi->detail as $det)
                <tr>
                    <td class="px-4 py-2">{{ $det->batch->jenisVaksin->nama_vaksin }}</td>
                    <td class="px-4 py-2 font-medium">{{ $det->batch->nomor_batch }}</td>
                    <td class="px-4 py-2">{{ $det->batch->tanggal_kadaluwarsa->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 text-right">{{ $det->jumlah }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $det->waktu_terverifikasi?->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <div class="flex flex-wrap items-start gap-3">
        @if ($distribusi->nota)
            <a href="{{ route('nota.pdf', $distribusi) }}" target="_blank" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Buka / cetak nota PDF</a>
        @endif
        <a href="{{ route('distribusi.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">Kembali ke riwayat</a>
    </div>

    @if ($distribusi->status === 'Final')
        <form method="POST" action="{{ route('distribusi.batal', $distribusi) }}" class="mt-6 max-w-xl space-y-3 rounded-lg border border-red-200 bg-white p-4" onsubmit="return confirm('Batalkan transaksi ini dan kembalikan stok?')">
            @csrf
            <h2 class="text-sm font-semibold text-red-700">Batalkan transaksi</h2>
            <p class="text-xs text-slate-500">Pembatalan hanya mengembalikan stok ke batch asal. Transaksi dan nota tetap tersimpan di riwayat.</p>
            <x-input name="alasan_batal" label="Alasan pembatalan" required />
            <button class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Batalkan transaksi</button>
        </form>
    @endif
@endif
@endsection