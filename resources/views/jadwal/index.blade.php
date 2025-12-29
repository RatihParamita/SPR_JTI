@extends('layouts.template')

@section('content')
<div class="card" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div class="card-header">
        <div class="d-flex align-items-center w-100">
            {{-- Filter Ruangan --}}
            <div class="d-flex align-items-center mx-3">
                <label class="col-form-label mr-2 mb-0">Filter:</label>
                <select class="form-control form-control-sm" id="filter_ruangan" style="width: 200px;">
                    <option value="">- Semua Ruangan -</option>
                    @foreach ($ruanganList as $ruangan)
                        <option value="{{ $ruangan->ruangan_id }}">{{ $ruangan->ruangan_nama }}</option>
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
                <button onclick="modalAction('{{ url('/jadwal/create_ajax') }}')" class="btn btn-success btn-sm">
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

        {{-- Tabel Data Jadwal --}}
        <table class="table table-sm" id="table_jadwal">
            <thead style="background-color: #e3e3e3; font-color: #3F3F3F;">
                <tr>
                    <th>No.</th>
                    <th>Ruangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Kegiatan/Acara</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody style="background-color: #ffffffff; font-color: #3F3F3F;">
                @foreach($jadwals as $key => $jadwal)
                <tr>
                    <td>{{ $key + 1 }}.</td>
                    <td data-search="{{ $jadwal->ruangans->pluck('ruangan_nama')->implode(' ') }}">
                        {{ Str::limit($jadwal->ruangans->pluck('ruangan_nama')->implode(', '), 35, '...') }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($jadwal->jadwal_tgl)->locale('id')->translatedFormat('d F Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($jadwal->jadwal_jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jadwal_jam_selesai)->format('H:i') }}</td>
                    <td>{{ Str::limit($jadwal->jadwal_nama, 35, '...') }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Aksi">
                            <button onclick="modalAction('{{ url('/jadwal/' . $jadwal->jadwal_id . '/show_ajax') }}')" class="btn btn-outline-info btn-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="modalAction('{{ url('/jadwal/' . $jadwal->jadwal_id . '/edit_ajax') }}')" class="btn btn-outline-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="modalAction('{{ url('/jadwal/' . $jadwal->jadwal_id . '/confirm_ajax') }}')" class="btn btn-outline-danger btn-sm" title="Hapus">
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
<div class="modal fade" id="modalJadwal" tabindex="-1" role="dialog" aria-labelledby="modalJadwalTitle" aria-hidden="true">
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
        /* Custom CSS to fix Select2 height and alignment */
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            padding: 0 0.75rem;
            display: flex;
            align-items: center;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 0 !important;
            margin-top: 0 !important;
            color: #495057; /* Match Bootstrap text color */
            font-family:inherit;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Fix Select2 Height & Font for Multi Select (Ruangan) */
        .select2-container .select2-selection--multiple {
            min-height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Fix Placeholder Font & Alignment */
        .select2-container .select2-search--inline .select2-search__field {
            font-family: inherit !important;
            margin-top: 0 !important;
            line-height: 1.5 !important;
            height: auto !important;
            vertical-align: middle !important;
        }

        /* Fix Chips Stacking & Design */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border: 1px solid #006fe6;
            color: #fff;
            padding: 2px 8px 2px 24px; /* Left padding for the 'x' */
            margin: 2px 4px 2px 0;
            border-radius: 4px;
            position: relative; /* For absolute positioning of 'x' */
            display: inline-flex;
            align-items: center;
        }

        /* Fix 'x' Removal Button Position */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255,255,255,0.8);
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 20px; /* Specific width clickable area */
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid rgba(255,255,255,0.3);
            margin: 0; /* Reset margins */
            padding: 0; /* Reset padding */
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: rgba(0,0,0,0.1);
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Fungsi global untuk menampilkan modal
    function modalAction(url) {
        $('#modalSize').removeClass('modal-xl modal-lg modal-md modal-sm').addClass('modal-lg'); // Reset ukuran
            
        // Load konten modal
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#modalJadwal .modal-content').html(data);
                $('#modalJadwal').modal('show');
            },
            error: function(xhr) {
                alert('Gagal memuat modal. Cek console log.');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        // Inisialisasi DataTables Client Side
        var dataJadwal = $('#table_jadwal').DataTable({
            autoWidth: false,
            responsive: true,
            dom: 'rtip', // Hide default search (f) and length (l)
            columnDefs: [
                { orderable: false, targets: [0, 5] }, // Disable sort on No and Aksi
                { searchable: false, targets: [0, 5] }
            ]
        });

        // Event handler untuk filter Ruangan (Client Side)
        $('#filter_ruangan').change(function() {
            var selectedRuangan = $(this).val(); // Get value (ruangan_id)
            var selectedText = $(this).find("option:selected").text().trim(); // Get text (ruangan_nama)
            
            if (selectedRuangan === "") {
                dataJadwal.column(1).search("").draw(); // Reset filter (Column 1 is Ruangan)
            } else {
                // Exact match for Ruangan Name? Or ID?
                // The table displays Ruangan Name. So we should search by Name if relying on DOM.
                // But wait, the dropdown values are IDs. The text is Name.
                // Verify column index 1 corresponds to 'Ruangan'.
                // Yes: <th>Ruangan</th> (index 1)
                
                // IMPORTANT: Since Ruangan column can contain multiple comma-separated names, 
                // and truncated text '...', simplistic exact search might fail if not careful.
                // However, user asked for "like other files" (simple client search).
                // Let's use the Text from dropdown for search.
                
                dataJadwal.column(1).search(selectedText).draw();
            }
        });

        // Event handler untuk Show Entries
        $('#data-table-length').change(function() {
            dataJadwal.page.len($(this).val()).draw();
        });

    });
</script>
@endpush
