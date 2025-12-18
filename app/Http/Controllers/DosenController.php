<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DosenModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DosenController extends Controller
{
    public function index() {

        $breadcrumb = (object) [
            'title' => 'Daftar Dosen',
            'list' => ['Home','Daftar Dosen']
         ];

         $page = (object) [
            'title' => 'Dosen yang terdaftar dalam sistem'
         ];

         $activeMenu = 'dosen'; //set menu yang sedang aktif
         $prodiList = ProdiModel::all();
         $dosens = DosenModel::with(['user', 'prodi'])->get(); // Fetch all dosens for client-side rendering

         return view('dosen.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'prodiList' => $prodiList, 'dosens' => $dosens]);
    }

    public function list(Request $request)
    {
        try {
            $dosens = DosenModel::select('m_dosen.*')->with('prodi'); 

            // FILTER BERDASARKAN PRODI (prodi_id)
            if ($request->has('prodi_id') && $request->prodi_id != '') {
                $dosens->where('prodi_id', $request->prodi_id);
            }

            return DataTables::of($dosens)
                ->addIndexColumn() 
                ->addColumn('prodi_nama', function ($dosen) {
                    return $dosen->prodi ? $dosen->prodi->prodi_nama : '-';
                })
                ->addColumn('aksi', function ($dosen) {
                    $btn = '<button onclick="modalAction(\''.url('/dosen/' . $dosen->dosen_id . '/show_ajax').'\')" class="btn btn-outline-info btn-sm" title="Detail"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dosen->dosen_id . '/edit_ajax').'\')" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dosen->dosen_id . '/confirm_ajax').'\')" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['aksi']) 
                ->make(true);
        } catch (\Throwable $e) {
            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function create_ajax(Request $request)
    {
        $prodiList = ProdiModel::all();
        // Cek apakah request datang dari AJAX. Jika tidak, redirect ke halaman utama.
        if ($request->ajax() || $request->wantsJson()) {
            return view('dosen.create_ajax', compact('prodiList'));
        }
        return redirect('/');
        
        /*if (!request()->ajax() && !request()->wantsJson()) {
            // Jika bukan AJAX, lakukan redirect atau berikan respon yang sesuai
            return redirect('/dosen')->with('error', 'Akses tidak diizinkan.');
        }

        // Ambil data yang dibutuhkan untuk form
        $prodiList = ProdiModel::all(); 
        
        // Mengembalikan view modal/form tambah
        return view('dosen.create_ajax', compact('prodiList'));

        /*$prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown
        return view('dosen.create_ajax', compact('prodiList'));*/
    }

    public function store_ajax(Request $request)
    {
        // Tentukan Level ID untuk Dosen (Asumsi Level Kode 'DSN' memiliki ID tertentu)
        $levelDosen = LevelModel::where('level_kode', 'DSN')->first();
        if (!$levelDosen) {
            return response()->json([
                'status' => false,
                'message' => 'Level Dosen (DSN) tidak ditemukan dalam database!'
            ], 500);
        }
        $levelId = $levelDosen->level_id;

        // Aturan Validasi
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:m_user,username',
            'password' => 'required|string|min:6|max:100',
            'dosen_nidn' => 'required|string|max:10|unique:m_dosen,dosen_nidn',
            'dosen_nama' => 'required|string|max:100',
            'prodi_id' => 'required|integer|exists:m_prodi,prodi_id',
            'dosen_noHp' => 'nullable|string|max:13',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'dosen_nidn.required' => 'NIDN wajib diisi.',
            'dosen_nidn.max' => 'NIDN maksimal 10 karakter.',
            'dosen_nidn.unique' => 'NIDN sudah terdaftar.',
            'dosen_nama.required' => 'Nama Dosen wajib diisi.',
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

            // 2. Simpan data ke tabel m_dosen
            DosenModel::create([
                'user_id' => $user->user_id, // Gunakan ID User yang baru dibuat
                'prodi_id' => $request->prodi_id,
                'dosen_nama' => $request->dosen_nama,
                'dosen_nidn' => $request->dosen_nidn,
                'dosen_noHp' => $request->dosen_noHp,
            ]);

            // Respon sukses
            return response()->json([
                'status' => true,
                'message' => 'Data dosen berhasil ditambahkan!',
            ], 201);

        } catch (QueryException $e) {
            // Hapus user yang sudah terbuat jika terjadi error saat membuat dosen
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
                'dosen_nidn'        => 'required|string|max:10|unique:m_dosen,dosen_nidn',
                'dosen_nama'        => 'required|string|max:100',
                'prodi_id'          => 'required|integer|exists:m_prodi,prodi_id',
                'dosen_noHp'        => 'required|string|max:13',
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

                // Transaksi: Simpan Dosen
                $dosen = new DosenModel();
                $dosen->user_id = $user->user_id;
                $dosen->dosen_nidn = $request->input('dosen_nidn');
                $dosen->dosen_nama = $request->input('dosen_nama');
                $dosen->prodi_id = $request->input('prodi_id');
                $dosen->dosen_noHp = $request->input('dosen_noHp');
                $dosen->save();

                //DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data dosen berhasil disimpan!',
                ]);

            } catch (\Exception $e) {
                //DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data dosen: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');*/
    }

    public function show_ajax(string $id)
    {
        if (request()->ajax()) {
            // Relasi prodi dan user diload
            $dosen = DosenModel::with(['user', 'prodi'])->find($id);

            if (!$dosen) {
                abort(404, 'Data Dosen tidak ditemukan.');
            }

            return view('dosen.show_ajax', compact('dosen'));
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        if (request()->ajax()) {
            $dosen = DosenModel::find($id);
            $prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown

            if (!$dosen) {
                abort(404, 'Data Dosen tidak ditemukan.');
            }

            return view('dosen.edit_ajax', compact('dosen', 'prodiList'));
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $dosen = DosenModel::find($id);

            if (!$dosen) {
                return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            // Validasi: Pastikan username unik kecuali untuk user yang sedang diedit
            $rules = [
                'dosen_nidn'        => 'required|string|max:10|unique:m_dosen,dosen_nidn,' . $id . ',dosen_id',
                'dosen_nama'        => 'required|string|max:100',
                'prodi_id'          => 'required|integer|exists:m_prodi,prodi_id',
                'dosen_noHp'        => 'required|string|max:13',
                'username'          => 'nullable|string|max:100|unique:m_user,username,' . $dosen->user_id . ',user_id',
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
                // Update Data Dosen
                $dosen->update([
                    'dosen_nidn' => $request->dosen_nidn,
                    'dosen_nama' => $request->dosen_nama,
                    'prodi_id'   => $request->prodi_id,
                    'dosen_noHp' => $request->dosen_noHp,
                ]);

                // Update Data User (jika ada perubahan username atau password)
                $user = UserModel::find($dosen->user_id);
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
                    'message' => 'Data dosen berhasil diupdate!',
                ]);
            } catch (\Exception $e) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupdate data dosen: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        if (request()->ajax()) {
            $dosen = DosenModel::with('user', 'prodi')->find($id);

            if (!$dosen) {
                return response()->json(['status' => false, 'message' => 'Data dosen tidak ditemukan'], 404);
            }

            return view('dosen.confirm_ajax', compact('dosen'));
        }
        return redirect('/');
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $dosen = DosenModel::find($id);

                if (!$dosen) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data dosen tidak ditemukan!'
                    ], 404);
                }

                $userId = $dosen->user_id;

                // 1. Hapus data Dosen
                $dosen->delete();

                // 2. Hapus data User terkait
                UserModel::destroy($userId);

                return response()->json([
                    'status' => true,
                    'message' => 'Data dosen berhasil dihapus!'
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

    public function import()
    {
        if (request()->ajax()) {
            return view('dosen.import');
        }
        return redirect('/');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_dosen' => 'required|file|mimes:xlsx,xls|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            
            $file = $request->file('file_dosen');
            
            try {
                $reader = IOFactory::createReaderForFile($file->getRealPath());
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                // 1. Get Level Dosen ID
                $levelDosen = LevelModel::where('level_kode', 'DSN')->first();
                if (!$levelDosen) {
                    return response()->json([
                         'status' => false,
                         'message' => 'Level Dosen (DSN) tidak ditemukan!'
                    ], 500);
                }
                $levelId = $levelDosen->level_id;

                // 2. Load all Prodi into an associative array for quick lookup [ 'Name' => 'id' ]
                $prodiMap = ProdiModel::pluck('prodi_id', 'prodi_nama')->toArray();

                $insertedCount = 0;
                
                if (count($data) > 1) {
                    DB::beginTransaction(); // Start Transaction

                    foreach ($data as $row => $value) {
                         if ($row === 0) continue; // Skip header row

                         $nidn = isset($value[0]) ? trim($value[0]) : null;   // A
                         $nama = isset($value[1]) ? trim($value[1]) : null;   // B
                         $prodiName = isset($value[2]) ? trim($value[2]) : null; // C
                         $noHp = isset($value[3]) ? trim($value[3]) : null;   // D
                         
                         if (!$nidn || !$nama || !$prodiName) {
                             continue; // Skip invalid rows
                         }

                         // Check duplication
                         if (UserModel::where('username', $nidn)->exists()) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "Username/NIDN $nidn sudah terdaftar! Impor dibatalkan.",
                             ], 400); 
                         }
                         if (DosenModel::where('dosen_nidn', $nidn)->exists()) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "NIDN $nidn sudah terdaftar! Impor dibatalkan.",
                             ], 400); 
                         }

                         // Lookup Prodi ID
                         $prodiId = $prodiMap[$prodiName] ?? null;
                         if (!$prodiId) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "Program Studi '$prodiName' tidak ditemukan! Pastikan nama sama persis dengan program studi terdaftar.",
                             ], 400);
                         }

                         // Create User
                         $user = UserModel::create([
                             'username' => $nidn,
                             'password' => Hash::make($nidn), // Default password = NIDN
                             'level_id' => $levelId,
                         ]);

                         // Create Dosen
                         DosenModel::create([
                             'user_id' => $user->user_id,
                             'prodi_id' => $prodiId,
                             'dosen_nidn' => $nidn,
                             'dosen_nama' => $nama,
                             'dosen_noHp' => $noHp,
                         ]);

                         $insertedCount++;
                    }

                    DB::commit();

                    if ($insertedCount > 0) {
                        return response()->json([
                            'status' => true,
                            'message' => "$insertedCount data dosen berhasil diimpor!",
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Tidak ada data dosen yang diimpor (Mungkin file kosong atau format salah).',
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'File kosong atau format salah!',
                    ], 400);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }
}
