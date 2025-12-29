<form action="{{ url('tendik/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" style="font-weight: bold;">Tambah Data Tendik</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
                
                {{-- KOLOM KIRI (NIDN) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tendik_nidn">NIDN</label>
                        <input type="text" class="form-control" id="tendik_nidn" name="tendik_nidn" placeholder="NIDN Tendik" required>
                        <small id="error_tendik_nidn" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                {{-- KOLOM KANAN (No. HP) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tendik_noHp">No. HP</label>
                        <input type="text" class="form-control" id="tendik_noHp" name="tendik_noHp" placeholder="Nomor Handphone Tendik" required>
                        <small id="error_tendik_noHp" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                {{-- KOLOM JUSTIFY (Nama) --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="tendik_nama">Nama</label>
                        <input type="text" class="form-control" id="tendik_nama" name="tendik_nama" placeholder="Nama Tendik" required>
                        <small id="error_tendik_nama" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                {{-- KOLOM JUSTIFY (Username) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username Tendik" required>
                        <small id="error_username" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                {{-- KOLOM KANAN (Password) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password Tendik" required>
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
                url: '{{ url('tendik/ajax') }}',
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
                            $('#modalTendik').modal('hide'); // Tutup modal (pastikan ID sesuai dengan di index.blade.php)
                            location.reload(); // Reload halaman untuk memperbarui tabel
                        });
                    } else {
                        // Tampilkan sweet alert untuk error umum
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menambahkan data tendik.',
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
