@empty($jadwal)
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan
            </div>
            <button type="button" data-dismiss="modal" class="btn btn-warning">Kembali</button>
        </div>
    </div>
@else
<form action="{{ url('/jadwal/' . $jadwal->jadwal_id . '/update_ajax') }}" method="POST" id="form-edit">
    @csrf
    @method('PUT')
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Edit Data Jadwal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            
             {{-- ROW 1: Ruangan --}}
             <div class="form-group">
                <label for="ruangan_ids">Ruangan</label>
                <select class="select2" id="ruangan_ids" name="ruangan_ids[]" multiple="multiple" required style="width: 100%;">
                    @php
                        $selectedRuangan = $jadwal->ruangans->pluck('ruangan_id')->toArray();
                    @endphp
                    @foreach ($ruanganList as $ruangan)
                         <option value="{{ $ruangan->ruangan_id }}" {{ in_array($ruangan->ruangan_id, $selectedRuangan) ? 'selected' : '' }}>
                            {{ $ruangan->ruangan_nama }}</option>
                    @endforeach
                </select>
                <small id="error_ruangan_ids" class="error-text form-text text-danger"></small>
            </div>

            {{-- ROW 2: Tanggal, Jam Mulai, Jam Selesai --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jadwal_tgl">Tanggal</label>
                         <input type="date" class="form-control" id="jadwal_tgl" name="jadwal_tgl" value="{{ $jadwal->jadwal_tgl }}" required>
                        <small id="error_jadwal_tgl" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="form-group">
                        <label for="jadwal_jam_mulai">Jam Mulai</label>
                         <input type="time" class="form-control" id="jadwal_jam_mulai" name="jadwal_jam_mulai" value="{{ \Carbon\Carbon::parse($jadwal->jadwal_jam_mulai)->format('H:i') }}" required>
                        <small id="error_jadwal_jam_mulai" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jadwal_jam_selesai">Jam Selesai</label>
                         <input type="time" class="form-control" id="jadwal_jam_selesai" name="jadwal_jam_selesai" value="{{ \Carbon\Carbon::parse($jadwal->jadwal_jam_selesai)->format('H:i') }}" required>
                        <small id="error_jadwal_jam_selesai" class="error-text form-text text-danger"></small>
                    </div>
                </div>
            </div>

            {{-- ROW 3: Kegiatan/Acara & Peserta --}}
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label for="jadwal_nama">Kegiatan/Acara</label>
                        <input type="text" class="form-control" id="jadwal_nama" name="jadwal_nama" value="{{ $jadwal->jadwal_nama }}" placeholder="Nama Kegiatan/Acara" required>
                        <small id="error_jadwal_nama" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="jadwal_jumPes">Peserta</label>
                        <input type="number" class="form-control" id="jadwal_jumPes" name="jadwal_jumPes" value="{{ $jadwal->jadwal_jumPes }}" required>
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
                            <option value="">- Semua Status -</option>
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
                            <option value="">- Semua Program Studi -</option>
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
                            <option value="">- Semua Kelas -</option>
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
                                $namaLengkap = $user->dosen->dosen_nama;
                            } elseif ($levelKode == 'TDK' && $user->tendik) {
                                $namaLengkap = $user->tendik->tendik_nama;
                            } elseif ($levelKode == 'ADM' && $user->admin) {
                                $namaLengkap = $user->admin->admin_nama;
                            }
                        @endphp
                        <option value="{{ $user->user_id }}" 
                                data-level="{{ $levelKode }}" 
                                data-prodi="{{ $prodiId }}" 
                                data-kelas="{{ $kelasId }}"
                                {{ $user->user_id == $jadwal->user_id ? 'selected' : '' }}>
                            {{ $namaLengkap }}
                        </option>
                    @endforeach
                </select>
                <small id="error_user_id" class="error-text form-text text-danger"></small>
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Init Select2
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
        const $allOptions = $userSelect.find('option').clone(); // Cache
        const originalSelected = '{{ $jadwal->user_id }}'; // Original value

        function filterUsers() {
            const status = $('#filter_status').val();
            const prodi = $('#filter_prodi').val();
            const kelas = $('#filter_kelas').val();
             
             // Get currently selected (if any) to preserve it if visible
            const currentVal = $userSelect.val();

            $userSelect.empty().append('<option value="">-- Pilih Peminjam --</option>');

            $allOptions.each(function() {
                const $opt = $(this);
                const optLevel = $opt.data('level');
                const optProdi = $opt.data('prodi');
                const optKelas = $opt.data('kelas');
                const optVal = $opt.val();

                if (!optLevel) return;

                let show = true;

                if (status && optLevel !== status) show = false;
                if (prodi && (optLevel === 'MHS' || optLevel === 'DSN') && String(optProdi) !== String(prodi)) show = false;
                if (kelas && optLevel === 'MHS' && String(optKelas) !== String(kelas)) show = false;

                // Always show the currently assigned user regardless of filter (optional UX choice, safer for editing)
                if (optVal == originalSelected) show = true;

                if (show) {
                    $userSelect.append($opt.clone());
                }
            });

            // Re-select value if it still exists
            $userSelect.val(currentVal || originalSelected).trigger('change');
        }

        $('#filter_status').change(function() {
            const status = $(this).val();
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
            $('#filter_kelas').empty().append('<option value="">- Semua Kelas -</option>');
            
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


        $(document).on('submit', '#form-edit', function(e) {
            e.preventDefault(); 
            $('.error-text').text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: $(this).attr('action'),
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
                             if (typeof dataJadwal !== 'undefined') {
                                location.reload();
                            } else {
                                location.reload(); 
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal mengupdate data jadwal.',
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
@endempty
