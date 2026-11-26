@php $nama = "items[{$i}]"; @endphp
<div class="baris rounded-md border border-slate-200 p-3" data-baris>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <x-select :name="$nama . '[id_jenis_vaksin]'" label="Jenis vaksin" :options="$vaksin->pluck('nama_vaksin', 'id_jenis_vaksin')" :value="$row['id_jenis_vaksin'] ?? null" placeholder="Pilih vaksin" required />
        <x-input :name="$nama . '[nomor_batch]'" label="Nomor batch" :value="$row['nomor_batch'] ?? null" required />
        <x-input :name="$nama . '[tanggal_kadaluwarsa]'" label="Tanggal kedaluwarsa" type="date" :value="$row['tanggal_kadaluwarsa'] ?? null" required />
        <x-input :name="$nama . '[jumlah_diterima]'" label="Jumlah (vial)" type="number" min="1" :value="$row['jumlah_diterima'] ?? null" required />
    </div>
    <button type="button" class="mt-2 text-xs text-red-600 hover:underline" data-hapus>Hapus baris</button>
</div>