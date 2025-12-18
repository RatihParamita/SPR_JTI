<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WelcomeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang!',
            'list' => ['Home', 'Dashboard']
        ];
        $user = Auth::user();
        $levelId = (int) ($user->level_id ?? 0);
        $activeMenu = 'dashboard';

        // ====== Hitung statistik dasar ======
        $today = now()->toDateString();

        $totalRuangan   = DB::table('m_ruangan')->count();

        $jadwalHariIni  = DB::table('t_jadwal')
            ->whereDate('jadwal_tgl', $today)
            ->count();
        
        

        switch($user->level_id) {
            case 1:
                return view('admin.welcome', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
            case 2:
                return view('dosen.welcome', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
            case 3:
                return view('tendik.welcome', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
            case 4:
                return view('mahasiswa.welcome', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
            

        }

        //return view('welcome', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
