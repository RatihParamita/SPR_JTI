<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"><!-- agar $.ajaxSetup tidak error -->

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        body.login-page{
            background:#f5f5f5;                /* abu muda seperti screenshot */
        }
        .login-box{
            min-width: 360px;
            width: 400px;
            max-width: 420px;
        }
        .login-card{
            border:0;
            border-radius:10px;                /* sudut membulat jelas */
            box-shadow:0 20px 40px rgba(0,0,0,.08);
        }
        .login-card .card-body{
            padding: 32px 36px;
        }

        /* ===== Title ===== */
        .login-title{
            font-weight:800;
            font-size: 36px;                   /* besar seperti di gambar */
            line-height:1.15;
            margin-bottom: 10px;
        }
        .brand-jti{
            background: linear-gradient(90deg,#993A36,#F15B30,#FEC01A);
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent; /* gradasi untuk 'JTI' */
        }
        .login-subtitle{
            color:#757575;                      /* abu medium */
            margin: 6px 0 20px;
        }

        /* ===== Inputs ===== */
        .form-control{
            height: 52px;                       /* input besar */
            border-radius: 12px;
        }
        .input-group-lg > .form-control{
            height: 56px;
        }
        /* sembunyikan ikon di kanan agar bersih seperti screenshot */
        .input-group-append{ display:none; }

        .help-line {
            margin: 6px 0 14px;
            text-align: center;
        }
        .help-link{
            display:block;
            text-align:center;
            margin: 6px 0 16px;
        }

        /* ===== Button ===== */
        .btn-primary{
            width: 100px;
            background-color:#04A9F5 !important;
            border-color:#04A9F5 !important;
            font-weight:700;
            border-radius:5px;
            padding:12px 12px;
            
            /* PENTING: default tanpa shadow & ada transisi */
            box-shadow:none;
            transition: transform .12s ease, box-shadow .12s ease, background-color .12s ease, border-color .12s ease;

            /* Center bila tombolnya display:block */
            display:block;
            margin:0 auto;
        }

        /* Hover: timbul + shadow */
        .btn-primary:hover{
        transform: translateY(-1px);
        box-shadow:0 2px 12px rgba(0, 0, 0, 0.35);
        }

        /* Focus (keyboard): beri ring tipis, tanpa shadow default */
        .btn-primary:focus,
        .btn-primary:focus-visible{
        outline:2px solid #93dafe;
        outline-offset:2px;
        box-shadow:none;
        }

        /* Active: sedikit turun, shadow lebih kecil */
        .btn-primary:active{
        transform: translateY(0);
        box-shadow:0 4px 10px rgba(4,169,245,.28);
        }

        /* Disabled: tidak pakai shadow */
        .btn-primary:disabled{
        box-shadow:none;
        opacity:.7;
        cursor:not-allowed;
        }

        
    </style>
</head>
<body class="hold-transition login-page"> 
    <div class="login-box">
        <div class="card login-card">
            <div class="card-body">
                <div class="text-center">
                    <h1 class="login-title">
                        Sistem Peminjaman Ruangan <span class="brand-jti">JTI</span>
                    </h1>
                    <p class="login-subtitle">
                        Masukkan username dan password (menggunakan NIM/NIDN &amp; password)
                    </p>
                </div>

                <form action="{{ url('login') }}" method="POST" id="form-login">
                    @csrf
                    <div class="input-group input-group-lg mb-3">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username" autocomplete="username" required>
                        <small id="error-username" class="error-text text-danger w-100 mt-1"></small>
                    </div>
                    <div class="input-group input-group-lg mb-2">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" autocomplete="current-password" required>
                        <small id="error-password" class="error-text text-danger w-100 mt-1"></small>
                    </div>

                    {{-- <a class="help-link" href="{{ route('register') }}">Butuh bantuan? <u>Manual</u></a> --}}
                    {{-- <div class="help-line text-center">
                        <span>Butuh bantuan?</span>
                        <a class="help-link" 
                            href="{{ asset('manual/Manual%20Sistem%20Peminjaman%20Ruangan%20JTI.pdf') }}" 
                            target="_blank" rel="noopener"><b>Manual</b></a>
                    </div> --}}
                    <div class="help-line">
                        <span>Butuh bantuan?</span>
                        <a class="help-link">
                            <b>Manual</b></a>
                    </div>

                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- jquery-validation -->
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $("#form-login").validate({
                rules: {
                    username: { required: true, minlength: 4, maxlength: 100 },
                    password: { required: true, minlength: 6, maxlength: 50 }
                },
                messages: {
                    username: {
                        required: "Username harus diisi!",
                    },
                    password: {
                        required: "Password harus diisi!",
                    },
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        dataType: 'json',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                }).then(function() {
                                    window.location = response.redirect;
                                });
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan!',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
    
</body>
</html>
