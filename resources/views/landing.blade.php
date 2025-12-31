<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Peminjaman Ruangan JTI</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        /*body {
            /* Background image handling 
            background: url('{{ asset('images/polinema-bg.jpg') }}') no-repeat center center fixed; 
            background-color: #023047; /* Fallback color 
            margin: 0;
            padding: 0;
            background-size: cover;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }*/
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #023047; /* Slightly darker overlay */
            z-index: 1;
        }
        .landing-content {
            position: relative;
            margin-top: 100px;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            padding: 20px;
        }
        .logo-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            width: 180px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
        }
        .btn-login {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 12px 40px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        .btn-login:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.3);
            text-decoration: none; 
            color: #212529;
        }
        h1 {
            /*font-weight: 900;*/
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 0.5rem;
        }
        h3 {
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            margin-bottom: 2rem;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            z-index: 2;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="overlay"></div>

    <div class="landing-content">
        <div class="logo-container">
            <!-- Ensure this path is correct. User showed Logo JTI Polinema.png usage in sidebar -->
            <img src="{{ url('images/Logo JTI Polinema.png') }}" alt="Logo JTI">
        </div>

        <h1 class="display-4" style="font-weight: bold;">Sistem Peminjaman Ruangan</h1>
        <h3>Jurusan Teknologi Informasi - Politeknik Negeri Malang</h3>
        
        <p class="lead mb-5">
            Silakan login untuk melihat jadwal, ruangan, formulir<br>
            peminjaman, dan mengakses fitur lainnya.
        </p>

        <a href="{{ route('login') }}" class="btn btn-login">
            <i class="fas fa-sign-in-alt mr-2"></i> LOGIN
        </a>
    </div>

    <div class="footer">
        &copy; 2025 Jurusan Teknologi Informasi - Politeknik Negeri Malang. All rights reserved.
    </div>

    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
