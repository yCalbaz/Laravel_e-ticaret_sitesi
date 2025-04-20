<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler</title>
    @vite(['resources/js/app.js' ,'resources/css/seller.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">

</head>
<body>
@include('layouts.panel_header')   
<div class="container mt-5">
    <div class="row">
</div>
            <div class="row" id="product-list">
            @foreach($urunler as $urun)
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 d-flex justify-content-center">
                        <div class="card shadow-sm custom-card">
                            <a href="{{ route('stock.create.form', ['product_sku' => $urun->product_sku]) }}">
                                <img src="{{ asset($urun->product_image) }}" class="card-img-top custom-img">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $urun->product_name }}</h5>
                                <a href="{{ route('stock.create.form', ['product_sku' => $urun->product_sku]) }}" class="btn btn-sm btn-outline-primary mt-2">Stok Ekle</a>
                            </div>
                        </div>
                    </div>
                @endforeach
                
        </div>
    </div>
</div>
</body>
</html>
