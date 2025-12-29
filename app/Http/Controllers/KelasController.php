<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KelasModel;
use App\Models\ProdiModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class KelasController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kelas',
            'list' => ['Home', 'Daftar Kelas']
        ];

        $page = (object) [
            'title' => 'Daftar kelas yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kelas'; 

        $kelas = KelasModel::select('m_kelas.*')
            ->join('m_prodi', 'm_kelas.prodi_id', '=', 'm_prodi.prodi_id')
            ->orderBy('m_prodi.prodi_nama', 'asc')
            ->orderByRaw('CAST(m_kelas.kelas_nama AS UNSIGNED) ASC')
            ->orderBy('m_kelas.kelas_nama', 'asc')
            ->with('prodi')
            ->get();
            
        $prodiList = ProdiModel::all();

        return view('kelas.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'kelas' => $kelas, 'prodiList' => $prodiList]);
    }

    public function list(Request $request)
    {
        try {
            $kelas = KelasModel::select('m_kelas.*')
                ->join('m_prodi', 'm_kelas.prodi_id', '=', 'm_prodi.prodi_id')
                ->orderBy('m_prodi.prodi_nama', 'asc')
                ->orderByRaw('CAST(m_kelas.kelas_nama AS UNSIGNED) ASC')
                ->orderBy('m_kelas.kelas_nama', 'asc')
                ->with('prodi');

            // FILTER BERDASARKAN PRODI (prodi_id)
            if ($request->has('prodi_id') && $request->prodi_id != '') {
                $kelas->where('m_kelas.prodi_id', $request->prodi_id);
            }

            return DataTables::of($kelas)
                ->addIndexColumn() 
                ->addColumn('prodi_nama', function ($kelas) {
                    return $kelas->prodi ? $kelas->prodi->prodi_nama : '-';
                })
                ->addColumn('aksi', function ($kelas) {
                    $btn = '<button onclick="modalAction(\'' . url('/kelas/' . $kelas->kelas_id . '/show_ajax') . '\')" class="btn btn-outline-info btn-sm" title="Detail"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button onclick="modalAction(\'' . url('/kelas/' . $kelas->kelas_id . '/edit_ajax') . '\')" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\'' . url('/kelas/' . $kelas->kelas_id . '/confirm_ajax') . '\')" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
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
            return view('kelas.create_ajax', compact('prodiList'));
        }
        return redirect('/');
    }

    public function store_ajax(Request $request)
    {
        // Aturan Validasi
        $validator = Validator::make($request->all(), [
            'kelas_nama' => 'required|string|max:5',
            'prodi_id' => 'required|integer|exists:m_prodi,prodi_id',
        ], [
            'kelas_nama.required' => 'Nama Kelas wajib diisi.',
            'kelas_nama.max' => 'Nama Kelas maksimal 5 karakter.',
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
            // Simpan data ke tabel m_kelas
            $kelas = KelasModel::create([
                'kelas_nama' => $request->kelas_nama,
                'prodi_id' => $request->prodi_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data kelas berhasil disimpan!',
                'data' => $kelas
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Penyimpanan data gagal. Error Database: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit_ajax($id)
    {
        if (request()->ajax()) {
            $kelas = KelasModel::find($id);
            $prodiList = ProdiModel::all(); // Ambil semua data prodi untuk dropdown

            if (!$kelas) {
                abort(404, 'Data kelas tidak ditemukan!');
            }

            return view('kelas.edit_ajax', compact('kelas', 'prodiList'));
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $kelas = KelasModel::find($id);

            if (!$kelas) {
                return response()->json(['status' => false, 'message' => 'Data kelas tidak ditemukan!'], 404);
            }

            // Validasi: Pastikan kelas unik kecuali untuk user yang sedang diedit
            $rules = [
                'kelas_nama'        => 'required|string|max:5|unique:m_kelas,kelas_nama,' . $id . ',kelas_id',
                'prodi_id'          => 'required|integer|exists:m_prodi,prodi_id',
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
                // Update Data Kelas
                $kelas->update([
                    'kelas_nama' => $request->kelas_nama,
                    'prodi_id'   => $request->prodi_id,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data kelas berhasil diupdate!',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupdate data kelas: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax($id)
    {
        if (request()->ajax()) {
            $kelas = KelasModel::with('prodi')->find($id);

            if (!$kelas) {
                return response()->json(['status' => false, 'message' => 'Data kelas tidak ditemukan!'], 404);
            }

            return view('kelas.confirm_ajax', compact('kelas'));
        }
        return redirect('/');
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $kelas = KelasModel::find($id);

            if (!$kelas) {
                 return response()->json(['status' => false, 'message' => 'Data kelas tidak ditemukan!'], 404);
            }

            try {
                $kelas->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data kelas berhasil dihapus!'
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
