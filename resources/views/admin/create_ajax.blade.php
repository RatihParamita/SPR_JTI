<form action="{{ url('admin/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" style="font-weight: bold;">Tambah Data Admin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
                
                {{-- KOLOM KIRI (NIDN, Prodi, Username) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="admin_nidn">NIDN</label>
                        <input type="text" class="form-control" id="admin_nidn" name="admin_nidn" placeholder="NIDN Admin" required>
                        <small id="error_admin_nidn" class="error-text form-text text-danger"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="prodi_id">Program Studi</label>
                        <select class="form-control" id="prodi_id" name="prodi_id" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach ($prodiList as $prodi)
                                <option value="{{ $prodi->prodi_id }}">{{ $prodi->prodi_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error_prodi_id" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username Admin" required>
                        <small id="error_username" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                {{-- KOLOM KANAN (Nama, No. HP, Password) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="admin_nama">Nama</label>
                        <input type="text" class="form-control" id="admin_nama" name="admin_nama" placeholder="Nama Admin" required>
                        <small id="error_admin_nama" class="error-text form-text text-danger"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_noHp">No. HP</label>
                        <input type="text" class="form-control" id="admin_noHp" name="admin_noHp" placeholder="Nomor Handphone Admin" required>
                        <small id="error_admin_noHp" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password Admin" required>
                        <small id="error_password" class="error-text form-text text-danger"></small>
                    </div>
                </div>
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
        // Gunakan event delegation untuk menangani submit form yang dimuat secara dinamis
        $(document).on('submit', '#form-tambah', function(e) {
            e.preventDefault(); // Mencegah submit form standar

            // Kosongkan semua pesan error
            $('.error-text').text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: '{{ url('admin/ajax') }}',
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
                            $('#modalAdmin').modal('hide'); // Tutup modal (pastikan ID sesuai dengan di index.blade.php)
                            location.reload(); // Reload halaman untuk memperbarui tabel
                        });
                    } else {
                        // Tampilkan sweet alert untuk error umum
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menambahkan data admin.',
                        });
                    }
                },
                error: function(xhr) {
                    $('#btn-submit').prop('disabled', false).text('Simpan');
                    
                    // Handle error 422 (Validasi)
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            // Tampilkan pesan error di bawah input yang sesuai
                            $('#error_' + key).text(value[0]);
                            // Tambahkan kelas is-invalid ke input
                            $('#' + key).addClass('is-invalid');
                        });
                    } 
                    // Handle error lain (500, dll)
                    else if (xhr.status !== 422) {
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

        // Pastikan SweetAlert2 sudah dimuat sebelum digunakan
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 library not loaded. Please ensure it is included in your template.');
        }
    });
</script>
