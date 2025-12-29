@empty($kelas)
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
<form action="{{ url('/kelas/' . $kelas->kelas_id . '/delete_ajax') }}" method="POST" id="form-confirm">
    @csrf
    @method('DELETE')
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Hapus Data Kelas</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div style="color: red;">
                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan!
                <br>
                <br>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">Nama</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-book mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $kelas->kelas_nama }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">Program Studi</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-swatchbook mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $kelas->prodi ? $kelas->prodi->prodi_nama : '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </button>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Gunakan event delegation untuk menangani submit form yang dimuat secara dinamis
        $(document).on('submit', '#form-confirm', function(e) {
            e.preventDefault(); // Mencegah submit form standar

            $.ajax({
                url: $(this).attr('action'),
                type: 'DELETE', // Menggunakan method DELETE sesuai route
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#btn-submit').prop('disabled', true).text('Menghapus...');
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
                            $('#modalKelas').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menghapus data kelas.',
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan pada server.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                    });
                    console.error('AJAX Error:', xhr.responseText);
                },
                complete: function() {
                    $('#btn-submit').prop('disabled', false).text('Hapus');
                }
            });
            return false;
        });
    });
</script>
@endempty