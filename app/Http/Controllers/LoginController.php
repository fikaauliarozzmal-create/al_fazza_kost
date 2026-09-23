<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function portal() { return view('auth.login'); }
    public function registerForm() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_whatsapp' => ['required', 'string', 'max:20', 'unique:users,no_whatsapp'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = User::create([
            'name' => $data['name'], 'no_whatsapp' => $data['no_whatsapp'], 'username' => $data['username'],
            // Tabel lama tetap mewajibkan email; login menggunakan username.
            'email' => $data['username'].'@alfazzakost.local', 'password' => Hash::make($data['password']),
            'role' => 'User', 'status_akun' => 'Aktif',
        ]);
        Auth::login($user); $request->session()->regenerate();
        return $this->redirectToRoleDashboard($user)
            ->with('success', 'Akun berhasil dibuat. Silakan lanjutkan booking kamar.');
    }

    public function loginPenghuni(Request $request)
    {
        $data = $request->validate(['username' => ['required', 'string'], 'password' => ['required', 'string']]);
        $user = User::where('username', $data['username'])->first();
        if (! $user || ! filled($user->password) || ! Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }
        if ($user->status_akun !== 'Aktif') return back()->withErrors(['username' => 'Akun belum aktif.'])->onlyInput('username');
        Auth::login($user); $request->session()->regenerate();
        return $this->redirectToRoleDashboard($user);
    }

    public function logout(Request $request)
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectToRoleDashboard(User $user)
    {
        return redirect()->route($user->isAdmin() ? 'dashboard' : 'penghuni.dashboard');
    }
}
