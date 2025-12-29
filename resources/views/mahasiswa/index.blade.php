@extends('layouts.template')

@section('content')
<div class="card" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div class="card-header">
        {{-- <h3 class="card-title">{{ $page->title }}</h3> --}}
        <div class="d-flex align-items-center w-100">
            {{-- FILTER PROGRAM STUDI --}}
            <div class="d-flex align-items-center mx-3">
                <label class="col-form-label mr-2 mb-0">Filter:</label>
                <select class="form-control form-control-sm" id="mahasiswa_prodi" name="mahasiswa_prodi" style="width: 200px;">
                    <option value="">- Semua Program Studi -</option>
                    {{-- Perbaikan: Menggunakan prodi_kode sebagai value untuk pencarian --}}
                    @foreach($prodiList as $prodi)
                        <option value="{{ $prodi->prodi_kode }}">{{ $prodi->prodi_nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- SEARCH BAR --}}
            <div class="d-flex align-items-center mx-3">
                <label class="col-form-label mr-2 mb-0">Cari:</label>
                <input type="text" class="form-control form-control-sm" id="mahasiswa_search" name="mahasiswa_search" placeholder="Cari berdasarkan nama">
                <button type="button" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i>
                </button>
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
            
            {{-- TOMBOL TAMBAH & IMPOR --}}
            <div class="card-tools ml-auto my-1">
                <button onclick="modalAction('{{ url('/mahasiswa/create_ajax') }}')" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah
                </button>
                <button onclick="modalAction('{{ url('/mahasiswa/import') }}')" class="btn btn-sm btn-import">
                    <i class="fas fa-upload"></i> Impor
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

        {{-- Tabel Data Mahasiswa --}}
        <table class="table table-sm" id="table_mahasiswa">
            <thead style="background-color: #e3e3e3; font-color: #3F3F3F;">
                <tr>
                    <th>No.</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Program Studi</th>
                    <th>Kelas</th>
                    <th>Nomor HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody style="background-color: #ffffffff; font-color: #3F3F3F;">
                @foreach($mahasiswas as $key => $mahasiswa)
                <tr>
                    <td>{{ $key + 1 }}.</td>
                    <td>{{ $mahasiswa->mahasiswa_nim }}</td>
                    <td>{{ Str::limit($mahasiswa->mahasiswa_nama, 50, '...') }}</td>
                    <td>{{ $mahasiswa->prodi ? $mahasiswa->prodi->prodi_kode : '-' }}</td>
                    <td>{{ $mahasiswa->kelas ? $mahasiswa->kelas->kelas_nama : '-' }}</td>
                    <td>{{ $mahasiswa->mahasiswa_noHp }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Aksi">
                            <button onclick="modalAction('{{ url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/show_ajax') }}')" class="btn btn-outline-info btn-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="modalAction('{{ url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/edit_ajax') }}')" class="btn btn-outline-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="modalAction('{{ url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/confirm_ajax') }}')" class="btn btn-outline-danger btn-sm" title="Hapus">
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
<div class="modal fade" id="modalMahasiswa" tabindex="-1" role="dialog" aria-labelledby="modalMahasiswaTitle" aria-hidden="true">
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
        /* Custom CSS for Import Button */
        .btn-import {
            background-color: #ff851b;
            color: #fff;
        }
        .btn-import:hover {
            background-color: #e07415; /* Darker shade of orange */
            color: #fff;
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
                $('#modalMahasiswa .modal-content').html(data);
                $('#modalMahasiswa').modal('show');
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
        var mahasiswaMahasiswa = $('#table_mahasiswa').DataTable({
            autoWidth: false,
            responsive: true,
            dom: 'rtip', // Hide default search (f) and length (l), keep table (t), info (i), pagination (p)
            columnDefs: [
                { orderable: false, targets: [0, 5] }, // Disable sort on No and Aksi
                { searchable: false, targets: [0, 5] }
            ]
        });
        
        console.log("DataTable initialized:", mahasiswaMahasiswa); // DEBUG

        // Filter Program Studi (Client Side)
        $('#mahasiswa_prodi').change(function() {
            var selectedProdi = $(this).val(); // Get value (prodi_kode)
            console.log("Prodi changed:", selectedProdi);
            
            if (selectedProdi === "") {
                mahasiswaMahasiswa.column(3).search("").draw(); // Reset filter
            } else {
                // Exact match for Code (e.g., 'TI' or 'SIB')
                mahasiswaMahasiswa.column(3).search(selectedProdi).draw();
            }
        });

        // Event handler untuk search bar custom
        $('#mahasiswa_search').on('keyup', function() {
            mahasiswaMahasiswa.search(this.value).draw();
        });
        
        // Event handler untuk Show Entries
        $('#data-table-length').change(function() {
            mahasiswaMahasiswa.page.len($(this).val()).draw();
        });

    });

    // Make global function for import handling
    function importMahasiswa(event) {
        event.preventDefault();
        var form = $('#form-import')[0];
        var formData = new FormData(form);
        
        // Disable button
        var submitBtn = $('#form-import').find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).text('Mengimpor...');

        $.ajax({
            url: '{{ url('/mahasiswa/import_ajax') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) { 
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#modalMahasiswa').modal('hide');
                        $('#table_mahasiswa').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                 var errorMessage = 'Terjadi kesalahan saat mengimpor data.';
                 if (xhr.responseJSON && xhr.responseJSON.message) {
                     errorMessage = xhr.responseJSON.message;
                 }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }
</script>
@endpush