<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeUser
{
    /**
     * Menangani permintaan masuk (incoming request).
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Akses ditolak. Silakan login terlebih dahulu.');
        }

        // 2. Ambil kode level dari user yang sedang login menggunakan method getRole() dari UserModel
        // Asumsi: getRole() akan mengembalikan string level_kode, misalnya 'ADM'
        
        // >>> BARIS DEBUGGING KUNCI! HAPUS INI SETELAH SELESAI. <<<
        // Ketika Anda mengakses rute '/admin', layar akan berhenti di sini 
        // dan menampilkan nilai level_kode (misal: 'ADM', 'admin', atau '').
        $user_role = $request->user()->getRole();
        
        // Cek hasil $user_role. Jika tidak sesuai dengan $roles (misal 'ADM'), ia akan abort.
        // TAPI, kita perlu lihat nilainya:
        // dd($user_role); 
        // >>> BARIS DEBUGGING KUNCI! HAPUS INI SETELAH SELESAI. <<<

        // 3. Cek apakah role user ada di daftar role yang diizinkan ($roles)
        if ($user_role && in_array($user_role, $roles)) {
            // Jika diizinkan, lanjutkan request
            return $next($request);
        }
        
        // 4. Jika level tidak sesuai (atau user_role null/kosong), batalkan request
        $requiredRoles = implode(', ', $roles);
        abort(403, 'Forbidden. Insufficient Permissions. Level Anda (' . ($user_role ?? 'KOSONG') . ') tidak diizinkan. Level yang dibutuhkan: ' . $requiredRoles);
    }
}