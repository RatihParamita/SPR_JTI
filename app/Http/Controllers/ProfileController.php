<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\DosenModel;
use App\Models\TendikModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\KelasModel;

class ProfileController extends Controller
{
    public function show_ajax()
    {
        $user = Auth::user();
        $data = null;
        $role = $user->getRole();

        switch ($role) {
            case 'ADM':
                $data = AdminModel::with('prodi')->where('user_id', $user->user_id)->first();
                break;
            case 'DSN':
                $data = DosenModel::with('prodi')->where('user_id', $user->user_id)->first();
                break;
            case 'TDK':
                $data = TendikModel::where('user_id', $user->user_id)->first();
                break;
            case 'MHS':
                $data = MahasiswaModel::with(['prodi', 'kelas'])->where('user_id', $user->user_id)->first();
                break;
        }

        return view('profile.show_ajax', compact('user', 'data', 'role'));
    }

    public function edit_ajax()
    {
        $user = Auth::user();
        $data = null;
        $role = $user->getRole();
        $prodi = ProdiModel::all();
        $kelas = KelasModel::all(); 

        switch ($role) {
            case 'ADM':
                $data = AdminModel::where('user_id', $user->user_id)->first();
                break;
            case 'DSN':
                $data = DosenModel::where('user_id', $user->user_id)->first();
                break;
            case 'TDK':
                $data = TendikModel::where('user_id', $user->user_id)->first();
                break;
            case 'MHS':
                $data = MahasiswaModel::where('user_id', $user->user_id)->first();
                break;
        }

        return view('profile.edit_ajax', compact('user', 'data', 'role', 'prodi', 'kelas'));
    }

    public function update_ajax(Request $request) 
    {
        $user = Auth::user();
        $role = $user->getRole();
        $userId = $user->user_id;

        // Validation Rules
        $rules = [
            'username' => 'string|min:3|unique:m_user,username,'.$userId.',user_id',
            'password' => 'nullable|min:5',
        ];

        // Role specific validation
        if ($role == 'ADM') {
            $rules['nama'] = 'string|max:100';
            $rules['nidn'] = 'string|max:20|unique:m_admin,admin_nidn,'. ($user->admin->admin_id ?? 0) .',admin_id';
            $rules['no_hp'] = 'nullable|string|max:15';
            //$rules['prodi_id'] = 'required|integer'; // Assuming Admin edits their prodi? Usually not, but let's see.
        } elseif ($role == 'DSN') {
            $rules['nama'] = 'string|max:100';
            $rules['nidn'] = 'string|max:20|unique:m_dosen,dosen_nidn,'. ($user->dosen->dosen_id ?? 0) .',dosen_id';
            $rules['no_hp'] = 'nullable|string|max:15';
        } elseif ($role == 'TDK') {
            $rules['nama'] = 'string|max:100';
            $rules['nidn'] = 'string|max:20|unique:m_tendik,tendik_nidn,'. ($user->tendik->tendik_id ?? 0) .',tendik_id';
            $rules['no_hp'] = 'nullable|string|max:15';
        } elseif ($role == 'MHS') {
            $rules['nama'] = 'string|max:100';
            $rules['nim'] = 'string|max:20|unique:m_mahasiswa,mahasiswa_nim,'. ($user->mahasiswa->mahasiswa_id ?? 0) .',mahasiswa_id';
            $rules['no_hp'] = 'nullable|string|max:15';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Update User Account
            $user->username = $request->username;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Update Profile Data
            if ($role == 'ADM') {
                $admin = AdminModel::where('user_id', $userId)->first();
                $admin->update([
                    //'admin_nama' => $request->nama,
                    //'admin_nidn' => $request->nidn,
                    'admin_noHp' => $request->no_hp,
                    // 'prodi_id' => $request->prodi_id // Optional if editable
                ]);
            } elseif ($role == 'DSN') {
                $dosen = DosenModel::where('user_id', $userId)->first();
                $dosen->update([
                    // 'dosen_nama' => $request->nama, // Readonly
                    // 'dosen_nidn' => $request->nidn, // Readonly
                    'dosen_noHp' => $request->no_hp,
                ]);
            } elseif ($role == 'TDK') {
                $tendik = TendikModel::where('user_id', $userId)->first();
                $tendik->update([
                    // 'tendik_nama' => $request->nama, // Readonly
                    // 'tendik_nidn' => $request->nidn, // Readonly
                    'tendik_noHp' => $request->no_hp,
                ]);
            } elseif ($role == 'MHS') {
                $mhs = MahasiswaModel::where('user_id', $userId)->first();
                $mhs->update([
                    // 'mahasiswa_nama' => $request->nama, // Readonly
                    // 'mahasiswa_nim' => $request->nim, // Readonly
                    'mahasiswa_noHp' => $request->no_hp,
                ]);
            }

            return response()->json([
                'status' => true,
                'msg' => 'Profil berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
