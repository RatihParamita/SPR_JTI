@extends('layouts.template')

@section('content')
<div class="card" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div class="card-header">
        {{-- <div class="card-title">{{ $page->title }}</div> --}}
        <div class="d-flex align-items-center w-100">
            {{-- Show Entries --}}
            <div class="col-md-3 col-sm-6 d-flex align-items-center mb-2 mb-md-0">
                <label class="col-form-label mr-2 mb-0" for="data-table-length">Show</label>
                <select class="form-control form-control-sm" id="data-table-length" style="width: 70px;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label class="col-form-label ml-2 mb-0">entries</label>
            </div>
            
            {{-- TOMBOL TAMBAH --}}
            <div class="card-tools ml-auto my-1">
                <button onclick="modalAction('{{ url('/ruangan/create_ajax') }}')" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Tabel Data Ruangan --}}
        <table class="table table-sm" id="table_ruangan">
            <thead style="background-color: #e3e3e3; font-color: #3F3F3F;">
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Fasilitas</th>
                    <th>Kuota</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody style="background-color: #ffffffff; font-color: #3F3F3F;">
                @foreach($ruangan as $key => $r)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ Str::limit($r->ruangan_nama, 30, '...') }}</td>
                    <td>{{ $r->ruangan_kode }}</td>
                    <td>{{ Str::limit($r->ruangan_fasilitas, 50, '...') }}</td>
                    <td>{{ $r->ruangan_kuota }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Aksi">
                            <button onclick="modalAction('{{ url('/ruangan/' . $r->ruangan_id . '/show_ajax') }}')" class="btn btn-outline-info btn-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="modalAction('{{ url('/ruangan/' . $r->ruangan_id . '/edit_ajax') }}')" class="btn btn-outline-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="modalAction('{{ url('/ruangan/' . $r->ruangan_id . '/confirm_ajax') }}')" class="btn btn-outline-danger btn-sm" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal untuk CRUD AJAX --}}
<div class="modal fade" id="modalRuangan" tabindex="-1" role="dialog" aria-labelledby="modalRuanganTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="modalSize">
        <div class="modal-content">
            {{-- Konten modal akan diisi oleh AJAX --}}
        </div>
    </div>
</div>

@endsection

@push('css')
    {{-- CSS tambahan --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        /* CSS opsional untuk DataTable */
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
@endpush

@push('js')
    {{-- JS Datatables --}}
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script>
    // Fungsi global untuk menampilkan modal
    function modalAction(url) {
        $('#modalSize').removeClass('modal-xl modal-lg modal-md modal-sm').addClass('modal-lg'); // Reset ukuran
            
        // Load konten modal
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#modalRuangan .modal-content').html(data);
                $('#modalRuangan').modal('show');
            },
            error: function(xhr) {
                alert('Gagal memuat modal. Cek console log.');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        console.log("Document ready!"); // DEBUG

        // Inisialisasi DataTables Client Side
        var dataRuangan = $('#table_ruangan').DataTable({
            autoWidth: false,
            responsive: true,
            dom: 'rtip', // Hide default search (f) and length (l), keep table (t), info (i), pagination (p)
        });
        
        console.log("DataTable initialized:", dataRuangan); // DEBUG

        // Event handler untuk Show Entries
        $('#data-table-length').change(function() {
            console.log("Length changed:", $(this).val()); // DEBUG
            dataRuangan.page.len($(this).val()).draw();
        });

    });

</script>
@endpush