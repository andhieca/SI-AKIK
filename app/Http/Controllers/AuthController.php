<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'tahun' => ['required', 'integer', 'digits:4'],
        ]);

        // Separate credentials for auth attempt (remove 'tahun')
        $authCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ];

        if (\Illuminate\Support\Facades\Auth::attempt($authCredentials)) {
            $request->session()->regenerate();

            // Store Selected Year in Session
            session(['tahun_anggaran' => $credentials['tahun']]);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
