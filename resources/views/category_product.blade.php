<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kategori }} Ürünleri</title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
        
</head>
<body>
@include('layouts.header')   
<div class="container mt-5">
    <h1>{{ $kategori }}</h1>

    <div class="row">
        @foreach($urunler as $urun)
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
                <div class="card shadow-sm custom-card">
                    <a href="{{ route('product.details', ['sku' => $urun->product_sku]) }}">
                        <img src="{{ asset($urun->product_image) }}" class="card-img-top custom-img">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">{{ $urun->product_name }}</h5>
                        <p class="card-text">{{ $urun->product_price }} TL</p>
                        <button type="submit" class="btn btn-primary btn-sm" onclick="addCart('{{ $urun->product_sku }}')">Sepete Ekle</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
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