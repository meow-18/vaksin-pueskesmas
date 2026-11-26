<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi Vaksin</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 14px; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #444; padding: 4px 5px; text-align: left; vertical-align: top; }
        th { background: #eee; } .kanan { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Distribusi Vaksin · {{ config('vaksin.nama_puskesmas') }}</h1>
    <div>
        Periode: {{ $filter['dari'] ?? 'awal' }} s.d. {{ $filter['sampai'] ?? 'sekarang' }}
        @if (! empty($filter['status'])) · Status: {{ $filter['status'] }} @endif
        · Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead><tr><th>Tanggal</th><th>No. nota</th><th>Fasilitas</th><th>Pengambil</th><th>Status</th><th>Vaksin / batch</th><th class="kanan">Total vial</th></tr></thead>
        <tbody>
        @forelse ($daftar as $d)
            <tr>
                <td>{{ $d->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                <td>{{ $d->nota?->no_nota ?? '-' }}</td>
                <td>{{ $d->fasilitas->nama_fasilitas }}</td>
                <td>{{ $d->nama_pengambil }}</td>
                <td>{{ $d->status }}</td>
                <td>
                    @foreach ($d->detail as $det)
                        {{ $det->batch->jenisVaksin->nama_vaksin }} · {{ $det->batch->nomor_batch }} ({{ $det->jumlah }})<br>
                    @endforeach
                </td>
                <td class="kanan">{{ $d->detail->sum('jumlah') }}</td>
            </tr>
        @empty
            <tr><td colspan="7">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>