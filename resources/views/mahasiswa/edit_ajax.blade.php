@empty($mahasiswa)
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
<form action="{{ url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/update_ajax') }}" method="POST" id="form-edit">
    @csrf
    @method('PUT')
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Edit Data Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NIM</label>
                        <input type="text" class="form-control" id="mahasiswa_nim" name="mahasiswa_nim" value="{{ $mahasiswa->mahasiswa_nim }}" required>
                        <small id="error-mahasiswa_nim" class="error-text form-text text-danger"></small>
                    </div>
                    
                    <div class="form-group">
                        <label>Prodi</label>
                        <select class="form-control" id="prodi_id" name="prodi_id" required>
                            <option value="">-- Pilih Prodi --</option>
                            @foreach ($prodiList as $prodi)
                                <option value="{{ $prodi->prodi_id }}" {{ $prodi->prodi_id == $mahasiswa->prodi_id ? 'selected' : '' }}>{{ $prodi->prodi_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-prodi_id" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ $mahasiswa->user->username }}" required>
                        <small id="error-username" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" id="mahasiswa_nama" name="mahasiswa_nama" value="{{ $mahasiswa->mahasiswa_nama }}" required>
                        <small id="error-mahasiswa_nama" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select class="form-control" id="kelas_id" name="kelas_id" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelasList as $kelas)
                                        {{-- Only show classes that match the current prodi initially --}}
                                        @if($kelas->prodi_id == $mahasiswa->prodi_id)
                                            <option value="{{ $kelas->kelas_id }}" {{ $kelas->kelas_id == $mahasiswa->kelas_id ? 'selected' : '' }}>{{ $kelas->kelas_nama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small id="error-kelas_id" class="error-text form-text text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>No. HP</label>
                                <input type="text" class="form-control" id="mahasiswa_noHp" name="mahasiswa_noHp" value="{{ $mahasiswa->mahasiswa_noHp }}" required>
                                <small id="error-mahasiswa_noHp" class="error-text form-text text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small id="error-password" class="error-text form-text text-danger"></small>
                    </div>
                </div>
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
        // Dynamic Dropdown Kelas based on Prodi (Edit)
        // Store the initial selected class to restore it if needed or just handle logic
        var currentKelasId = '{{ $mahasiswa->kelas_id }}';

        $('#prodi_id').on('change', function() {
            var prodiId = $(this).val();
            if (prodiId) {
                $.ajax({
                    url: '{{ url('mahasiswa/get_kelas_by_prodi') }}/' + prodiId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#kelas_id').empty();
                        $('#kelas_id').append('<option value="">-- Pilih Kelas --</option>');
                        $.each(data, function(key, value) {
                            // If the fetched class matches the originally selected class, select it (only if prodi hasn't changed effectively, though usually changing prodi means changing class context)
                            // In edit, if user changes prodi, we should reset class selection unless we want to be fancy.
                            // However, if the user changes prodi back to original, we might want to restore.
                            // For now, standard behavior: reset to empty or select if match found.
                            
                            // Check if this value.kelas_id == currentKelasId (but only if the prodi matches original?)
                            // Simpler: just append. User picks new class.
                            $('#kelas_id').append('<option value="' + value.kelas_id + '">' + value.kelas_nama + '</option>');
                        });
                    }
                });
            } else {
                $('#kelas_id').empty();
                $('#kelas_id').append('<option value="">-- Pilih Kelas --</option>');
            }
        });

        // Gunakan event delegation untuk menangani submit form yang dimuat secara dinamis
        $(document).on('submit', '#form-edit', function(e) {
            e.preventDefault(); // Mencegah submit form standar

            // Kosongkan semua pesan error
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
                        // Tampilkan sweet alert untuk sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modalMahasiswa').modal('hide'); 
                            location.reload(); 
                        });
                    } else {
                        // Tampilkan pesan error validasi
                        $('.error-text').text('');
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                            $('#' + prefix).addClass('is-invalid');
                        });

                        // Tampilkan sweet alert untuk error umum
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal mengupdate data mahasiswa.',
                        });
                    }
                },
                error: function(xhr) {
                    $('#btn-submit').prop('disabled', false).text('Simpan');
                    
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            // Fix key format if needed (e.g., student_name -> error-student_name)
                            // Assuming backend returns keys matching input names
                            $('#error-' + key).text(value[0]);
                            $('#' + key).addClass('is-invalid');
                        });
                    } 
                    else {
                         Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server. Cek console log!',
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
        
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 library not loaded.');
        }
    });
</script>
@endempty