<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <!--li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>-->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      @auth
        <!-- Profile Menu -->
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="#" onclick="showProfileModal()" role="button">
                <span class="mr-2 d-none d-md-inline text-dark font-weight-bold">{{ Auth::user()->getNamaPembuatAttribute() }}</span> <!-- Helper accessor if available, or fetch manually -->
                <i class="fas fa-user-circle fa-lg text-dark"></i>
            </a>
        </li>
        <!-- Logout Button -->
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center text-danger" href="{{ route('logout') }}" role="button">
                <i class="fas fa-sign-out-alt mr-1"></i> 
                <span class="d-none d-md-inline font-weight-bold">Logout</span>
            </a>
        </li>
      @endauth

      @guest
        <li class="nav-item">
            <a class="nav-link font-weight-bold text-primary" href="{{ route('login') }}" role="button">
                <i class="fas fa-sign-in-alt mr-1"></i> Login
            </a>
        </li>
      @endguest
    </ul>
</nav>

<!-- Profile Modal -->
<div class="modal fade" id="modal-profile">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="font-weight: bold;">
                  @if (Auth::user()->getRole() == 'ADM')
                    Profil Admin
                  @elseif (Auth::user()->getRole() == 'DSN')
                    Profil Dosen
                  @elseif (Auth::user()->getRole() == 'TDK')
                    Profil Tendik
                  @elseif (Auth::user()->getRole() == 'MHS')
                    Profil Mahasiswa
                  @endif
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content loaded via AJAX -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    function showProfileModal() {
        $('#modal-profile').modal('show');
        $.ajax({
            url: "{{ url('profile/show_ajax') }}", 
            type: "GET",
            success: function(response) {
                $('#modal-profile .modal-body').html(response);
            },
            error: function() {
                $('#modal-profile .modal-body').html('<p class="text-danger text-center">Gagal memuat data profil.</p>');
            }
        });
    }
</script>
@endpush