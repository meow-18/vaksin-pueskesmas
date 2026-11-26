<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use Barryvdh\DomPDF\Facade\Pdf;

/** FR-08: nota PDF. Dibuat saat dibuka, sehingga dapat dicetak ulang dari riwayat tanpa mengubah data. */
class NotaController extends Controller
{
    public function pdf(Distribusi $distribusi)
    {
        abort_unless($distribusi->nota, 404, 'Nota belum diterbitkan.');

        $distribusi->load(['fasilitas', 'pengguna', 'nota', 'detail.batch.jenisVaksin']);

        return Pdf::loadView('nota.pdf', ['d' => $distribusi])
            ->setPaper('a4')
            ->stream(str_replace('/', '-', $distribusi->nota->no_nota) . '.pdf');
    }
}