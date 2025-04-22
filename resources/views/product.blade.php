<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler</title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">

</head>
<body>
@include('layouts.header')   
<div class="container mt-5">
    <div class="row">
</div>
            <div class="row" id="product-list">
            @foreach($urunler as $urun)
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 d-flex justify-content-center">
                        <div class="card shadow-sm custom-card">
                            <a href="{{ route('product.details', ['sku' => $urun->product_sku]) }}">
                                <img src="{{ asset($urun->product_image) }}" class="card-img-top custom-img">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $urun->product_name }}</h5>
                                @if ($urun->discount_rate > 0 && $urun->discounted_price !== null)
                                    <p class="card-text original-price text-danger" style="margin-bottom: 2px;"><del>{{ $urun->product_price }} TL</del></p>
                                    <p class=" text-success " style="font-size: 15px; margin-bottom: 2px;">İNDİRİMLİ FİYAT</p>
                                    <p class="  text-success"  style="font-size: 25px; margin-bottom: 2px;">{{ number_format($urun->discounted_price, 2) }} TL</p>
                                @else
                                    <p class="card-text">{{ $urun->product_price }} TL</p>
                                @endif
                                <button type="submit" class=" cart-add-btn" onclick="addCart('{{ $urun->product_sku }}')">Sepete Ekle</button>
                            </div> 
                        </div>
                    </div>
                @endforeach
                
        </div>
    </div>
</div>
@include('layouts.footer')
</body>
</html>
