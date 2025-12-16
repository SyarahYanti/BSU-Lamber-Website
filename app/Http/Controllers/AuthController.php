<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // =========================
    // TAMPILKAN FORM LOGIN
    // =========================
    public function loginForm()
    {
        return view('auth.login');
    }

    // =========================
    // PROSES LOGIN
    // =========================
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Coba login
        if (Auth::attempt($request->only('email', 'password'))) {
            // Regenerasi session agar aman
            $request->session()->regenerate();

            // Setelah login -> langsung masuk dashboard
            return redirect()->route('dashboard')
                ->with('success', 'Anda berhasil masuk!');
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // =========================
    // PROSES LOGOUT
    // =========================
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }

    // =========================
    // HALAMAN DASHBOARD
    // =========================
    public function dashboard()
    {
        return view('dashboard');
    }
}
