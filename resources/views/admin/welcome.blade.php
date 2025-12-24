@extends('layouts.template')

@section('content')

<div class = "card">
    <div class = "card-header">
        <h3 class = "card-title" style="font-weight: bold;">Dashboard</h3>
        <div class = "card-tools"></div>
    </div>
    <div class = "card-body">
        Rencananya akan dikasih stat card total ruangan, total jadwal pada hari terkini, total ruangan kosong, dan total pengguna aktif.
        <br>Lalu di bawahnya diberi grafik top 5 ruangan terfavorit, distribusi status peminjam, dan tren peminjaman selama 6 bulan.
    </div>
</div>
@endsection
@push('css')
    
@endpush
@push('js')
    <script>
        function modalAction(url = '') {
        $('#myModal').load(url,function() {
            $('#myModal').modal('show');
        });
    }
    </script>
@endpush