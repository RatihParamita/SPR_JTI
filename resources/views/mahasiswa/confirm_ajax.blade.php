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
<form action="{{ url('/mahasiswa/' . $mahasiswa->mahasiswa_id . '/delete_ajax') }}" method="POST" id="form-confirm">
    @csrf
    @method('DELETE')
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Hapus Data Mahasiswa</h5>
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
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-user-circle fa-4x mr-3" style="color: #001f3f;"></i> {{-- Dark blue color for user icon --}}
                <div>
                    <h3 class="mb-0 font-weight-bold">{{ $mahasiswa->mahasiswa_nama }}</h3>
                    <p class="text-muted mb-0">ID Mahasiswa: {{ $mahasiswa->mahasiswa_id }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">NIM</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-id-card mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $mahasiswa->mahasiswa_nim }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">Username</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $mahasiswa->user->username }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">Program Studi</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-swatchbook mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $mahasiswa->prodi ? $mahasiswa->prodi->prodi_nama : '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">Kelas</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-book mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $mahasiswa->kelas ? $mahasiswa->kelas->kelas_nama : '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">No. HP</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $mahasiswa->mahasiswa_noHp }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </button>
            <button type="submit" class="btn btn-danger" id="btn-submit">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Gunakan event handler langsung pada form (bukan delegasi document) agar tidak double binding saat modal diload ulang
        $('#form-confirm').on('submit', function(e) {
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
                            $('#modalMahasiswa').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menghapus data mahasiswa.',
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
                    $('#btn-submit').prop('disabled', false).html('<i class="fas fa-trash mr-1"></i> Hapus');
                }
            });
            return false;
        });
    });
</script>
@endempty