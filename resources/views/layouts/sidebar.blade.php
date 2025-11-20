<div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <!--div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div-->

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{ url('/') }}" class="nav-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }} ">
              <i class="fas fa-home"></i>
              <p>
                Dashboard
              </p>
            </a>
            <!--ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index2.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index3.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v3</p>
                </a>
              </li>
            </ul-->
          </li>
          <li class="nav-item">
            <a href="{{ url('/') }}" class="nav-link {{ ($activeMenu == 'jadwal') ? 'active' : '' }} ">
              <i class="fas fa-clock"></i>
              <p>
                Daftar Jadwal
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('/') }}" class="nav-link {{ ($activeMenu == 'ruangan') ? 'active' : '' }} ">
              <i class="fas fa-door-open"></i>
              <p>
                Daftar Ruangan
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link {{ $activeMenu == 'user' ? 'active' : '' }}">
              <i class="fas fa-users"></i>
              <p>
                Daftar Pengguna
                <i class="fas fa-angle-down"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin') }}" class="nav-link {{ $activeMenu == 'admin' ? 'active' : '' }}">
                  <i class="fas fa-user"></i>
                  <p>Admin</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/dosen') }}" class="nav-link {{ $activeMenu == 'dosen' ? 'active' : '' }}">
                  <i class="fas fa-user"></i>
                  <p>Dosen</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/tendik') }}" class="nav-link {{ $activeMenu == 'tendik' ? 'active' : '' }}">
                  <i class="fas fa-user"></i>
                  <p>Tendik</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/mahasiswa') }}" class="nav-link {{ $activeMenu == 'mahasiswa' ? 'active' : '' }}">
                  <i class="fas fa-user"></i>
                  <p>Mahasiswa</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ url('/prodi') }}" class="nav-link {{ $activeMenu == 'prodi' ? 'active' : '' }}">
              <i class="fas fa-swatchbook"></i>
              <p>
                Daftar Program Studi
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('/kelas') }}" class="nav-link {{ $activeMenu == 'kelas' ? 'active' : '' }}">
              <i class="fas fa-book"></i>
              <p>
                Daftar Kelas
              </p>
            </a>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
