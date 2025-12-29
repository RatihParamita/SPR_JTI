<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index() {

        $breadcrumb = (object) [
            'title' => 'Daftar Admin',
            'list' => ['Home','Daftar Admin']
         ];

         $page = (object) [
            'title' => 'Admin yang terdaftar dalam sistem'
         ];

         $activeMenu = 'admin'; //set menu yang sedang aktif
         $prodiList = ProdiModel::all();
         $admins = AdminModel::with(['user', 'prodi'])->get(); // Fetch all admins for client-side rendering

         return view('admin.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'prodiList' => $prodiList, 'admins' => $admins]);
    }

    public function list(Request $request)
    {
        $admins = AdminModel::with(['user', 'prodi']); // Eager load relasi user dan prodi

        // FILTER BERDASARKAN PRODI (prodi_id)
        if ($request->has('prodi_id') && $request->prodi_id != '') {
            $admins->where('prodi_id', $request->prodi_id);
        }

        // SEARCH BERDASARKAN admin_nama
        // DataTables mengirimkan parameter pencarian melalui 'search[value]'
        if ($request->filled('search.value')) {
            $searchValue = $request->input('search.value');
            $admins->where('admin_nama', 'like', '%' . $searchValue . '%');
        }

        // MANUAL DEBUG RESPONSE
        $data = AdminModel::with(['user', 'prodi'])->get();
        
        // Transform data to include computed columns
        $transformedData = $data->map(function ($item, $key) {
            $item->DT_RowIndex = $key + 1;
            $item->prodi_kode = $item->prodi ? $item->prodi->prodi_kode : '-';
            $item->aksi = '<button class="btn btn-sm btn-info">Test</button>';
            return $item;
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'data' => $transformedData
        ]);
    }

    public function create_ajax(Request $request)
    {
        $prodiList = ProdiModel::all();
        // Cek apakah request datang dari AJAX. Jika tidak, redirect ke halaman utama.
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.create_ajax', compact('prodiList'));
        }
        return redirect('/');
        
        /*if (!request()->ajax() && !request()->wantsJson()) {
            // Jika bukan AJAX, lakukan redirect atau berikan respon yang sesuai
            return redirect('/admin')->with('error', 'Akses tidak diizinkan.');
        }

        // Ambil data yang dibutuhkan untuk form
        $prodiList = ProdiModel::all(); 
        
        // Mengembalikan view modal/form tambah
        return view('admin.create_ajax', compact('prodiList'));

        /*$prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown
        return view('admin.create_ajax', compact('prodiList'));*/
    }

    public function store_ajax(Request $request)
    {
        // Tentukan Level ID untuk Admin (Asumsi Level Kode 'ADM' memiliki ID tertentu)
        $levelAdmin = LevelModel::where('level_kode', 'ADM')->first();
        if (!$levelAdmin) {
            return response()->json([
                'status' => false,
                'message' => 'Level Admin (ADM) tidak ditemukan dalam database!'
            ], 500);
        }
        $levelId = $levelAdmin->level_id;

        // Aturan Validasi
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:m_user,username',
            'password' => 'required|string|min:6|max:100',
            'admin_nidn' => 'required|string|max:10|unique:m_admin,admin_nidn',
            'admin_nama' => 'required|string|max:100',
            'prodi_id' => 'required|integer|exists:m_prodi,prodi_id',
            'admin_noHp' => 'nullable|string|max:13',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'admin_nidn.required' => 'NIDN wajib diisi.',
            'admin_nidn.max' => 'NIDN maksimal 10 karakter.',
            'admin_nidn.unique' => 'NIDN sudah terdaftar.',
            'admin_nama.required' => 'Nama Admin wajib diisi.',
            'prodi_id.required' => 'Program Studi wajib dipilih.',
            'prodi_id.exists' => 'Program Studi tidak valid.',
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // 1. Simpan data ke tabel m_user
            $user = UserModel::create([
                'username' => $request->username,
                'password' => Hash::make($request->password), // Hash password sebelum disimpan
                'level_id' => $levelId,
            ]);

            // 2. Simpan data ke tabel m_admin
            AdminModel::create([
                'user_id' => $user->user_id, // Gunakan ID User yang baru dibuat
                'prodi_id' => $request->prodi_id,
                'admin_nama' => $request->admin_nama,
                'admin_nidn' => $request->admin_nidn,
                'admin_noHp' => $request->admin_noHp,
            ]);

            // Respon sukses
            return response()->json([
                'status' => true,
                'message' => 'Data admin berhasil ditambahkan!',
            ], 201);

        } catch (QueryException $e) {
            // Hapus user yang sudah terbuat jika terjadi error saat membuat admin
            if (isset($user)) {
                $user->delete(); 
            }
            return response()->json([
                'status' => false,
                'message' => 'Penyimpanan data gagal. Error Database: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Hapus user yang sudah terbuat jika terjadi error
            if (isset($user)) {
                $user->delete(); 
            }
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
        
        /*if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'admin_nidn'        => 'required|string|max:10|unique:m_admin,admin_nidn',
                'admin_nama'        => 'required|string|max:100',
                'prodi_id'          => 'required|integer|exists:m_prodi,prodi_id',
                'admin_noHp'        => 'required|string|max:13',
                'username'          => 'required|string|unique:m_user,username|max:100',
                'password'          => 'required|string|min:6|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            
            //DB::beginTransaction();
            try {
                // Transaksi: Simpan User
                $user = new UserModel();
                $user->username = $request->input('username');
                $user->password = Hash::make($request->input('password'));
                $user->level_id = 1;
                $user->save();

                // Transaksi: Simpan Admin
                $admin = new AdminModel();
                $admin->user_id = $user->user_id;
                $admin->admin_nidn = $request->input('admin_nidn');
                $admin->admin_nama = $request->input('admin_nama');
                $admin->prodi_id = $request->input('prodi_id');
                $admin->admin_noHp = $request->input('admin_noHp');
                $admin->save();

                //DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data admin berhasil disimpan!',
                ]);

            } catch (\Exception $e) {
                //DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data admin: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');*/
    }

    public function show_ajax(string $id)
    {
        // Relasi prodi dan user diload
        $admin = AdminModel::with(['user', 'prodi'])->find($id);

        if (!$admin) {
            abort(404, 'Data admin tidak ditemukan!');
        }

        return view('admin.show_ajax', compact('admin'));
    }

    public function edit_ajax(string $id)
    {
        $admin = AdminModel::find($id);
        $prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown

        if (!$admin) {
            abort(404, 'Data admin tidak ditemukan!');
        }

        return view('admin.edit_ajax', compact('admin', 'prodiList'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $admin = AdminModel::find($id);

            if (!$admin) {
                return response()->json(['status' => false, 'message' => 'Data admin tidak ditemukan!'], 404);
            }

            // Validasi: Pastikan username unik kecuali untuk user yang sedang diedit
            $rules = [
                'admin_nidn'        => 'required|string|max:10|unique:m_admin,admin_nidn,' . $id . ',admin_id',
                'admin_nama'        => 'required|string|max:100',
                'prodi_id'          => 'required|integer|exists:m_prodi,prodi_id',
                'admin_noHp'        => 'required|string|max:13',
                'username'          => 'nullable|string|max:100|unique:m_user,username,' . $admin->user_id . ',user_id',
                'password'          => 'nullable|string|min:6|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                // Update Data Admin
                $admin->update([
                    'admin_nidn' => $request->admin_nidn,
                    'admin_nama' => $request->admin_nama,
                    'prodi_id'   => $request->prodi_id,
                    'admin_noHp' => $request->admin_noHp,
                ]);

                // Update Data User (jika ada perubahan username atau password)
                $user = UserModel::find($admin->user_id);
                if ($user) {
                    $userData = [];
                    if ($request->filled('username')) {
                        $userData['username'] = $request->username;
                    }
                    if ($request->filled('password')) {
                        $userData['password'] = Hash::make($request->password);
                    }
                    if (!empty($userData)) {
                        $user->update($userData);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data admin berhasil diupdate!',
                ]);
            } catch (\Exception $e) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupdate data admin: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $admin = AdminModel::with('user', 'prodi')->find($id);

        if (!$admin) {
            return response()->json(['status' => false, 'message' => 'Data admin tidak ditemukan!'], 404);
        }

        return view('admin.confirm_ajax', compact('admin'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $admin = AdminModel::find($id);

                if (!$admin) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data admin tidak ditemukan!'
                    ], 404);
                }

                $userId = $admin->user_id;

                // 1. Hapus data Admin
                $admin->delete();

                // 2. Hapus data User terkait
                UserModel::destroy($userId);

                return response()->json([
                    'status' => true,
                    'message' => 'Data admin dan user berhasil dihapus!'
                ]);

            } catch (QueryException $e) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus karena masih digunakan di tabel lain!'
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
