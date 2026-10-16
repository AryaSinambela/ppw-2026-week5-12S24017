<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // WAJIB: regenerasi ID sesi untuk mencegah Session Fixation
            $request->session()->regenerate();

            // Session state (disimpan di server)
            session(['login_timestamp' => now()->format('d M Y, H:i:s') . ' WIB']);

            // HTTP Cookie (terenkripsi, HttpOnly, SameSite=Lax) selama 7 hari
            Cookie::queue('del_last_user_email', $request->email, 10080);

            return redirect()->intended(route('buku.index'))
                ->with('success', 'Selamat datang! Sesi pustakawan Anda telah aktif.');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Kredensial login tidak valid.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesi server telah dihapus dengan aman.');
    }
}