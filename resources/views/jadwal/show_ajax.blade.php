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
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Data Jadwal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            
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
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </button>
        </div>
    </div>
@endempty
