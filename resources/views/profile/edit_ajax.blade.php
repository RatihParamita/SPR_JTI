<form action="{{ url('profile/update_ajax') }}" method="POST" id="form-edit-profile">
    @csrf
    @method('PUT')
    <div id="modal-master">
        @if($role == 'ADM' || $role == 'DSN')
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        @if($role == 'MHS') NIM @else NIDN @endif
                    </label>
                    <input type="text" name="@if($role == 'MHS') nim @else nidn @endif" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_nidn }} @elseif($role == 'DSN') {{ $data->dosen_nidn }} @elseif($role == 'TDK') {{ $data->tendik_nidn }} @elseif($role == 'MHS') {{ $data->mahasiswa_nim }} @endif"
                        readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah NIM/NIDN.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_nama }} @elseif($role == 'DSN') {{ $data->dosen_nama }} @elseif($role == 'TDK') {{ $data->tendik_nama }} @elseif($role == 'MHS') {{ $data->mahasiswa_nama }} @endif"
                        readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Nama.</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Program Studi</label>
                    <!-- Display Only usually, but could be select if allowed -->
                    <input type="text" class="form-control" value="@if($role == 'TDK') - @elseif($role != 'TDK') {{ $data->prodi->prodi_nama ?? '-' }} @endif" readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Program Studi/Kelas.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_noHp }} @elseif($role == 'DSN') {{ $data->dosen_noHp }} @elseif($role == 'TDK') {{ $data->tendik_noHp }} @elseif($role == 'MHS') {{ $data->mahasiswa_noHp }} @endif">
                    <small id="error-no_hp" class="error-text form-text text-danger"></small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Username.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongi jika tidak ingin ubah">
                    <small id="error-password" class="error-text form-text text-danger"></small>
                </div>
            </div>
        </div>
        @endif

        @if($role == 'TDK')
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        @if($role == 'MHS') NIM @else NIDN @endif
                    </label>
                    <input type="text" name="@if($role == 'MHS') nim @else nidn @endif" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_nidn }} @elseif($role == 'DSN') {{ $data->dosen_nidn }} @elseif($role == 'TDK') {{ $data->tendik_nidn }} @elseif($role == 'MHS') {{ $data->mahasiswa_nim }} @endif"
                        readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah NIM/NIDN.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_noHp }} @elseif($role == 'DSN') {{ $data->dosen_noHp }} @elseif($role == 'TDK') {{ $data->tendik_noHp }} @elseif($role == 'MHS') {{ $data->mahasiswa_noHp }} @endif">
                    <small id="error-no_hp" class="error-text form-text text-danger"></small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_nama }} @elseif($role == 'DSN') {{ $data->dosen_nama }} @elseif($role == 'TDK') {{ $data->tendik_nama }} @elseif($role == 'MHS') {{ $data->mahasiswa_nama }} @endif"
                        readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Nama.</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Username.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongi jika tidak ingin ubah">
                    <small id="error-password" class="error-text form-text text-danger"></small>
                </div>
            </div>
        </div>
        @endif

        @if($role == 'MHS')
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        @if($role == 'MHS') NIM @else NIDN @endif
                    </label>
                    <input type="text" name="@if($role == 'MHS') nim @else nidn @endif" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_nidn }} @elseif($role == 'DSN') {{ $data->dosen_nidn }} @elseif($role == 'TDK') {{ $data->tendik_nidn }} @elseif($role == 'MHS') {{ $data->mahasiswa_nim }} @endif"
                        readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah NIM/NIDN.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_nama }} @elseif($role == 'DSN') {{ $data->dosen_nama }} @elseif($role == 'TDK') {{ $data->tendik_nama }} @elseif($role == 'MHS') {{ $data->mahasiswa_nama }} @endif"
                        readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Nama.</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Program Studi</label>
                    <!-- Display Only usually, but could be select if allowed -->
                    <input type="text" class="form-control" value="@if($role == 'TDK') - @elseif($role != 'TDK') {{ $data->prodi->prodi_nama ?? '-' }} @endif" readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Program Studi/Kelas.</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Kelas</label>
                    <input type="text" class="form-control" value="@if($role == 'MHS') {{ $data->kelas->kelas_nama ?? '-' }} @endif" readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Program Studi/Kelas.</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" 
                        value="@if($role == 'ADM') {{ $data->admin_noHp }} @elseif($role == 'DSN') {{ $data->dosen_noHp }} @elseif($role == 'TDK') {{ $data->tendik_noHp }} @elseif($role == 'MHS') {{ $data->mahasiswa_noHp }} @endif">
                    <small id="error-no_hp" class="error-text form-text text-danger"></small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" readonly>
                    <small class="text-muted">Hubungi Admin untuk ubah Username.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongi jika tidak ingin ubah">
                    <small id="error-password" class="error-text form-text text-danger"></small>
                </div>
            </div>
        </div>
        @endif

        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" onclick="showProfile()">
                <i class="fas fa-times"></i> Batal</button>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Simpan</button>
        </div>
    </div>
</form>

<script>
    function showProfile() {
        $.ajax({
            url: "{{ url('profile/show_ajax') }}", 
            type: "GET",
            success: function(response) {
                $('#modal-profile .modal-title').html(
                    @if($role == 'ADM') 'Profil Admin'
                    @elseif($role == 'DSN') 'Profil Dosen'
                    @elseif($role == 'TDK') 'Profil Tendik'
                    @elseif($role == 'MHS') 'Profil Mahasiswa'
                    @endif
                );
                $('#modal-profile .modal-body').html(response);
            }
        });
    }

    $(document).ready(function() {
        $("#form-edit-profile").validate({
            rules: {
                username: { required: false, minlength: 3 },
                password: { minlength: 5 },
                nama: { required: false, minlength: 3 },
                // nidn/nim dynamic checks ideally
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.msg,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            $('.error-text').text('');
                            $.each(response.errors, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.msg
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
        // Pastikan SweetAlert2 sudah dimuat sebelum digunakan
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 library not loaded. Please ensure it is included in your template.');
        }
    });
</script>
