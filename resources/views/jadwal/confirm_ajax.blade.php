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
<form action="{{ url('/jadwal/' . $jadwal->jadwal_id . '/delete_ajax') }}" method="POST" id="form-confirm">
    @csrf
    @method('DELETE')
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Hapus Data Jadwal</h5>
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
            {{-- ROW 1 --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="font-weight-bold text-dark">Ruangan</label>
                    <div class="d-flex align-items-center">
                         <i class="fas fa-door-open mr-2 text-primary"></i>
                         @if($jadwal->ruangans->isNotEmpty())
                            <span class="text-secondary">{{ $jadwal->ruangans->pluck('ruangan_nama')->implode(', ') }}</span>
                         @else
                            <span>-</span>
                         @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="font-weight-bold text-dark">Nama Peminjam</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle mr-2 text-primary"></i>
                        @php
                             $user = $jadwal->user;
                             $nama = $user->username;
                             if($user->mahasiswa) $nama = $user->mahasiswa->mahasiswa_nama;
                             elseif($user->dosen) $nama = $user->dosen->dosen_nama;
                             elseif($user->tendik) $nama = $user->tendik->tendik_nama;
                             elseif($user->admin) $nama = $user->admin->admin_nama;
                        @endphp
                        <span class="text-secondary">{{ $nama }}</span>
                    </div>
                </div>
            </div>

            {{-- ROW 2 --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="font-weight-bold text-dark">Tanggal</label>
                    <div class="d-flex align-items-center">
                         <i class="far fa-calendar mr-2 text-primary"></i>
                         <span class="text-secondary">{{ \Carbon\Carbon::parse($jadwal->jadwal_tgl)->locale('id')->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="font-weight-bold text-dark">Status Peminjam</label>
                    <div class="d-flex align-items-center">
                         <i class="fas fa-user mr-2 text-primary"></i>
                         <span class="text-secondary">{{ $jadwal->user->level->level_nama ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- ROW 3 --}}
            <div class="row mb-3">
                 <div class="col-md-3">
                    <label class="font-weight-bold text-dark">Jam Mulai</label>
                    <div class="d-flex align-items-center">
                         <i class="far fa-clock mr-2 text-primary"></i>
                         <span class="text-secondary">{{ \Carbon\Carbon::parse($jadwal->jadwal_jam_mulai)->format('H:i') }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-dark">Jam Selesai</label>
                    <div class="d-flex align-items-center">
                         <i class="far fa-clock mr-2 text-primary"></i>
                         <span class="text-secondary">{{ \Carbon\Carbon::parse($jadwal->jadwal_jam_selesai)->format('H:i') }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="font-weight-bold text-dark">Program Studi</label>
                    <div class="d-flex align-items-center">
                         <i class="fas fa-swatchbook mr-2 text-primary"></i>
                         @php
                            $prodi = '-';
                            if($user->mahasiswa) $prodi = $user->mahasiswa->prodi->prodi_nama ?? '-';
                            elseif($user->dosen) $prodi = $user->dosen->prodi->prodi_nama ?? '-';
                         @endphp
                         <span class="text-secondary">{{ $prodi }}</span>
                    </div>
                </div>
            </div>

            {{-- ROW 4 --}}
            <div class="row mb-3">
                <div class="col-md-6">
                   <label class="font-weight-bold text-dark">Kegiatan/Acara</label>
                   <div class="d-flex align-items-center">
                        <i class="fas fa-thumbtack mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $jadwal->jadwal_nama }}</span>
                   </div>
               </div>
               <div class="col-md-6">
                   <label class="font-weight-bold text-dark">Kelas</label>
                   <div class="d-flex align-items-center">
                        {{-- <i class="fas fa-users-class mr-2 text-primary"></i> --}} {{-- icon placeholder --}}
                        <i class="fas fa-book mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $user->mahasiswa->kelas->kelas_nama ?? '-' }}</span>
                   </div>
               </div>
            </div>

            {{-- ROW 5 --}}
            <div class="row mb-3">
                <div class="col-md-6">
                   <label class="font-weight-bold text-dark">Peserta</label>
                   <div class="d-flex align-items-center">
                        <i class="fas fa-hand-paper mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $jadwal->jadwal_jumPes }} Orang</span>
                   </div>
               </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Unbind previous events to avoid duplication if modal is reused
        $(document).off('submit', '#form-confirm').on('submit', '#form-confirm', function(e) {
            e.preventDefault(); 
            
            var form = $(this);
            console.log("Submitting delete form via AJAX..."); // Debug

            $.ajax({
                url: form.attr('action'),
                type: 'DELETE', // Laravel handles this via _method field, but simple DELETE type is clearer for jQuery
                data: form.serialize(),
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
                            $('#modalJadwal').modal('hide');
                            // Refresh DataTable or Page
                            if (typeof dataJadwal !== 'undefined') {
                                // dataJadwal.ajax.reload(); // client side reload might just be location.reload
                                location.reload();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menghapus data jadwal.',
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server.',
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
