@empty($ruangan)
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
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Data Ruangan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="font-weight-bold text-dark">Nama</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-door-open mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $ruangan->ruangan_nama }}</span>
                    </div>
                </div>
                <div class="col-md-12 mb-4">
                    <label class="font-weight-bold text-dark">Kode</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-font mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $ruangan->ruangan_kode }}</span>
                    </div>
                </div>
                <div class="col-md-12 mb-4">
                    <label class="font-weight-bold text-dark">Kuota</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hand-paper mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $ruangan->ruangan_kuota }}</span>
                    </div>
                </div>
                <div class="col-md-12 mb-4">
                    <label class="font-weight-bold text-dark">Fasilitas</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-desktop mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $ruangan->ruangan_fasilitas }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </button>
        </div>
    </div>
@endempty
