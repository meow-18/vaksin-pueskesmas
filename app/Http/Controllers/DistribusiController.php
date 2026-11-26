<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Distribusi;
use App\Models\Fasilitas;
use App\Models\JenisVaksin;
use App\Services\DistribusiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** FR-05 s.d. FR-09, FR-14: distribusi, verifikasi batch, nota, riwayat, pembatalan. */
class DistribusiController extends Controller
{
    /** Riwayat distribusi dengan filter periode, fasilitas, vaksin, dan nomor batch. */
    public function index(Request $request): View
    {
        $daftar = $this->query($request)
            ->with(['fasilitas', 'nota', 'pengguna'])
            ->orderByDesc('id_distribusi')
            ->paginate(15)
            ->withQueryString();

        return view('distribusi.index', [
            'daftar' => $daftar,
            'fasilitas' => Fasilitas::orderBy('nama_fasilitas')->get(),
            'vaksin' => JenisVaksin::orderBy('nama_vaksin')->get(),
        ]);
    }

    public function create(): View
    {
        return view('distribusi.create', [
            'fasilitas' => Fasilitas::where('aktif', true)->orderBy('nama_fasilitas')->get(),
            'vaksin' => JenisVaksin::where('aktif', true)->orderBy('nama_vaksin')->get(),
        ]);
    }

    public function store(Request $request, DistribusiService $service): RedirectResponse
    {
        $data = $request->validate([
            'id_fasilitas' => ['required', 'integer', 'exists:fasilitas,id_fasilitas'],
            'nama_pengambil' => ['required', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_jenis_vaksin' => ['required', 'integer', 'exists:jenis_vaksin,id_jenis_vaksin'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $distribusi = $service->buatDraft($request->user(), $data, $data['items']);

        return redirect()->route('distribusi.show', $distribusi)
            ->with('sukses', 'Draft tersimpan. Cocokkan batch dengan kemasan fisik, lalu finalisasi.');
    }

    public function show(Distribusi $distribusi): View
    {
        $distribusi->load(['fasilitas', 'pengguna', 'pembatal', 'nota', 'detail.batch.jenisVaksin']);

        $alternatif = [];
        $fefoPertama = [];
        if ($distribusi->status === Distribusi::DRAFT) {
            foreach ($distribusi->detail as $det) {
                $idJenis = $det->batch->id_jenis_vaksin;
                $semua = Batch::with('jenisVaksin')->where('id_jenis_vaksin', $idJenis)->untukDistribusi()->fefo()->get();
                $fefoPertama[$idJenis] = $semua->first()?->id_batch;
                $alternatif[$det->id_detail_distribusi] = $semua->filter(
                    fn (Batch $b) => $b->id_batch === $det->id_batch || $b->stok_saat_ini >= $det->jumlah
                )->values();
            }
        }

        return view('distribusi.show', compact('distribusi', 'alternatif', 'fefoPertama'));
    }

    public function finalisasi(Request $request, Distribusi $distribusi, DistribusiService $service): RedirectResponse
    {
        $request->validate([
            'detail' => ['required', 'array'],
            'detail.*.id_batch' => ['nullable', 'integer'],
            'detail.*.terverifikasi' => ['nullable'],
        ]);

        $service->finalisasi($distribusi, $request->user(), $request->input('detail', []), $request->boolean('konfirmasi_non_fefo'));

        return redirect()->route('distribusi.show', $distribusi)->with('sukses', 'Transaksi final. Stok dikurangi dan nota diterbitkan.');
    }

    public function batal(Request $request, Distribusi $distribusi, DistribusiService $service): RedirectResponse
    {
        $data = $request->validate(['alasan_batal' => ['required', 'string', 'max:255']]);

        $service->batalkan($distribusi, $request->user(), $data['alasan_batal']);

        return redirect()->route('distribusi.show', $distribusi)->with('sukses', 'Transaksi dibatalkan dan stok dikembalikan ke batch asal.');
    }

    public function destroy(Request $request, Distribusi $distribusi, DistribusiService $service): RedirectResponse
    {
        $service->hapusDraft($distribusi, $request->user());

        return redirect()->route('distribusi.index')->with('sukses', 'Draft dibuang.');
    }

    /** Laporan distribusi dalam format PDF dengan filter yang sama seperti riwayat. */
    public function laporan(Request $request)
    {
        $daftar = $this->query($request)
            ->with(['fasilitas', 'nota', 'detail.batch.jenisVaksin'])
            ->orderBy('tanggal_transaksi')
            ->get();

        return Pdf::loadView('laporan.pdf', [
            'daftar' => $daftar,
            'filter' => $request->only(['dari', 'sampai', 'status']),
        ])->setPaper('a4', 'landscape')->download('laporan-distribusi-' . now()->format('Ymd-His') . '.pdf');
    }

    private function query(Request $request)
    {
        return Distribusi::query()
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal_transaksi', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_transaksi', '<=', $request->date('sampai')))
            ->when($request->filled('id_fasilitas'), fn ($q) => $q->where('id_fasilitas', $request->integer('id_fasilitas')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('id_jenis_vaksin'), fn ($q) => $q->whereHas('detail.batch', fn ($b) => $b->where('id_jenis_vaksin', $request->integer('id_jenis_vaksin'))))
            ->when($request->filled('nomor_batch'), fn ($q) => $q->whereHas('detail.batch', fn ($b) => $b->where('nomor_batch', 'like', '%' . $request->string('nomor_batch') . '%')));
    }
}