<div class="row mb-2">
    <div class="col-sm-6">
        {{-- Menampilkan Page Title --}}
        <h1>{{ $breadcrumb->title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            {{-- Breadcrumb 'Home' biasanya sudah termasuk dalam list --}}
            @foreach ($breadcrumb->list as $key=>$value )
                @if ($key == count($breadcrumb->list)-1)
                <li class="breadcrumb-item active">{{ $value }}</li>
                @else 
                <li class="breadcrumb-item">{{ $value }}</li>
                @endif
            @endforeach
        </ol>
    </div>
</div>