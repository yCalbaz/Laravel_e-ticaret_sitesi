<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama Sonuçları</title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    @include('layouts.header')

    <div class="container mt-5">
        <h2>Arama Sonuçları: "{{ $query }}"</h2>

        @if($products->count() > 0)
            <div class="row">
                @foreach($products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
                        <div class="card shadow-sm custom-card">
                        <a href="{{ route('product.details', ['sku' => $product->product_sku]) }}"><img src="{{ asset($product->product_image) }}" class="card-img-top custom-img"></a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->product_name }}</h5>
                                <p class="card-text font-weight-bold">{{ $product->product_price }} TL</p>
                                <button type="submit" class="cart-add-btn" onclick="addCart( '{{$product->product_sku }}')">Sepete Ekle</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>Aradığınız kriterlere eşleşen ürün yok.</p>
        @endif
    </div>

  @include('layouts.footer')
</body>
</html>
