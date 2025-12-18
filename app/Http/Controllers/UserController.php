<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LevelModel;
use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\DosenModel;
use App\Models\TendikModel;
use App\Models\MahasiswaModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    public function index() {
         $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home','User']
         ];

         $page = (object) [
            'title' => 'Daftar user yang ada dalam sistem'
         ];

         $activeMenu = 'user'; //set menu yang sedang aktif

         $level = LevelModel::all(); //mengambil data level untuk filtering level

         return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'level_id', 'username')
            ->with('level')
            ->addSelect([
                'nama' => function ($query) {
                    $query->selectRaw("
                        CASE
                            WHEN level_id = 1 THEN (SELECT admin_nama FROM m_admin WHERE m_admin.user_id = m_user.user_id)
                            WHEN level_id = 2 THEN (SELECT dosen_nama FROM m_dosen WHERE m_dosen.user_id = m_user.user_id)
                            WHEN level_id = 3 THEN (SELECT tendik_nama FROM m_tendik WHERE m_tendik.user_id = m_user.user_id)
                            WHEN level_id = 4 THEN (SELECT mahasiswa_nama FROM m_mahasiswa WHERE m_mahasiswa.user_id = m_user.user_id)
                            ELSE NULL
                        END
                    ");
                }
            ]);

        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }
        
        return DataTables::of($users)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $search = $request->get('search')['value']) {
                    $query->where(function ($query) use ($search) {
                        $query->where('username', 'like', "%{$search}%")
                            ->orWhereRaw("CASE
                                    WHEN level_id = 1 THEN (SELECT admin_nama FROM m_admin WHERE m_admin.user_id = m_user.user_id)
                                    WHEN level_id = 2 THEN (SELECT dosen_nama FROM m_dosen WHERE m_dosen.user_id = m_user.user_id)
                                    WHEN level_id = 3 THEN (SELECT tendik_nama FROM m_tendik WHERE m_tendik.user_id = m_user.user_id)
                                    WHEN level_id = 4 THEN (SELECT mahasiswa_nama FROM m_mahasiswa WHERE m_mahasiswa.user_id = m_user.user_id)
                                    ELSE NULL
                                END LIKE ?", ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('nama', function ($user) {
                return $user->nama; 
            })
            ->addColumn('aksi', function ($user) {
                $btn = '';
                    $btn = '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                
                $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    
    public function create_ajax() {
        $level = LevelModel::select('level_id', 'level_nama') -> get();

        return view('user.create_ajax') -> with('level', $level);
    }

    public function create_detail_ajax(Request $request) {
        $user_id = $request->session()->get('user_id');
        $level_id = $request->session()->get('level_id');

        $user = UserModel::find($user_id); 

        return view('user.create_detail_ajax', compact('user', 'level_id'));
    }

    public function store_ajax(Request $request) {

        if ($request -> ajax() || $request -> wantsJson()) {
            $rules = [
                'level_id'  => 'required|integer',
                'username'  => 'required|string|min:3|unique:m_user,username',
                'password'  => 'required|min:6',
                
            ];

            $validator = Validator::make($request -> all(),$rules);

            if ($validator -> fails()) {
                return response() -> json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $user = UserModel::create($request->all());
            $user_id = $user -> user_id;

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Lanjutkan dengan mengisi detail user.',
                    'user_id' => $user_id,
                    'level_id' => $request->level_id,
                ]);
            }
            
            return redirect('user/create_detail_ajax')->with([
                'user_id'   => $user_id,
                'level_id'  => $request->level_id
            ]);
        }

    }

    public function show_ajax(string $id) {
        $user = UserModel::with(['admin', 'dosen', 'tendik','mahasiswa'])->find($id);
        $level = LevelModel::select('level_id', 'level_nama') -> get();
        return view('user.show_ajax', ['user' => $user, 'level' => $level]);
    }
    


    public function edit_ajax(string $id) {
        $user = UserModel::with(['admin', 'dosen', 'tendik','mahasiswa'])->find($id);
        $level = LevelModel::select('level_id', 'level_nama', 'level_kode') -> get();

        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($id);
            $role = null;
            $rules = [];
    
            if ($user) {
                $levelKode = $user->level->level_kode;
    
                if ($levelKode === 'ADM') {
                    $role = AdminModel::where('user_id', $user->user_id)->first();
                    $rules = [
                        'password' => 'nullable|min:6|max:50',
                        'admin_prodi' => 'required',
                        'admin_noHp' => 'required'
                    ];
                } elseif ($levelKode === 'DSN') {
                    $role = DosenModel::where('user_id', $user->user_id)->first();
                    $rules = [
                        'password' => 'nullable|min:6|max:50',
                        'dosen_noHp' => 'required'
                    ];
                } elseif ($levelKode === 'TDK') {
                    $role = TendikModel::where('user_id', $user->user_id)->first();
                    $rules = [
                        'password' => 'nullable|min:6|max:50',
                        'tendik_noHp' => 'required'
                    ];
                } elseif ($levelKode === 'MHS') {
                    $role = MahasiswaModel::where('user_id', $user->user_id)->first();
                    $rules = [
                        'password' => 'nullable|min:6|max:50',
                        'mahasiswa_noHp' => 'required'
                    ];
                }
            }
    
            // If rules are empty, it means no valid level_kode was found, exit early
            if (empty($rules)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Level tidak dikenali atau data tidak ditemukan.',
                ]);
            }
    
            // Validate the request
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }
    
            // Update Role Data
            if ($role) {
                $updateData = [];
                
                if ($levelKode === 'ADM') {
                    $updateData = [
                        'admin_nama'  => $request->admin_nama,
                        'admin_prodi' => $request->admin_prodi,
                        'admin_noHp'  => $request->admin_noHp,
                    ];
                } elseif ($levelKode === 'DSN') {
                    $updateData = [
                        'dosen_noHp'  => $request->dosen_noHp,
                    ];
                } elseif ($levelKode === 'TDK') {
                    $updateData = [
                        'tendik_noHp' => $request->tendik_noHp,
                    ];
                } elseif ($levelKode === 'MHS') {
                    $updateData = [
                        'mahasiswa_noHp' => $request->mahasiswa_noHp,
                    ];
                }
    
                $role->update($updateData);
            }
    
            // Update User Data
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
    
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diedit!',
            ]);
        }
    
        return redirect('/');
    }

    public function confirm_ajax(string $id) {
        $user = UserModel::find($id);

        return view('user.confirm_ajax', ['user' => $user]);
    } 

    public function delete_ajax(Request $request, $id) {
        if ($request -> ajax() || $request -> wantsJson()) {
            $user = UserModel::find($id);

            if ($user) {
                $user->delete();
                return response() -> json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus!'
                ]);
            } else {
                return response() -> json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan!'
                ]);
            }
        }
        return redirect('/');
    }

    public function import() {
        return view('user.import');
    }

    public function import_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
        
        $rules = [
            'file_user' => ['required', 'mimes:xlsx', 'max:1024'], 
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }

        $file = $request->file('file_user');

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true); 
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $data = $sheet->toArray(null, false, true, true);

            $insert = [];

            if (count($data) > 1) { 
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { 
                        $insert[] = [
                            'level_id'=> $value['A'],
                            'username' => $value['B'],
                            'nama' => $value['C'],
                            'password' => $value['D'],
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    UserModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport!'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport.'
                ]);
            }

        }   catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
                ]);
            }
        }

        return redirect('/');
    }

    /*public function import_pfp() {
        return view('import_pfp');
    }
 

    public function import_ajax_pfp(Request $request)
    {
        $request->validate([
            'file_pfp' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);
    
        $file = $request->file('file_pfp');
    
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = public_path('images/pfp');
    
        $file->move($path, $filename);
    
        $user = auth()->user();
        $user->profile_picture = $filename;
        $user->save();
        
        return redirect('/');
    }


    public function edit_profile()
    {
        return view('edit_profile', ['user' => auth()->user()]);
    }
    
    
    public function edit_profile_save(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'password' => ['nullable', 'confirmed'], // Ensure 'password_confirmation' exists in the form
        ]);

        // Update name
        $user->nama = $request->nama;

        // Update password 
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Profile updated successfully']);
        }

        return redirect('/')->with('status', 'Profile updated successfully!');
    }*/
}
