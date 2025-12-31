<div class="card-body">
    <div class="row">
        <div class="col-md-12 text-center mb-4">
            <i class="fas fa-user-circle fa-6x text-secondary"></i>
            <h3 class="mt-2 font-weight-bold">
                @if($role == 'ADM') {{ $data->admin_nama }}
                @elseif($role == 'DSN') {{ $data->dosen_nama }}
                @elseif($role == 'TDK') {{ $data->tendik_nama }}
                @elseif($role == 'MHS') {{ $data->mahasiswa_nama }}
                @endif
            </h3>
            <p class="text-muted">
                @if($role == 'ADM') ID Admin: {{ $data->admin_id }}
                @elseif($role == 'DSN') ID Dosen: {{ $data->dosen_id }}
                @elseif($role == 'TDK') ID Tendik: {{ $data->tendik_id }}
                @elseif($role == 'MHS') ID Mahasiswa: {{ $data->mahasiswa_id }}
                @endif
            </p>
        </div>
    </div>

    @if($role == 'ADM' || $role == 'DSN')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">
                    @if($role == 'MHS') NIM @else NIDN @endif
                </label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-id-card text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->admin_nidn ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->dosen_nidn ?? '-' }}
                        @elseif($role == 'TDK') {{ $data->tendik_nidn ?? '-' }}
                        @elseif($role == 'MHS') {{ $data->mahasiswa_nim ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">Username</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user text-primary mr-2"></i>
                    <span class="text-secondary">{{ $user->username }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">Program Studi</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-swatchbook text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->prodi->prodi_nama ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->prodi->prodi_nama ?? '-' }}
                        @elseif($role == 'TDK') -
                        @elseif($role == 'MHS') {{ $data->prodi->prodi_nama ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">No. HP</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-phone text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->admin_noHp ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->dosen_noHp ?? '-' }}
                        @elseif($role == 'TDK') {{ $data->tendik_noHp ?? '-' }}
                        @elseif($role == 'MHS') {{ $data->mahasiswa_noHp ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($role == 'Tendik')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">
                    @if($role == 'MHS') NIM @else NIDN @endif
                </label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-id-card text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->admin_nidn ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->dosen_nidn ?? '-' }}
                        @elseif($role == 'TDK') {{ $data->tendik_nidn ?? '-' }}
                        @elseif($role == 'MHS') {{ $data->mahasiswa_nim ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">Username</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user text-primary mr-2"></i>
                    <span class="text-secondary">{{ $user->username }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">No. HP</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-phone text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->admin_noHp ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->dosen_noHp ?? '-' }}
                        @elseif($role == 'TDK') {{ $data->tendik_noHp ?? '-' }}
                        @elseif($role == 'MHS') {{ $data->mahasiswa_noHp ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($role == 'MHS')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">
                    @if($role == 'MHS') NIM @else NIDN @endif
                </label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-id-card text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->admin_nidn ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->dosen_nidn ?? '-' }}
                        @elseif($role == 'TDK') {{ $data->tendik_nidn ?? '-' }}
                        @elseif($role == 'MHS') {{ $data->mahasiswa_nim ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">Username</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user text-primary mr-2"></i>
                    <span class="text-secondary">{{ $user->username }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">Program Studi</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-swatchbook text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->prodi->prodi_nama ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->prodi->prodi_nama ?? '-' }}
                        @elseif($role == 'TDK') -
                        @elseif($role == 'MHS') {{ $data->prodi->prodi_nama ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">Kelas</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-book text-primary mr-2"></i>
                    <span class="text-secondary">{{ $data->kelas->kelas_nama ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">No. HP</label>
                <div class="d-flex align-items-center">
                    <i class="fas fa-phone text-primary mr-2"></i>
                    <span class="text-secondary">
                        @if($role == 'ADM') {{ $data->admin_noHp ?? '-' }}
                        @elseif($role == 'DSN') {{ $data->dosen_noHp ?? '-' }}
                        @elseif($role == 'TDK') {{ $data->tendik_noHp ?? '-' }}
                        @elseif($role == 'MHS') {{ $data->mahasiswa_noHp ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
<div class="modal-footer justify-content-between">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-arrow-left"></i> Kembali</button>
    <button type="button" class="btn btn-warning" onclick="editProfile()">
        <i class="fas fa-edit"></i> Edit</button>
</div>

<script>
    function editProfile() {
        $.ajax({
            url: "{{ url('profile/edit_ajax') }}", 
            type: "GET",
            success: function(response) {
                $('#modal-profile .modal-title').html(
                    @if($role == 'ADM') 'Edit Profil Admin'
                    @elseif($role == 'DSN') 'Edit Profil Dosen'
                    @elseif($role == 'TDK') 'Edit Profil Tendik'
                    @elseif($role == 'MHS') 'Edit Profil Mahasiswa'
                    @endif
                );
                $('#modal-profile .modal-body').html(response);
            }
        });
    }
</script>
