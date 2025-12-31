<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
//redirect ke home jika sudah login
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {

                $level = Auth::user();
                

                switch ($level -> level_id) {
                    case 1:
                        $nama = Auth::user()->admin->admin_nama;
                        return response()->json([
                            'status' => true,
                            'message' => 'Selamat datang, ' . $nama . '.',
                            'redirect' => url('/dashboard')
                        ]);
                    case 2:
                        $nama = Auth::user()->dosen->dosen_nama;
                        return response()->json([
                            'status' => true,
                            'message' => 'Selamat datang, ' . $nama . '.',
                            'redirect' => url('/dashboard')
                        ]);
                    case 3:
                        $nama = Auth::user()->tendik->tendik_nama;
                        return response()->json([
                            'status' => true,
                            'message' => 'Selamat datang, ' . $nama . '.',
                            'redirect' => url('/dashboard')
                        ]);
                    case 4:
                        $nama = Auth::user()->mahasiswa->mahasiswa_nama;
                        return response()->json([
                            'status' => true,
                            'message' => 'Selamat datang, ' . $nama . '.',
                            'redirect' => url('/dashboard')
                        ]);
                 }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Login gagal, username atau password salah.'
                ]);
            }
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
