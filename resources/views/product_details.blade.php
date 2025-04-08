<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->product_name }} - Ürün Detayı</title>

    @vite(['resources/js/app.js', 'resources/css/style.css', 'resources/css/product_details.css'])
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">

    <style>
        
    </style>
</head>
<body>
 
@include('layouts.header')

<div class="container product-detail-container">
    <div class="row">
        <div class="col-md-6 product-image-container">
            <img src="{{ asset($product->product_image) }}" class="product-image" alt="{{ $product->product_name }}">
        </div>
        <div class="col-md-6 product-info">
            <h2>{{ $product->product_name }}</h2>
            <p class="product-price">{{ $product->product_price }} TL</p>
            <p class="product-description">{{ $product->details }}</p>
            <h5>Beden Seçimi:</h5>
            <div class="size">
                @foreach ($groupedStocks as $stock)
                    @if ($stock['total_piece'] > 0 && $stock['size'])
                        <button data-size-id="{{ $stock['size']->id }}" class="size-button">{{ $stock['size']->size_name }}</button>
                    @elseif ($stock['size'])
                        <button class="size-button" style="text-decoration: line-through; opacity: 0.5; cursor: not-allowed;" disabled>{{ $stock['size']->size_name }}</button>
                    @endif
                @endforeach
            </div>
            <br>

            <button type="button" class="add-to-cart-button" onclick="addCart('{{ $product->product_sku }}')">Sepete Ekle</button>
        </div>
    </div>
</div>

@include('layouts.footer')


</body>
</html>