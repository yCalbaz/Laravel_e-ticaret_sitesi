<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

@include('layouts.header')

<div class="container mt-5">
    <h2 class="text-center mb-4">Ürünler</h2>

    @if(isset($products) && $products->count() > 0)
        <div class="row">
        @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
                <div class="card shadow-sm custom-card">
                    <img src="{{ asset($product->product_image) }}" class="card-img-top custom-img">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->product_name }}</h5>
                        <p class="card-text font-weight-bold">{{ $product->product_price }} TL</p>
                        <form action="{{ route('cart.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                            <input type="hidden" name="product_price" value="{{ $product->product_price }}">
                            <input type="hidden" name="product_image" value="{{ $product->image }}">
                            <input type="hidden" name="product_piece" value="1">
                            <button type="submit" class="btn btn-primary btn-sm">Sepete Ekle</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    @else
        <p class="text-center text-muted">Ürün bulunmamaktadır.</p>
    @endif
</div>

<style>
    .custom-footer {
        background-color: #ff671d; 
        color: white; 
        padding: 15px 0;
        text-align: center;
    }

    .custom-card {
        width: 100%; 
        max-width: 250px; 
        margin: 0 auto; 
        height: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center; 
    }

    .custom-img {
        width: 100%; 
        height: 200px; 
        object-fit: cover; 
    }

    .card-body {
        flex-grow: 1; 
        display: flex;
        flex-direction: column;
        justify-content: center; 
        align-items: center;
        padding: 1.5rem;
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin: 10px 0;
    }

    .card-text {
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .btn-sm {
        width: 100%;
        max-width: 150px;
    }

</style>

<footer class="custom-footer">
    <p>&copy; 2025 Şirket Adı - Tüm Hakları Saklıdır.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
