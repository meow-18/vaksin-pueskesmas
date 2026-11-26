<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function form(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Hanya akun aktif yang dapat masuk.
        if (! Auth::attempt(['username' => $data['username'], 'password' => $data['password'], 'aktif' => 1])) {
            AuditLogger::catat(null, 'login.gagal', 'pengguna', null, ['username' => $data['username']]);

            throw ValidationException::withMessages(['username' => 'Username atau kata sandi salah, atau akun tidak aktif.']);
        }

        $request->session()->regenerate();
        /** @var Pengguna $user */
        $user = Auth::user();
        AuditLogger::catat($user, 'login', 'pengguna', $user->id_pengguna);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        AuditLogger::catat($request->user(), 'logout', 'pengguna', $request->user()?->id_pengguna);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}