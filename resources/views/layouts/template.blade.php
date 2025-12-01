<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sistem Peminjaman Ruangan')</title>

  <style>
        /* CSS Kustom Anda */
        #page-title {
            font-weight: 700;
            font-size: 36px;
            line-height: 120%;
            letter-spacing: -0.02em;

            color: #023047;
            z-index: 1000; 
        }

        #custom-sidebar {
          background-color: #023047 !important;
        }

        .brand-link .brand-text {
            color: #FFFFFF !important; 
        }

        .sidebar .form-inline {
          border-top: 1px solid #01496E !important; 
          padding-top: 1rem !important; 
          margin-bottom: 1rem !important; 
        }

        .sidebar .form-inline .form-control-sidebar {
            background-color: #01496E !important; 
            color: #FFFFFF; 
            border-color: #01496E !important; 
        }

        .sidebar .form-inline .btn-sidebar {
            background-color: #01496E !important; 
            border-color: #01496E !important;
          }

        .sidebar .form-inline .btn-sidebar i {
            color: #B3B3B3 !important; 
        }

        .nav-sidebar .nav-link.active {
          background-color: #219EBC !important; 
          color: #FFFFFF !important; 
        }

        .nav-sidebar .nav-item:not(.menu-open) > .nav-link:not(.active):hover {
          background-color: #0477B1; 
          opacity: 0.8; 
        }

        .nav-sidebar .nav-link {
          transition: background-color 0.3s ease;
        }

        .nav-sidebar .nav-link:not(.active),
        .nav-sidebar .nav-link:not(.active) p,
        .nav-sidebar .nav-link:not(.active) i {
          color: #FFFFFF !important; 
        }
    </style>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  @stack('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed" data-panel-auto-height-mode="height">
<div class="wrapper">

  <!-- Navbar -->
  @include('layouts.header')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar elevation-4" id="custom-sidebar">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
      <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    
      <span class="brand-text font-weight-light">PWL - Starter Code</span>
    </a>

    <!-- Sidebar -->
    @include('layouts.sidebar')
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      @include('layouts.breadcrumb')
      
      <!-- Main content -->
      <section class="content">
        @yield('content')
        
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  @include('layouts.footer')
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/adminlte.js?v=3.2.0') }}"></script>
</body>
</html>
