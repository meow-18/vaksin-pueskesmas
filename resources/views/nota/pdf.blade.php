<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $d->nota->no_nota }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 15px; margin: 0; } h2 { font-size: 13px; margin: 0 0 6px; }
        .kop { border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 5px 6px; text-align: left; }
        th { background: #eee; } .kanan { text-align: right; }
        .info td { border: 0; padding: 2px 0; }
        .ttd { margin-top: 36px; width: 100%; } .ttd td { border: 0; text-align: center; height: 80px; vertical-align: bottom; }
        .batal { color: #b00020; font-size: 20px; font-weight: bold; border: 3px solid #b00020; display: inline-block; padding: 2px 10px; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>{{ config('vaksin.nama_puskesmas') }}</h1>
        <div>{{ config('vaksin.alamat_puskesmas') }}</div>
    </div>

    <h2>NOTA DISTRIBUSI VAKSIN
        @if ($d->status === 'Dibatalkan') <span class="batal">DIBATALKAN</span> @endif
    </h2>

    <table class="info">
        <tr><td width="22%">Nomor nota</td><td>: {{ $d->nota->no_nota }}</td></tr>
        <tr><td>Tanggal terbit</td><td>: {{ $d->nota->tanggal_terbit->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Fasilitas penerima</td><td>: {{ $d->fasilitas->nama_fasilitas }} ({{ $d->fasilitas->jenis }})</td></tr>
        <tr><td>Alamat</td><td>: {{ $d->fasilitas->alamat }}</td></tr>
        <tr><td>Nama pengambil</td><td>: {{ $d->nama_pengambil }}</td></tr>
    </table>

    <br>
    <table>
        <thead><tr><th>No</th><th>Jenis vaksin</th><th>Nomor batch</th><th>Kedaluwarsa</th><th class="kanan">Jumlah (vial)</th><th class="kanan">Akumulasi kotak</th></tr></thead>
        <tbody>
        @foreach ($d->detail as $i => $det)
            @php
                $per = max(1, (int) $det->batch->jenisVaksin->vial_per_kotak);
                $kotak = intdiv($det->jumlah, $per); $sisa = $det->jumlah % $per;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td><td>{{ $det->batch->jenisVaksin->nama_vaksin }}</td><td>{{ $det->batch->nomor_batch }}</td>
                <td>{{ $det->batch->tanggal_kadaluwarsa->format('d/m/Y') }}</td><td class="kanan">{{ $det->jumlah }}</td>
                <td class="kanan">{{ $kotak }} kotak{{ $sisa ? " + {$sisa} vial" : '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td width="50%">Penerima<br><br><br><br>( {{ $d->nama_pengambil }} )</td>
            <td width="50%">Apoteker penanggung jawab<br><br><br><br>( {{ $d->pengguna->nama_pengguna }} )</td>
        </tr>
    </table>
</body>
</html>