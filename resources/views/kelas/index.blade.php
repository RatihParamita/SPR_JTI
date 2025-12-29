@extends('layouts.template')

@section('content')
<div class="card" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div class="card-header">
        {{-- <div class="card-title">{{ $page->title }}</div> --}}
        <div class="d-flex align-items-center w-100">
            {{-- FILTER PROGRAM STUDI --}}
            <div class="d-flex align-items-center mx-3">
                <label class="col-form-label mr-2 mb-0">Filter:</label>
                <select class="form-control form-control-sm" id="kelas_prodi" name="kelas_prodi" style="width: 200px;">
                    <option value="">- Semua Program Studi -</option>
                    {{-- Perbaikan: Menggunakan prodi_kode sebagai value untuk pencarian --}}
                    @foreach($prodiList as $prodi)
                        <option value="{{ $prodi->prodi_id }}">{{ $prodi->prodi_nama }}</option>
                    @endforeach
                </select>
            </div>
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
                <button onclick="modalAction('{{ url('/kelas/create_ajax') }}')" class="btn btn-success btn-sm">
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

        {{-- Tabel Data Kelas --}}
        <table class="table table-sm" id="table_kelas">
            <thead style="background-color: #e3e3e3; font-color: #3F3F3F;">
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Program Studi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody style="background-color: #ffffffff; font-color: #3F3F3F;">
                @foreach($kelas as $key => $k)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $k->kelas_nama }}</td>
                    <td>{{ Str::limit($k->prodi ? $k->prodi->prodi_nama : '-', 70) }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Aksi">
                            <button onclick="modalAction('{{ url('/kelas/' . $k->kelas_id . '/edit_ajax') }}')" class="btn btn-outline-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="modalAction('{{ url('/kelas/' . $k->kelas_id . '/confirm_ajax') }}')" class="btn btn-outline-danger btn-sm" title="Hapus">
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
<div class="modal fade" id="modalKelas" tabindex="-1" role="dialog" aria-labelledby="modalKelasTitle" aria-hidden="true">
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
                $('#modalKelas .modal-content').html(data);
                $('#modalKelas').modal('show');
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
        var dataKelas = $('#table_kelas').DataTable({
            autoWidth: false,
            responsive: true,
            dom: 'rtip', // Hide default search (f) and length (l), keep table (t), info (i), pagination (p)
            columnDefs: [
                { orderable: false, targets: 3 }
            ],
        });
        
        console.log("DataTable initialized:", dataKelas); // DEBUG

        // Event handler untuk Show Entries
        $('#data-table-length').change(function() {
            console.log("Length changed:", $(this).val()); // DEBUG
            dataKelas.page.len($(this).val()).draw();
        });

        // Event handler untuk Filter Prodi
        $('#kelas_prodi').on('change', function() {
            var value = $(this).val();
            var text = $(this).find('option:selected').text();
            
            if (value === "") {
                dataKelas.column(2).search('').draw(); // Clear filter
            } else {
                dataKelas.column(2).search(text).draw(); // Filter by Prodi Name (Column 2)
            }
        });

    });

</script>
@endpush