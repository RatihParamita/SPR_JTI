<?php

namespace App\Http\Controllers;

use App\Models\JadwalModel;
use App\Models\RuanganModel;
use App\Models\UserModel;
use App\Models\LevelModel;
use App\Models\ProdiModel;
use App\Models\KelasModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class JadwalController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Jadwal',
            'list' => ['Home', 'Daftar Jadwal']
        ];

        $page = (object) [
            'title' => 'Daftar jadwal yang terdaftar dalam sistem'
        ];

        $activeMenu = 'jadwal';
        $userList = UserModel::all();
        $ruanganList = RuanganModel::all();
        // Client-side: Load All Data
        $jadwals = JadwalModel::with(['user', 'ruangans'])->get();

        return view('jadwal.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'userList' => $userList, 'ruanganList' => $ruanganList, 'jadwals' => $jadwals]);
    }

    // Deprecated for client-side, but kept/emptied if needed by route (though we removed ajax call in view)
    public function list(Request $request) 
    {
        return response()->json([]);
    }

    public function create_ajax()
    {
        if (request()->ajax()) {
            // Load user with relations for filtering in frontend
            $userList = UserModel::with(['level', 'mahasiswa', 'dosen', 'tendik'])->get(); 
            $ruanganList = RuanganModel::all();
            
            // Data for filters
            $levelList = LevelModel::all();
            $prodiList = ProdiModel::all();
            $kelasList = KelasModel::all(); 

            return view('jadwal.create_ajax', compact('userList', 'ruanganList', 'levelList', 'prodiList', 'kelasList'));
        }
        return redirect('/');
    }
    
    public function get_kelas_by_prodi($prodi_id)
    {
        $kelasList = KelasModel::where('prodi_id', $prodi_id)->get();
        // Sort specifically by kelas_nama naturally to handle 1B, 3C, 10A correctly
        $sortedKelas = $kelasList->sortBy('kelas_nama', SORT_NATURAL)->values();
        return response()->json($sortedKelas);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'jadwal_nama'        => 'required|string|max:255',
                'jadwal_tgl'         => 'required|date',
                'jadwal_jam_mulai'   => 'required|date_format:H:i', // Format jam HTML5 biasanya H:i
                'jadwal_jam_selesai' => 'required|date_format:H:i|after:jadwal_jam_mulai',
                'jadwal_jumPes'      => 'required|integer',
                'user_id'            => 'required|exists:m_user,user_id',
                'ruangan_ids'        => 'required|array', // Array ID Ruangan
                'ruangan_ids.*'      => 'exists:m_ruangan,ruangan_id',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            try {
                // 1. Simpan Jadwal
                $jadwal = JadwalModel::create([
                    'jadwal_nama' => $request->jadwal_nama,
                    'jadwal_tgl' => $request->jadwal_tgl,
                    'jadwal_jam_mulai' => $request->jadwal_jam_mulai,
                    'jadwal_jam_selesai' => $request->jadwal_jam_selesai,
                    'jadwal_jumPes' => $request->jadwal_jumPes,
                    'user_id' => $request->user_id,
                ]);

                // 2. Simpan Relasi Ruangan (Attach pivot table)
                $jadwal->ruangans()->attach($request->ruangan_ids);

                return response()->json([
                    'status' => true,
                    'message' => 'Data jadwal berhasil disimpan!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        if (request()->ajax()) {
            $jadwal = JadwalModel::with(['user.level', 'user.mahasiswa.prodi', 'user.mahasiswa.kelas', 'ruangans'])->find($id);

            if (!$jadwal) {
                return response()->json(['status' => false, 'message' => 'Data jadwal tidak ditemukan!'], 404);
            }

            return view('jadwal.show_ajax', compact('jadwal'));
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        if (request()->ajax()) {
            $jadwal = JadwalModel::with('ruangans')->find($id);
            $userList = UserModel::with(['level', 'mahasiswa', 'dosen', 'tendik'])->get();
            $ruanganList = RuanganModel::all(); 
            
            // Data for filters
            $levelList = LevelModel::all();
            $prodiList = ProdiModel::all();
            $kelasList = KelasModel::all();

            if (!$jadwal) {
                return response()->json(['status' => false, 'message' => 'Data jadwal tidak ditemukan!'], 404);
            }

            return view('jadwal.edit_ajax', compact('jadwal', 'userList', 'ruanganList', 'levelList', 'prodiList', 'kelasList'));
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'jadwal_nama'        => 'required|string|max:255',
                'jadwal_tgl'         => 'required|date',
                'jadwal_jam_mulai'   => 'required|date_format:H:i',
                'jadwal_jam_selesai' => 'required|date_format:H:i|after:jadwal_jam_mulai',
                'jadwal_jumPes'      => 'required|integer',
                'user_id'            => 'required|exists:m_user,user_id',
                'ruangan_ids'        => 'required|array',
                'ruangan_ids.*'      => 'exists:m_ruangan,ruangan_id',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $jadwal = JadwalModel::find($id);

            if (!$jadwal) {
                return response()->json(['status' => false, 'message' => 'Data jadwal tidak ditemukan!'], 404);
            }

            try {
                // 1. Update Jadwal
                $jadwal->update([
                    'jadwal_nama' => $request->jadwal_nama,
                    'jadwal_tgl' => $request->jadwal_tgl,
                    'jadwal_jam_mulai' => $request->jadwal_jam_mulai,
                    'jadwal_jam_selesai' => $request->jadwal_jam_selesai,
                    'jadwal_jumPes' => $request->jadwal_jumPes,
                    'user_id' => $request->user_id,
                ]);

                // 2. Update Relasi Ruangan (Sync pivot table)
                // Sync akan menghapus yang tidak ada di array dan menambahkan yang baru
                $jadwal->ruangans()->sync($request->ruangan_ids);

                return response()->json([
                    'status' => true,
                    'message' => 'Data jadwal berhasil diupdate!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $jadwal = JadwalModel::with('user', 'ruangans')->find($id);

        if (!$jadwal) {
            return response()->json(['status' => false, 'message' => 'Data jadwal tidak ditemukan!'], 404);
        }

        return view('jadwal.confirm_ajax', compact('jadwal'));
        /*if (request()->ajax()) {
            $jadwal = JadwalModel::find($id);

            if (!$jadwal) {
                return response()->json(['status' => false, 'message' => 'Data jadwal tidak ditemukan!'], 404);
            }

            return view('jadwal.confirm_ajax', compact('jadwal'));
        }
        return redirect('/');*/
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $jadwal = JadwalModel::find($id);

            if (!$jadwal) {
                 return response()->json(['status' => false, 'message' => 'Data jadwal tidak ditemukan!'], 404);
            }

            try {
                // Hapus relasi di pivot table dulu (optional jika cascading delete sudah di set di database, tapi aman dilakukan manual)
                $jadwal->ruangans()->detach();
                
                // Hapus data jadwal
                $jadwal->delete();
                
                return response()->json([
                    'status' => true,
                    'message' => 'Data jadwal berhasil dihapus!'
                ]);
            } catch (QueryException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus karena masih digunakan!'
                ], 500);
            } catch (\Exception $e) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }
}
