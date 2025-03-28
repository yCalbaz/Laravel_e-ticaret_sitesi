<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depo Seçimi</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>

@include('layouts.panel_header')

<div class="container mt-5">
    <h2>Depolarım</h2>

    @if(count($stores) > 0)
        <div class="row">
            @foreach ($stores as $store)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header">
                            {{ $store->store_name }}
                        </div>
                        <div class="card-body">
                            <a href="{{ route('seller.orders', ['storeId' => $store->id]) }}" class="btn btn-primary">Gelen Siparişler</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>Yetkili olduğunuz depo bulunamadı.</p>
    @endif
</div>

</body>
</html>