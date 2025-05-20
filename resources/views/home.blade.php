<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>
    @include('layouts.header')
    <div class="container-fluid mt-5 ">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner slay">
                <div class="carousel-item active">
                    <a href="{{ route('urun')}}"><img src="{{ asset('storage/images/slaydir1.png') }}" class="d-block w-100" alt="Slider 1"></a>
                </div>
                <div class="carousel-item">
                    <a href="{{ route('urun')}}"><img src="{{ asset('storage/images/slaydir12.png') }}" class="d-block w-100" alt="Slider 2"></a>
                </div>
                <div class="carousel-item">
                    <a href="{{ route('urun')}}"><img src="{{ asset('storage/images/slaydir13.png') }}" class="d-block w-100" alt="Slider 3"></a>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Geri</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">İleri</span>
            </a>
        </div>
    </div>

    <div class="container mt-5 ">
    
 
        <div class="container mt-5">
            <a href="{{ route('category.product', ['category_slug' => 'adidas']) }}">
                <img src="{{ asset('storage/images/banner15.png') }}" class="img-fluid" alt="Banner">
            </a>
        </div>
        <div class="mt-5 brand-container">
            <div class="row brand-row">
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <a href="{{ route('category.product', ['category_slug' => 'nike']) }}">
                        <img src="{{ asset('storage/images/nike.png') }}" class="img-fluid" alt="nike">
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <a href="{{ route('category.product', ['category_slug' => 'lumberjack']) }}">
                        <img src="{{ asset('storage/images/adidas.png') }}" class="img-fluid" alt="adidas">
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <a href="{{ route('category.product', ['category_slug' => 'adidas']) }}">
                        <img src="{{ asset('storage/images/lumberjack.png') }}" class="img-fluid" alt="lumberjack">
                    </a>
                </div>
            </div>
            
        </div>
        <div class="container mt-5" >
            <h2 style="margin-bottom: 0;">Popüler Ürünler</h2>
        </div>
        <div class="row container mt-5" id="product-list" style="margin-top: 0 !important;"> 
            @foreach($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
                    <div class="card shadow-sm custom-card">
                        <a href="{{ route('product.details', ['sku' => $product->product_sku]) }}">
                            <img src="{{ asset($product->product_image) }}" class="card-img-top custom-img">
                        </a>
                        <div class="card-body"> 
                            <h5 class="card-title">{{ $product->product_name }}</h5>
                            @if ($product->discount_rate > 0 && $product->discounted_price !== null)
                                    <p class="card-text original-price text-danger" style="margin-bottom: 2px;"><del>{{ $product->product_price }} TL</del></p>
                                    <p class=" text-success " style="font-size: 15px; margin-bottom: 2px;">İNDİRİMLİ FİYAT</p>
                                    <p class="  text-success"  style="font-size: 25px; margin-bottom: 2px;">{{ number_format($product->discounted_price, 2) }} TL</p>
                                @else
                                    <p class="card-text">{{ $product->product_price }} TL</p>
                                @endif
                            @csrf
                            <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                            <input type="hidden" name="product_price" value="{{ $product->product_price }}">
                            <input type="hidden" name="product_image" value="{{ $product->image }}">
                            <input type="hidden" name="product_piece" value="1">
                            <button type="button" class="cart-add-btn " onclick="addCart('{{ $product->product_sku }}')">Sepete Ekle</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="container mt-5">
            <div class="row">
            <div class="col-md-6">
                    <img src="{{ asset('storage/images/afis1.png') }}" alt="Afiş 2" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('storage/images/afis2.png') }}" alt="Afiş 1" class="img-fluid"> 
                </div>
                
            </div>
        </div>
        <div class="container mt-5">
            <a href="{{ route('category.product', ['category_slug' => 'adidas']) }}">
                <img src="{{ asset('storage/images/banner12.png') }}" class="img-fluid" alt="Banner">
            </a>
        </div>
        <br>
    </div> 
    @include('layouts.footer')
</body>

</html>