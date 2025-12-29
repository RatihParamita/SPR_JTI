<?php

namespace App\Http\Controllers;

use App\Models\RuanganModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class RuanganController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Ruangan',
            'list' => ['Home', 'Daftar Ruangan']
        ];

        $page = (object) [
            'title' => 'Daftar ruangan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'ruangan'; 

        $ruangan = RuanganModel::all();

        return view('ruangan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'ruangan' => $ruangan]);
    }

    public function list(Request $request) 
    {
        try {
            $ruangans = RuanganModel::select('ruangan_id', 'ruangan_kode', 'ruangan_nama', 'ruangan_fasilitas', 'ruangan_kuota');

            return DataTables::of($ruangans)
                ->addIndexColumn() 
                ->addColumn('aksi', function ($ruangan) {
                    $btn = '<button onclick="modalAction(\''.url('/ruangan/' . $ruangan->ruangan_id . '/show_ajax').'\')" class="btn btn-outline-info btn-sm" title="Detail"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/ruangan/' . $ruangan->ruangan_id . '/edit_ajax').'\')" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/ruangan/' . $ruangan->ruangan_id . '/confirm_ajax').'\')" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
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
            return view('ruangan.create_ajax');
        }
        return redirect('/');
    }

    public function store_ajax(Request $request)
    {
        // Aturan Validasi
        $validator = Validator::make($request->all(), [
            'ruangan_kode' => 'required|string|max:10|unique:m_ruangan,ruangan_kode',
            'ruangan_nama' => 'required|string|max:100',
            'ruangan_fasilitas' => 'nullable|string',
            'ruangan_kuota' => 'required|integer',
        ], [
            'ruangan_kode.required' => 'Kode ruangan wajib diisi.',
            'ruangan_kode.max' => 'Kode ruangan maksimal 10 karakter.',
            'ruangan_kode.unique' => 'Kode ruangan sudah terdaftar.',
            'ruangan_nama.required' => 'Nama ruangan wajib diisi.',
            'ruangan_nama.max' => 'Nama ruangan maksimal 100 karakter.',
            'ruangan_kuota.required' => 'Kuota ruangan wajib diisi.',
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
            RuanganModel::create([
                'ruangan_kode' => $request->ruangan_kode,
                'ruangan_nama' => $request->ruangan_nama,
                'ruangan_fasilitas' => $request->ruangan_fasilitas,
                'ruangan_kuota' => $request->ruangan_kuota,
            ]);

            // Respon sukses
            return response()->json([
                'status' => true,
                'message' => 'Data ruangan berhasil ditambahkan!',
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

    public function show_ajax(string $id)
    {
        if (request()->ajax()) {
            $ruangan = RuanganModel::find($id);

            if (!$ruangan) {
                return response()->json(['status' => false, 'message' => 'Data ruangan tidak ditemukan!'], 404);
            }

            return view('ruangan.show_ajax', compact('ruangan'));
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        if (request()->ajax()) {
            $ruangan = RuanganModel::find($id);

            if (!$ruangan) {
                return response()->json(['status' => false, 'message' => 'Data ruangan tidak ditemukan!'], 404);
            }

            return view('ruangan.edit_ajax', compact('ruangan'));
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'ruangan_kode' => 'required|string|max:10|unique:m_ruangan,ruangan_kode,'.$id.',ruangan_id',
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

            $ruangan = RuanganModel::find($id);

            if (!$ruangan) {
                return response()->json(['status' => false, 'message' => 'Data ruangan tidak ditemukan!'], 404);
            }

            try {
                $ruangan->update([
                    'ruangan_kode' => $request->ruangan_kode,
                    'ruangan_nama' => $request->ruangan_nama,
                    'ruangan_fasilitas' => $request->ruangan_fasilitas,
                    'ruangan_kuota' => $request->ruangan_kuota,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data ruangan berhasil diupdate!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupdate data ruangan: ' . $e->getMessage(),
                    'msgField' => []
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        if (request()->ajax()) {
            $ruangan = RuanganModel::find($id);

            if (!$ruangan) {
                return response()->json(['status' => false, 'message' => 'Data ruangan tidak ditemukan!'], 404);
            }

            return view('ruangan.confirm_ajax', compact('ruangan'));
        }
        return redirect('/');
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $ruangan = RuanganModel::find($id);

            if (!$ruangan) {
                 return response()->json(['status' => false, 'message' => 'Data ruangan tidak ditemukan!'], 404);
            }

            try {
                $ruangan->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data ruangan berhasil dihapus!'
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
