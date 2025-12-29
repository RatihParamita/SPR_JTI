<form action="{{ url('jadwal/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Tambah Data Jadwal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            
            {{-- ROW 1: Ruangan --}}
            <div class="form-group">
                <label for="ruangan_ids">Ruangan</label>
                <select class="select2" id="ruangan_ids" name="ruangan_ids[]" multiple="multiple" required style="width: 100%;">
                    @foreach ($ruanganList as $ruangan)
                        <option value="{{ $ruangan->ruangan_id }}">{{ $ruangan->ruangan_nama }}</option>
                    @endforeach
                </select>
                <small id="error_ruangan_ids" class="error-text form-text text-danger"></small>
            </div>

            {{-- ROW 2: Tanggal, Jam Mulai, Jam Selesai --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jadwal_tgl">Tanggal</label>
                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                             <input type="date" class="form-control" id="jadwal_tgl" name="jadwal_tgl" required>
                             {{-- <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div> --}}
                        </div>
                        <small id="error_jadwal_tgl" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="col-md-3">
                     <div class="form-group">
                        <label for="jadwal_jam_mulai">Jam Mulai</label>
                        <div class="input-group date" id="timepicker" data-target-input="nearest">
                             <input type="time" class="form-control" id="jadwal_jam_mulai" name="jadwal_jam_mulai" required>
                             {{-- <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="far fa-clock"></i></div>
                            </div> --}}
                        </div>
                        <small id="error_jadwal_jam_mulai" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="jadwal_jam_selesai">Jam Selesai</label>
                        <div class="input-group date" id="timepicker2" data-target-input="nearest">
                             <input type="time" class="form-control" id="jadwal_jam_selesai" name="jadwal_jam_selesai" required>
                             {{-- <div class="input-group-append" data-target="#timepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="far fa-clock"></i></div>
                            </div> --}}
                        </div>
                        <small id="error_jadwal_jam_selesai" class="error-text form-text text-danger"></small>
                    </div>
                </div>
            </div>

            {{-- ROW 3: Kegiatan/Acara & Peserta --}}
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="jadwal_nama">Kegiatan/Acara</label>
                        <input type="text" class="form-control" id="jadwal_nama" name="jadwal_nama" placeholder="Nama Kegiatan/Acara" required>
                        <small id="error_jadwal_nama" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="jadwal_jumPes">Peserta</label>
                        <input type="number" class="form-control" id="jadwal_jumPes" name="jadwal_jumPes" required>
                        <small id="error_jadwal_jumPes" class="error-text form-text text-danger"></small>
                    </div>
                </div>
            </div>

            {{-- ROW 4: Filters (Status, Prodi, Kelas) --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_status">Status Peminjam</label>
                        <select class="form-control" id="filter_status">
                            <option value="">- Pilih Status -</option>
                            @foreach ($levelList as $level)
                                <option value="{{ $level->level_kode }}">{{ $level->level_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_prodi">Program Studi</label>
                        <select class="form-control" id="filter_prodi" disabled>
                            <option value="">- Pilih Program Studi -</option>
                            @foreach ($prodiList as $prodi)
                                <option value="{{ $prodi->prodi_id }}">{{ $prodi->prodi_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_kelas">Kelas</label>
                        <select class="form-control" id="filter_kelas" disabled>
                            <option value="">- Pilih Kelas -</option>
                            @foreach ($kelasList as $kelas)
                                <option value="{{ $kelas->kelas_id }}">{{ $kelas->kelas_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ROW 5: Nama Peminjam --}}
            <div class="form-group">
                <label for="user_id">Nama Peminjam</label>
                <select class="select2-user" id="user_id" name="user_id" required style="width: 100%;">
                    <option value="">-- Pilih Peminjam --</option>
                    @foreach ($userList as $user)
                        @php
                            $levelKode = $user->level->level_kode ?? '';
                            $prodiId = '';
                            $kelasId = '';
                            $namaLengkap = $user->username; 

                            if ($levelKode == 'MHS' && $user->mahasiswa) {
                                $prodiId = $user->mahasiswa->prodi_id;
                                $kelasId = $user->mahasiswa->kelas_id;
                                $namaLengkap = $user->mahasiswa->mahasiswa_nama . ' (' . $user->mahasiswa->mahasiswa_nim . ')';
                            } elseif ($levelKode == 'DSN' && $user->dosen) {
                                $prodiId = $user->dosen->prodi_id;
                                $namaLengkap = $user->dosen->dosen_nama . ' (' . $user->dosen->dosen_nidn . ')';
                            } elseif ($levelKode == 'TDK' && $user->tendik) {
                                $namaLengkap = $user->tendik->tendik_nama . ' (' . $user->tendik->tendik_nidn . ')';
                            } elseif ($levelKode == 'ADM' && $user->admin) {
                                $namaLengkap = $user->admin->admin_nama . ' (' . $user->admin->admin_nidn . ')';
                            }
                        @endphp
                        <option value="{{ $user->user_id }}" 
                                data-level="{{ $levelKode }}" 
                                data-prodi="{{ $prodiId }}" 
                                data-kelas="{{ $kelasId }}">
                            {{ $namaLengkap }}
                        </option>
                    @endforeach
                </select>
                <small id="error_user_id" class="error-text form-text text-danger"></small>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Init Select2 for Ruangan
        $('.select2').select2({
            dropdownParent: $('#modalJadwal'),
            placeholder: "Pilih Ruangan",
            allowClear: true
        });

        // Init Select2 for User
        $('.select2-user').select2({
            dropdownParent: $('#modalJadwal'),
            placeholder: "Pilih Peminjam",
            allowClear: true
        });

        // --- FILTERING LOGIC ---
        const $userSelect = $('#user_id');
        const $allOptions = $userSelect.find('option').clone(); // Cache all options

        function filterUsers() {
            const status = $('#filter_status').val();
            const prodi = $('#filter_prodi').val();
            const kelas = $('#filter_kelas').val();

            $userSelect.empty().append('<option value="">-- Pilih Peminjam --</option>');

            $allOptions.each(function() {
                const $opt = $(this);
                const optLevel = $opt.data('level');
                const optProdi = $opt.data('prodi');
                const optKelas = $opt.data('kelas');

                if (!optLevel) return; // Skip placeholder

                let show = true;

                if (status && optLevel !== status) show = false;
                // Filter Prodi only affects MHS and DSN
                if (prodi && (optLevel === 'MHS' || optLevel === 'DSN') && String(optProdi) !== String(prodi)) show = false;
                // Filter Kelas only affects MHS
                if (kelas && optLevel === 'MHS' && String(optKelas) !== String(kelas)) show = false;

                if (show) {
                    $userSelect.append($opt.clone());
                }
            });
            
            // Re-trigger Select2 update usually not needed for options append but good practice if value changes
            $userSelect.trigger('change');
        }

        $('#filter_status').change(function() {
            const status = $(this).val();
            // Enable/Disable Filters based on Status
            if (status === 'MHS') {
                $('#filter_prodi, #filter_kelas').prop('disabled', false);
            } else if (status === 'DSN') {
                $('#filter_prodi').prop('disabled', false);
                $('#filter_kelas').prop('disabled', true).val('');
            } else {
                $('#filter_prodi, #filter_kelas').prop('disabled', true).val('');
            }
            filterUsers();
        });

        $('#filter_prodi').change(function() {
            var prodiId = $(this).val();
            // Clear and reset Kelas dropdown
            $('#filter_kelas').empty().append('<option value="">- Pilih Kelas -</option>');
            
            if (prodiId) {
                $.ajax({
                    url: '{{ url('jadwal/get_kelas_by_prodi') }}/' + prodiId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#filter_kelas').append('<option value="' + value.kelas_id + '">' + value.kelas_nama + '</option>');
                        });
                        filterUsers(); // Re-trigger user filtering
                    }
                });
            } else {
                filterUsers();
            }
        });

        $('#filter_kelas').change(filterUsers);

        // --- AJAX FORM SUBMIT ---
        $(document).on('submit', '#form-tambah', function(e) {
            e.preventDefault(); 
            $('.error-text').text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: '{{ url('jadwal/ajax') }}',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#btn-submit').prop('disabled', true).text('Menyimpan...');
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modalJadwal').modal('hide'); 
                            // Refresh DataTable if it exists
                            if (typeof dataJadwal !== 'undefined') {
                                // dataJadwal.ajax.reload(); // Cannot access variable from here easily if scoped
                                location.reload();
                            } else {
                                location.reload(); 
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menambahkan data jadwal.',
                        });
                    }
                },
                error: function(xhr) {
                    $('#btn-submit').prop('disabled', false).text('Simpan');
                    
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.msgField) {
                        let errors = xhr.responseJSON.msgField;
                        $.each(errors, function(key, value) {
                            $('#error_' + key).text(value[0]);
                            $('#' + key).addClass('is-invalid');
                        });
                    } 
                    else {
                         Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server.',
                        });
                        console.error('AJAX Error:', xhr.responseText);
                    }
                },
                complete: function() {
                    $('#btn-submit').prop('disabled', false).text('Simpan');
                }
            });
            return false;
        });
        // Pastikan SweetAlert2 sudah dimuat sebelum digunakan
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 library not loaded. Please ensure it is included in your template.');
        }
    });
</script>
