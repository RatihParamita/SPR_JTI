<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\KelasModel;
use App\Models\UserModel;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MahasiswaController extends Controller
{
    //CLIENT-SIDE
    public function index() {

        $breadcrumb = (object) [
            'title' => 'Daftar Mahasiswa',
            'list' => ['Home','Daftar Mahasiswa']
         ];

         $page = (object) [
            'title' => 'Mahasiswa yang terdaftar dalam sistem'
         ];

         $activeMenu = 'mahasiswa'; //set menu yang sedang aktif
         $prodiList = ProdiModel::all();
         $kelasList = KelasModel::all();
         $mahasiswas = MahasiswaModel::with(['user', 'prodi', 'kelas'])->get(); // Fetch all mahasiswas for client-side rendering

         return view('mahasiswa.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'prodiList' => $prodiList, 'kelasList' => $kelasList, 'mahasiswas' => $mahasiswas]);
    }

    public function list(Request $request)
    {
        try {
            $mahasiswas = MahasiswaModel::select('m_mahasiswa.*')->with('prodi'); 

            // FILTER BERDASARKAN PRODI (prodi_id)
            if ($request->has('prodi_id') && $request->prodi_id != '') {
                $mahasiswas->where('prodi_id', $request->prodi_id);
            }

            return DataTables::of($mahasiswas)
                ->addIndexColumn() 
                ->addColumn('prodi_nama', function ($mahasiswa) {
                    return $mahasiswa->prodi ? $mahasiswa->prodi->prodi_nama : '-';
                })
                ->addColumn('aksi', function ($mahasiswa) {
                    $btn = '<button onclick="modalAction(\''.url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/show_ajax').'\')" class="btn btn-outline-info btn-sm" title="Detail"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/edit_ajax').'\')" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/confirm_ajax').'\')" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
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
        $kelasList = KelasModel::all();
        // Cek apakah request datang dari AJAX. Jika tidak, redirect ke halaman utama.
        if ($request->ajax() || $request->wantsJson()) {
            return view('mahasiswa.create_ajax', compact('prodiList', 'kelasList'));
        }
        return redirect('/');
    }

    public function store_ajax(Request $request)
    {
        // Tentukan Level ID untuk Mahasiswa (Asumsi Level Kode 'MHS' memiliki ID tertentu)
        $levelMahasiswa = LevelModel::where('level_kode', 'MHS')->first();
        if (!$levelMahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Level Mahasiswa (MHS) tidak ditemukan dalam database!'
            ], 500);
        }
        $levelId = $levelMahasiswa->level_id;

        // Aturan Validasi
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:m_user,username',
            'password' => 'required|string|min:6|max:100',
            'mahasiswa_nim' => 'required|string|max:10|unique:m_mahasiswa,mahasiswa_nim',
            'mahasiswa_nama' => 'required|string|max:100',
            'prodi_id' => 'required|integer|exists:m_prodi,prodi_id',
            'kelas_id' => 'required|integer|exists:m_kelas,kelas_id',
            'mahasiswa_noHp' => 'nullable|string|max:13',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'mahasiswa_nim.required' => 'NIM wajib diisi.',
            'mahasiswa_nim.max' => 'NIM maksimal 10 karakter.',
            'mahasiswa_nim.unique' => 'NIM sudah terdaftar.',
            'mahasiswa_nama.required' => 'Nama Mahasiswa wajib diisi.',
            'prodi_id.required' => 'Program Studi wajib dipilih.',
            'prodi_id.exists' => 'Program Studi tidak valid.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',
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

            // 2. Simpan data ke tabel m_mahasiswa
            MahasiswaModel::create([
                'user_id' => $user->user_id, // Gunakan ID User yang baru dibuat
                'prodi_id' => $request->prodi_id,
                'kelas_id' => $request->kelas_id,
                'mahasiswa_nama' => $request->mahasiswa_nama,
                'mahasiswa_nim' => $request->mahasiswa_nim,
                'mahasiswa_noHp' => $request->mahasiswa_noHp,
            ]);

            // Respon sukses
            return response()->json([
                'status' => true,
                'message' => 'Data mahasiswa berhasil ditambahkan!',
            ], 201);

        } catch (QueryException $e) {
            // Hapus user yang sudah terbuat jika terjadi error saat membuat mahasiswa
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
        
    }

    public function show_ajax(string $id)
    {
        if (request()->ajax()) {
            // Relasi prodi, kelas, dan user diload
            $mahasiswa = MahasiswaModel::with(['user', 'prodi', 'kelas'])->find($id);

            if (!$mahasiswa) {
                abort(404, 'Data mahasiswa tidak ditemukan!');
            }

            return view('mahasiswa.show_ajax', compact('mahasiswa'));
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        if (request()->ajax()) {
            $mahasiswa = MahasiswaModel::find($id);
            $prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown
            $kelasList = KelasModel::all(); // Ambil semua data kelas untuk dropdown

            if (!$mahasiswa) {
                abort(404, 'Data Mahasiswa tidak ditemukan!');
            }

            return view('mahasiswa.edit_ajax', compact('mahasiswa', 'prodiList', 'kelasList'));
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $mahasiswa = MahasiswaModel::find($id);

            if (!$mahasiswa) {
                return response()->json(['status' => false, 'message' => 'Data mahasiswa tidak ditemukan!'], 404);
            }

            // Validasi: Pastikan username unik kecuali untuk user yang sedang diedit
            $rules = [
                'mahasiswa_nim'        => 'required|string|max:10|unique:m_mahasiswa,mahasiswa_nim,' . $id . ',mahasiswa_id',
                'mahasiswa_nama'        => 'required|string|max:100',
                'prodi_id'          => 'required|integer|exists:m_prodi,prodi_id',
                'kelas_id'          => 'required|integer|exists:m_kelas,kelas_id',
                'mahasiswa_noHp'        => 'required|string|max:13',
                'username'          => 'nullable|string|max:100|unique:m_user,username,' . $mahasiswa->user_id . ',user_id',
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
                // Update Data Mahasiswa
                $mahasiswa->update([
                    'mahasiswa_nim' => $request->mahasiswa_nim,
                    'mahasiswa_nama' => $request->mahasiswa_nama,
                    'prodi_id'   => $request->prodi_id,
                    'kelas_id'   => $request->kelas_id,
                    'mahasiswa_noHp' => $request->mahasiswa_noHp,
                ]);

                // Update Data User (jika ada perubahan username atau password)
                $user = UserModel::find($mahasiswa->user_id);
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
                    'message' => 'Data mahasiswa berhasil diupdate!',
                ]);
            } catch (\Exception $e) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupdate data mahasiswa: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        if (request()->ajax()) {
            $mahasiswa = MahasiswaModel::with('user', 'prodi', 'kelas')->find($id);

            if (!$mahasiswa) {
                return response()->json(['status' => false, 'message' => 'Data mahasiswa tidak ditemukan!'], 404);
            }

            return view('mahasiswa.confirm_ajax', compact('mahasiswa'));
        }
        return redirect('/');
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $mahasiswa = MahasiswaModel::find($id);

                if (!$mahasiswa) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data mahasiswa tidak ditemukan!'
                    ], 404);
                }

                $userId = $mahasiswa->user_id;

                // 1. Hapus data Mahasiswa
                $mahasiswa->delete();

                // 2. Hapus data User terkait
                UserModel::destroy($userId);

                return response()->json([
                    'status' => true,
                    'message' => 'Data mahasiswa berhasil dihapus!'
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
            return view('mahasiswa.import');
        }
        return redirect('/');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_mahasiswa' => 'required|file|mimes:xlsx,xls|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            
            $file = $request->file('file_mahasiswa');
            
            try {
                $reader = IOFactory::createReaderForFile($file->getRealPath());
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                // 1. Get Level Mahasiswa ID
                $levelMahasiswa = LevelModel::where('level_kode', 'MHS')->first();
                if (!$levelMahasiswa) {
                    return response()->json([
                         'status' => false,
                         'message' => 'Level Mahasiswa (MHS) tidak ditemukan!'
                    ], 500);
                }
                $levelId = $levelMahasiswa->level_id;

                // 2. Load all Prodi into an associative array for quick lookup [ 'Name' => 'id' ]
                $prodiMap = ProdiModel::pluck('prodi_id', 'prodi_nama')->toArray();

                // 3. Load all Kelas into an associative array for quick lookup [ 'Name' => 'id' ]
                $kelasMap = KelasModel::pluck('kelas_id', 'kelas_nama')->toArray();

                $insertedCount = 0;
                
                if (count($data) > 1) {
                    DB::beginTransaction(); // Start Transaction

                    foreach ($data as $row => $value) {
                         if ($row === 0) continue; // Skip header row

                         $nim = isset($value[0]) ? trim($value[0]) : null;   // A
                         $nama = isset($value[1]) ? trim($value[1]) : null;   // B
                         $prodiName = isset($value[2]) ? trim($value[2]) : null; // C
                         $kelasName = isset($value[3]) ? trim($value[3]) : null;   // D
                         $noHp = isset($value[4]) ? trim($value[4]) : null;   // E
                         
                         if (!$nim || !$nama || !$prodiName || !$kelasName) {
                             continue; // Skip invalid rows
                         }

                         // Check duplication
                         if (UserModel::where('username', $nim)->exists()) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "Username/NIM $nim sudah terdaftar! Impor dibatalkan.",
                             ], 400); 
                         }
                         if (MahasiswaModel::where('mahasiswa_nim', $nim)->exists()) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "NIM $nim sudah terdaftar! Impor dibatalkan.",
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

                         // Lookup Kelas ID
                         $kelasId = $kelasMap[$kelasName] ?? null;
                         if (!$kelasId) {
                             DB::rollBack();
                             return response()->json([
                                 'status' => false,
                                 'message' => "Kelas '$kelasName' tidak ditemukan! Pastikan nama sama persis dengan kelas terdaftar.",
                             ], 400);
                         }

                         // Create User
                         $user = UserModel::create([
                             'username' => $nim,
                             'password' => Hash::make($nim), // Default password = NIM
                             'level_id' => $levelId,
                         ]);

                         // Create Mahasiswa
                         MahasiswaModel::create([
                             'user_id' => $user->user_id,
                             'prodi_id' => $prodiId,
                             'kelas_id' => $kelasId,
                             'mahasiswa_nim' => $nim,
                             'mahasiswa_nama' => $nama,
                             'mahasiswa_noHp' => $noHp,
                         ]);

                         $insertedCount++;
                    }

                    DB::commit();

                    if ($insertedCount > 0) {
                        return response()->json([
                            'status' => true,
                            'message' => "$insertedCount data mahasiswa berhasil diimpor!",
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Tidak ada data mahasiswa yang diimpor (Mungkin file kosong atau format salah).',
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
    public function get_kelas_by_prodi($prodi_id)
    {
        $kelasList = KelasModel::where('prodi_id', $prodi_id)->get();
        // Sort specifically by kelas_nama naturally to handle 1B, 3C, 10A correctly
        $sortedKelas = $kelasList->sortBy('kelas_nama', SORT_NATURAL)->values();
        return response()->json($sortedKelas);
    }
}
