<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $daftar = AuditLog::with('pengguna')
            ->when($request->filled('aktivitas'), fn ($q) => $q->where('aktivitas', 'like', $request->string('aktivitas') . '%'))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('waktu', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('waktu', '<=', $request->date('sampai')))
            ->orderByDesc('id_audit_log')
            ->paginate(25)
            ->withQueryString();

        return view('audit-log.index', compact('daftar'));
    }
}