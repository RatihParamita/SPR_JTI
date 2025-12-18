@empty($admin)
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
            <h5 class="modal-title" style="font-weight: bold;">Data Admin</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-4">
                    <i class="fas fa-user-circle fa-4x mr-3" style="color: #001f3f;"></i> {{-- Dark blue color for user icon --}}
                    <div>
                        <h3 class="mb-0 font-weight-bold">{{ $admin->admin_nama }}</h3>
                        <p class="text-muted mb-0">ID Admin: {{ $admin->admin_id }}</p>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">NIDN</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-id-card mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $admin->admin_nidn }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">Username</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $admin->user->username }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">Program Studi</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-swatchbook mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $admin->prodi ? $admin->prodi->prodi_nama : '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark">No. HP</label>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone mr-2 text-primary"></i>
                        <span class="text-secondary">{{ $admin->admin_noHp }}</span>
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
