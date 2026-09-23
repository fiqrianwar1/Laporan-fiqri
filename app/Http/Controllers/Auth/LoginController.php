<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        // Kalau sudah login, langsung lempar ke halaman sesuai rolenya.
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user()->role);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Arahkan user ke halaman awal sesuai rolenya.
     * tinter  -> yang boleh mengisi laporan
     * manajer -> atasan, hanya melihat data
     */
    protected function redirectByRole(?string $role)
    {
        // Manajer langsung dibawa ke halaman pengawasan miliknya, karena
        // fokusnya memang memantau - bukan mengisi laporan.
        if ($role === 'manajer') {
            return redirect()->route('manajer.dashboard');
        }

        return redirect()->route('laporan-oplosan.index');
    }
}
