<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status_akun !== 'Aktif') {
                Auth::logout();

                return back()->withErrors([
                    'username' => 'Akun kamu belum aktif.',
                ])->onlyInput('username');
            }

            if ($user->role === 'Admin' || $user->role === 'Super Admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('penghuni.dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
