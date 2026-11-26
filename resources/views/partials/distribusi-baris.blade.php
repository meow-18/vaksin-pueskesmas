@php $nama = "items[{$i}]"; @endphp
<div class="rounded-md border border-slate-200 p-3" data-baris>
    <div class="grid gap-3 sm:grid-cols-3">
        <x-select :name="$nama . '[id_jenis_vaksin]'" label="Jenis vaksin" :options="$vaksin->pluck('nama_vaksin', 'id_jenis_vaksin')" :value="$row['id_jenis_vaksin'] ?? null" placeholder="Pilih vaksin" class="sm:col-span-2" required />
        <x-input :name="$nama . '[jumlah]'" label="Jumlah (vial)" type="number" min="1" :value="$row['jumlah'] ?? null" required />
    </div>
    <button type="button" class="mt-2 text-xs text-red-600 hover:underline" data-hapus>Hapus baris</button>
</div>