<form action="{{ url('ruangan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" style="font-weight: bold;">Tambah Data Ruangan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">

                {{-- KOLOM TENGAH (Nama) --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ruangan_nama">Nama</label>
                        <input type="text" class="form-control" id="ruangan_nama" name="ruangan_nama" required>
                        <small id="error_ruangan_nama" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                
                {{-- KOLOM KIRI (Kode) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ruangan_kode">Kode</label>
                        <input type="text" class="form-control" id="ruangan_kode" name="ruangan_kode" required>
                        <small id="error_ruangan_kode" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                {{-- KOLOM KANAN (Kuota) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ruangan_kuota">Kuota</label>
                        <input type="number" class="form-control" id="ruangan_kuota" name="ruangan_kuota" required>
                        <small id="error_ruangan_kuota" class="error-text form-text text-danger"></small>
                    </div>
                </div>

                {{-- KOLOM TENGAH (Fasilitas) --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ruangan_fasilitas">Fasilitas</label>
                        <textarea class="form-control" id="ruangan_fasilitas" name="ruangan_fasilitas"></textarea>
                        <small id="error_ruangan_fasilitas" class="error-text form-text text-danger"></small>
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
                url: '{{ url('ruangan/ajax') }}',
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
                            $('#modalRuangan').modal('hide'); 
                            location.reload(); 
                        });
                    } else {
                        // Tampilkan sweet alert untuk error umum
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menambahkan data ruangan.',
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
