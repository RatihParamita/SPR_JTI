<?php

namespace App\Http\Controllers;

use App\Models\ProdiModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class ProdiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Program Studi',
            'list' => ['Home', 'Daftar Program Studi']
        ];

        $page = (object) [
            'title' => 'Daftar program studi yang terdaftar dalam sistem'
        ];

        $activeMenu = 'prodi'; 

        $prodi = ProdiModel::all();

        return view('prodi.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'prodi' => $prodi]);
    }

    public function list(Request $request) 
    {
        try {
            $prodies = ProdiModel::select('prodi_id', 'prodi_kode', 'prodi_nama');

            return DataTables::of($prodies)
                ->addIndexColumn() 
                ->addColumn('aksi', function ($prodi) {
                    //$btn = '<button onclick="modalAction(\''.url('/prodi/' . $prodi->prodi_id . '/show_ajax').'\')" class="btn btn-outline-info btn-sm" title="Detail"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/prodi/' . $prodi->prodi_id . '/edit_ajax').'\')" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/prodi/' . $prodi->prodi_id . '/confirm_ajax').'\')" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
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

    public function create_ajax()
    {
        if (request()->ajax()) {
            return view('prodi.create_ajax');
        }
        return redirect('/');
    }

    public function store_ajax(Request $request)
    {
        // Aturan Validasi
        $validator = Validator::make($request->all(), [
            'prodi_kode' => 'required|string|max:5|unique:m_prodi,prodi_kode',
            'prodi_nama' => 'required|string|max:100',
        ], [
            'prodi_kode.required' => 'Kode program studi wajib diisi.',
            'prodi_kode.max' => 'Kode program studi maksimal 5 karakter.',
            'prodi_kode.unique' => 'Kode program studi sudah terdaftar.',
            'prodi_nama.required' => 'Nama program studi wajib diisi.',
            'prodi_nama.max' => 'Nama program studi maksimal 100 karakter.',
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
            ProdiModel::create([
                'prodi_kode' => $request->prodi_kode,
                'prodi_nama' => $request->prodi_nama,
            ]);

            // Respon sukses
            return response()->json([
                'status' => true,
                'message' => 'Data program studi berhasil ditambahkan!',
            ], 201);

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
        /*if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'ruangan_kode' => 'required|string|max:10|unique:m_ruangan,ruangan_kode',
                'ruangan_nama' => 'required|string|max:100',
                'ruangan_fasilitas' => 'nullable|string',
                'ruangan_kuota' => 'required|integer',
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
                RuanganModel::create([
                    'ruangan_kode' => $request->ruangan_kode,
                    'ruangan_nama' => $request->ruangan_nama,
                    'ruangan_fasilitas' => $request->ruangan_fasilitas,
                    'ruangan_kuota' => $request->ruangan_kuota,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data ruangan berhasil disimpan!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');*/
    }

    /*public function show_ajax(string $id)
    {
        if (request()->ajax()) {
            $prodi = ProdiModel::find($id);

            if (!$prodi) {
                return response()->json(['status' => false, 'message' => 'Data program studi tidak ditemukan!'], 404);
            }

            return view('prodi.show_ajax', compact('prodi'));
        }
        return redirect('/');
    }*/

    public function edit_ajax(string $id)
    {
        if (request()->ajax()) {
            $prodi = ProdiModel::find($id);

            if (!$prodi) {
                return response()->json(['status' => false, 'message' => 'Data program studi tidak ditemukan!'], 404);
            }

            return view('prodi.edit_ajax', compact('prodi'));
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'prodi_kode' => 'required|string|max:5|unique:m_prodi,prodi_kode,'.$id.',prodi_id',
                'prodi_nama' => 'required|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $prodi = ProdiModel::find($id);

            if (!$prodi) {
                return response()->json(['status' => false, 'message' => 'Data program studi tidak ditemukan!'], 404);
            }

            try {
                $prodi->update([
                    'prodi_kode' => $request->prodi_kode,
                    'prodi_nama' => $request->prodi_nama,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data program studi berhasil diupdate!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupdate data program studi: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        if (request()->ajax()) {
            $prodi = ProdiModel::find($id);

            if (!$prodi) {
                return response()->json(['status' => false, 'message' => 'Data program studi tidak ditemukan!'], 404);
            }

            return view('prodi.confirm_ajax', compact('prodi'));
        }
        return redirect('/');
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $prodi = ProdiModel::find($id);

            if (!$prodi) {
                 return response()->json(['status' => false, 'message' => 'Data program studi tidak ditemukan!'], 404);
            }

            try {
                $prodi->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data program studi berhasil dihapus!'
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
