@extends('layouts.template')

@section('content')

<div class = "card">
    <div class = "card-header">
        <h3 class = "card-title">Dashboard</h3>
        <div class = "card-tools"></div>
    </div>
    <div class = "card-body">
        Rencananya akan dikasih daftar tugas terbuat, daftar request, daftar tugas yang dimanage dan statusnya.
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