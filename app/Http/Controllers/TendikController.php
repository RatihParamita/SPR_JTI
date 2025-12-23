<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TendikModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TendikController extends Controller
{
    //CLIENT-SIDE
    public function index() {

        $breadcrumb = (object) [
            'title' => 'Daftar Tendik',
            'list' => ['Home','Daftar Tendik']
         ];

         $page = (object) [
            'title' => 'Tendik yang terdaftar dalam sistem'
         ];

         $activeMenu = 'tendik'; //set menu yang sedang aktif
         //$prodiList = ProdiModel::all();
         $tendiks = TendikModel::with(['user'])->get(); // Fetch all tendiks for client-side rendering

         return view('tendik.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'tendiks' => $tendiks]);
    }

    public function list(Request $request)
    {
        try {
            $tendiks = TendikModel::select('m_tendik.*'); 

            // FILTER BERDASARKAN PRODI (prodi_id)
            /*if ($request->has('prodi_id') && $request->prodi_id != '') {
                $tendiks->where('prodi_id', $request->prodi_id);
            }*/

            return DataTables::of($tendiks)
                ->addIndexColumn() 
                /*->addColumn('prodi_nama', function ($tendik) {
                    return $tendik->prodi ? $tendik->prodi->prodi_nama : '-';
                })*/
                ->addColumn('aksi', function ($tendik) {
                    $btn = '<button onclick="modalAction(\''.url('/tendik/' . $tendik->tendik_id . '/show_ajax').'\')" class="btn btn-outline-info btn-sm" title="Detail"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/tendik/' . $tendik->tendik_id . '/edit_ajax').'\')" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/tendik/' . $tendik->tendik_id . '/confirm_ajax').'\')" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
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
        //$prodiList = ProdiModel::all();
        // Cek apakah request datang dari AJAX. Jika tidak, redirect ke halaman utama.
        if ($request->ajax() || $request->wantsJson()) {
            return view('tendik.create_ajax');
        }
        return redirect('/');
        
        /*if (!request()->ajax() && !request()->wantsJson()) {
            // Jika bukan AJAX, lakukan redirect atau berikan respon yang sesuai
            return redirect('/tendik')->with('error', 'Akses tidak diizinkan.');
        }

        // Ambil data yang dibutuhkan untuk form
        $prodiList = ProdiModel::all(); 
        
        // Mengembalikan view modal/form tambah
        return view('tendik.create_ajax', compact('prodiList'));

        /*$prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown
        return view('tendik.create_ajax', compact('prodiList'));*/
    }

    public function store_ajax(Request $request)
    {
        // Tentukan Level ID untuk Tendik (Asumsi Level Kode 'TDK' memiliki ID tertentu)
        $levelTendik = LevelModel::where('level_kode', 'TDK')->first();
        if (!$levelTendik) {
            return response()->json([
                'status' => false,
                'message' => 'Level Tendik (TDK) tidak ditemukan dalam database!'
            ], 500);
        }
        $levelId = $levelTendik->level_id;

        // Aturan Validasi
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:m_user,username',
            'password' => 'required|string|min:6|max:100',
            'tendik_nidn' => 'required|string|max:10|unique:m_tendik,tendik_nidn',
            'tendik_nama' => 'required|string|max:100',
            'tendik_noHp' => 'nullable|string|max:13',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'tendik_nidn.required' => 'NIDN wajib diisi.',
            'tendik_nidn.max' => 'NIDN maksimal 10 karakter.',
            'tendik_nidn.unique' => 'NIDN sudah terdaftar.',
            'tendik_nama.required' => 'Nama Tendik wajib diisi.',
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

            // 2. Simpan data ke tabel m_tendik
            TendikModel::create([
                'user_id' => $user->user_id, // Gunakan ID User yang baru dibuat
                'tendik_nama' => $request->tendik_nama,
                'tendik_nidn' => $request->tendik_nidn,
                'tendik_noHp' => $request->tendik_noHp,
            ]);

            // Respon sukses
            return response()->json([
                'status' => true,
                'message' => 'Data tendik berhasil ditambahkan!',
            ], 201);

        } catch (QueryException $e) {
            // Hapus user yang sudah terbuat jika terjadi error saat membuat tendik
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
                'tendik_nidn'        => 'required|string|max:10|unique:m_tendik,tendik_nidn',
                'tendik_nama'        => 'required|string|max:100',
                'prodi_id'          => 'required|integer|exists:m_prodi,prodi_id',
                'tendik_noHp'        => 'required|string|max:13',
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

                // Transaksi: Simpan Tendik
                $tendik = new TendikModel();
                $tendik->user_id = $user->user_id;
                $tendik->tendik_nidn = $request->input('tendik_nidn');
                $tendik->tendik_nama = $request->input('tendik_nama');
                $tendik->prodi_id = $request->input('prodi_id');
                $tendik->tendik_noHp = $request->input('tendik_noHp');
                $tendik->save();

                //DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data tendik berhasil disimpan!',
                ]);

            } catch (\Exception $e) {
                //DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data tendik: ' . $e->getMessage(),
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
            $tendik = TendikModel::with(['user'])->find($id);

            /*if (!$tendik) {
                abort(404, 'Data Tendik tidak ditemukan.');
            }*/

            return view('tendik.show_ajax', compact('tendik'));
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        if (request()->ajax()) {
            $tendik = TendikModel::find($id);
            //$prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown

            if (!$tendik) {
                abort(404, 'Data Tendik tidak ditemukan.');
            }

            return view('tendik.edit_ajax', compact('tendik'));
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $tendik = TendikModel::find($id);

            if (!$tendik) {
                return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            // Validasi: Pastikan username unik kecuali untuk user yang sedang diedit
            $rules = [
                'tendik_nidn'        => 'required|string|max:10|unique:m_tendik,tendik_nidn,' . $id . ',tendik_id',
                'tendik_nama'        => 'required|string|max:100',
                'tendik_noHp'        => 'required|string|max:13',
                'username'          => 'nullable|string|max:100|unique:m_user,username,' . $tendik->user_id . ',user_id',
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
                // Update Data Tendik
                $tendik->update([
                    'tendik_nidn' => $request->tendik_nidn,
                    'tendik_nama' => $request->tendik_nama,
                    'tendik_noHp' => $request->tendik_noHp,
                ]);

                // Update Data User (jika ada perubahan username atau password)
                $user = UserModel::find($tendik->user_id);
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
                    'message' => 'Data tendik berhasil diupdate!',
                ]);
            } catch (\Exception $e) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupdate data tendik: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        if (request()->ajax()) {
            $tendik = TendikModel::with('user')->find($id);

            if (!$tendik) {
                return response()->json(['status' => false, 'message' => 'Data tendik tidak ditemukan'], 404);
            }

            return view('tendik.confirm_ajax', compact('tendik'));
        }
        return redirect('/');
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $tendik = TendikModel::find($id);

                if (!$tendik) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tendik tidak ditemukan!'
                    ], 404);
                }

                $userId = $tendik->user_id;

                // 1. Hapus data Tendik
                $tendik->delete();

                // 2. Hapus data User terkait
                UserModel::destroy($userId);

                return response()->json([
                    'status' => true,
                    'message' => 'Data tendik berhasil dihapus!'
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
            return view('tendik.import');
        }
        return redirect('/');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_tendik' => 'required|file|mimes:xlsx,xls|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            
            $file = $request->file('file_tendik');
            
            try {
                $reader = IOFactory::createReaderForFile($file->getRealPath());
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                // 1. Get Level Tendik ID
                $levelTendik = LevelModel::where('level_kode', 'TDK')->first();
                if (!$levelTendik) {
                    return response()->json([
                         'status' => false,
                         'message' => 'Level Tendik (TDK) tidak ditemukan!'
                    ], 500);
                }
                $levelId = $levelTendik->level_id;

                // 2. Load all Prodi into an associative array for quick lookup [ 'Name' => 'id' ]
                //$prodiMap = ProdiModel::pluck('prodi_id', 'prodi_nama')->toArray();

                $insertedCount = 0;
                
                if (count($data) > 1) {
                    DB::beginTransaction(); // Start Transaction

                    foreach ($data as $row => $value) {
                         if ($row === 0) continue; // Skip header row

                         $nidn = isset($value[0]) ? trim($value[0]) : null;   // A
                         $nama = isset($value[1]) ? trim($value[1]) : null;   // B
                         $noHp = isset($value[2]) ? trim($value[2]) : null;   // C
                         
                         if (!$nidn || !$nama) {
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
                         if (TendikModel::where('tendik_nidn', $nidn)->exists()) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "NIDN $nidn sudah terdaftar! Impor dibatalkan.",
                             ], 400); 
                         }

                         // Lookup Prodi ID
                         /*$prodiId = $prodiMap[$prodiName] ?? null;
                         if (!$prodiId) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "Program Studi '$prodiName' tidak ditemukan! Pastikan nama sama persis dengan program studi terdaftar.",
                             ], 400);
                         }*/

                         // Create User
                         $user = UserModel::create([
                             'username' => $nidn,
                             'password' => Hash::make($nidn), // Default password = NIDN
                             'level_id' => $levelId,
                         ]);

                         // Create Tendik
                         TendikModel::create([
                             'user_id' => $user->user_id,
                             'tendik_nidn' => $nidn,
                             'tendik_nama' => $nama,
                             'tendik_noHp' => $noHp,
                         ]);

                         $insertedCount++;
                    }

                    DB::commit();

                    if ($insertedCount > 0) {
                        return response()->json([
                            'status' => true,
                            'message' => "$insertedCount data tendik berhasil diimpor!",
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Tidak ada data tendik yang diimpor (Mungkin file kosong atau format salah).',
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
