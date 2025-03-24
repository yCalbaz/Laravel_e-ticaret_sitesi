<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->product_name }} - Ürün Detayı</title>

    @vite(['resources/js/app.js', 'resources/css/style.css', 'resources/css/product_details.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <p class="product-description"><strong>Açıklama:</strong> {{ $product->details }}</p>

            <button type="button" class="add-to-cart-button" onclick="addCart('{{ $product->product_sku }}')">Sepete Ekle</button>
        </div>
    </div>
</div>

<footer class="custom-footer">
    
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function addCart(productSku) {
        $.ajax({
            url: "{{ route('cart.add', ':sku') }}".replace(':sku', productSku),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                quantity: 1
            },
            success: function (response) {
                console.log("Başarıyla eklendi:", response);
                alert("Ürün sepete eklendi!");
            },
            error: function (xhr) {
                console.log("Hata oluştu! Durum kodu:", xhr.status);
                console.log("Hata mesajı:", xhr.responseText);
                alert("Hata oluştu! " + xhr.responseText);
            }
        });
    }
</script>

</body>
</html>