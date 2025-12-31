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

        $activeMenu = 'dashboard';

        $user = Auth::user();

        // 1. Stat Cards
        $today = now()->toDateString();

        $totalRuangan   = DB::table('m_ruangan')->count();
        $jadwalHariIni  = DB::table('t_jadwal')
            ->whereDate('jadwal_tgl', $today)
            ->count();
        
        $ruanganTerpakaiHariIni = DB::table('t_jadwal')
            ->join('t_jadwal_ruangan', 't_jadwal.jadwal_id', '=', 't_jadwal_ruangan.jadwal_id')
            ->whereDate('t_jadwal.jadwal_tgl', $today)
            ->distinct('t_jadwal_ruangan.ruangan_id')
            ->count('t_jadwal_ruangan.ruangan_id');

        $totalRuanganKosong = $totalRuangan - $ruanganTerpakaiHariIni;
        
        $totalUser = DB::table('m_user')->count();

        // Tambahan Statistik User untuk Stat Card Baru
        $totalMahasiswa = DB::table('m_mahasiswa')->count();
        $totalDosen = DB::table('m_dosen')->count();
        $totalTendik = DB::table('m_tendik')->count();

        // 2. Bar Chart: Top 5 Ruangan Terfavorit (Bulan Ini)
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $topRuangan = \App\Models\RuanganModel::withCount(['jadwal' => function($q) use($currentMonth, $currentYear){
                $q->whereMonth('jadwal_tgl', $currentMonth)
                  ->whereYear('jadwal_tgl', $currentYear);
            }])
            ->having('jadwal_count', '>', 0)
            ->orderByDesc('jadwal_count')
            ->limit(5)
            ->get();

        // 3. Pie Chart: Distribusi Peminjam (Bulan Ini) - Admin, Dosen, Tendik, Mahasiswa
        // Kita butuh join ke m_user lalu cek levelnya. Atau cek user_id.
        // Karena struktur tabel terpisah (m_admin, m_dosen, dll), kita bisa cek level di m_user.
        $distribusiPeminjam = [
            'admin' => 0,
            'dosen' => 0,
            'tendik' => 0,
            'mahasiswa' => 0
        ];

        $peminjamanBulanIni = DB::table('t_jadwal')
            ->join('m_user', 't_jadwal.user_id', '=', 'm_user.user_id')
            ->join('m_level', 'm_user.level_id', '=', 'm_level.level_id')
            ->whereMonth('jadwal_tgl', $currentMonth)
            ->whereYear('jadwal_tgl', $currentYear)
            ->select('m_level.level_kode', DB::raw('count(*) as total'))
            ->groupBy('m_level.level_kode')
            ->get();

        foreach($peminjamanBulanIni as $item){
            if($item->level_kode == 'ADM') $distribusiPeminjam['admin'] = $item->total;
            if($item->level_kode == 'DSN') $distribusiPeminjam['dosen'] = $item->total;
            if($item->level_kode == 'TDK') $distribusiPeminjam['tendik'] = $item->total;
            if($item->level_kode == 'MHS') $distribusiPeminjam['mahasiswa'] = $item->total;
        }

        // 4. Line Chart: Tren Peminjaman (6 Bulan Terakhir)
        $sixMonthsAgo = now()->subMonths(6);
        $trenPeminjaman = DB::table('t_jadwal')
            ->where('jadwal_tgl', '>=', $sixMonthsAgo)
            ->selectRaw('COUNT(*) as total, DATE_FORMAT(jadwal_tgl, "%Y-%m") as bulan')
            ->groupBy(DB::raw('DATE_FORMAT(jadwal_tgl, "%Y-%m")'))
            ->orderBy('bulan', 'asc')
            ->get();
            
        // Correcting the structure for ChartJS (Needs to match labels and data)
        // Here we just pass the raw data, blade will process it.
        $lineChartData = [];
        foreach($trenPeminjaman as $t){
            $lineChartData[] = [
                'tanggal' => $t->bulan,
                'total_peminjaman' => $t->total
            ];
        }

        $viewData = [
            'breadcrumb' => $breadcrumb, 
            'activeMenu' => $activeMenu, 
            'totalRuangan' => $totalRuangan, 
            'jadwalHariIni' => $jadwalHariIni, 
            'totalRuanganKosong' => $totalRuanganKosong, 
            'totalUser' => $totalUser, 
            'topRuangan' => $topRuangan, 
            'distribusiPeminjam' => $distribusiPeminjam, 
            'trenPeminjaman' => $trenPeminjaman,
            'lineChartData' => $lineChartData, // Add this line
            'totalMahasiswa' => $totalMahasiswa,
            'totalDosen' => $totalDosen,
            'totalTendik' => $totalTendik
        ];

        switch($user->level_id) {
            case 1:
                return view('admin.welcome', $viewData);
                break;
            case 2:
                return view('dosen.welcome', $viewData);
                break;
            case 3:
                return view('tendik.welcome', $viewData);
                break;
            case 4:
                return view('mahasiswa.welcome', $viewData);
                break;
            default:
                return view('welcome', $viewData); // Fallback
        }
    }

    public function landing()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('landing');
    }

    public function uploadFormulir(Request $request)
    {
        $request->validate([
            'file_formulir' => 'required|mimes:pdf|max:2048',
        ]);

        try {
            $file = $request->file('file_formulir');
            $fileName = 'formulir_peminjaman_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/forms', $fileName);
            
            DB::table('m_formulir')->insert([
                'formulir_path' => $path,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Formulir berhasil diunggah!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengunggah formulir: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadFormulir()
    {
        $formulir = DB::table('m_formulir')->orderBy('created_at', 'desc')->first();

        if (!$formulir) {
            return back()->with('error', 'Belum ada formulir yang tersedia.');
        }

        $path = storage_path('app/' . $formulir->formulir_path);

        if (!file_exists($path)) {
             return back()->with('error', 'File fisik formulir tidak ditemukan.');
        }

        return response()->download($path, 'Formulir Peminjaman Ruangan JTI.pdf');
    }
}
