<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün</title>
    @vite(['resources/css/style.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body> 

@include('layouts.header')   
<div class="container mt-5">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

<div class="row"> 
    @foreach($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
           <div class="card shadow-sm custom-card">
                <img src="{{ asset($product->product_image) }}" class="card-img-top custom-img">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->product_name }}</h5>
                    <p class="card-text font-weight-bold">{{ $product->product_price }} TL</p>
                    <form action="{{ route('cart.add',$product)}}" method="POST">
                        @csrf
                        <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                        <input type="hidden" name="product_price" value="{{ $product->product_price }}">
                        <input type="hidden" name="product_image" value="{{ $product->image }}">
                        <input type="hidden" name="product_piece" value="1">
                            
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm">Sepete Ekle</button>
                        </form>
                     
                </div>
            </div>
        </div>
    @endforeach
</div>
</div>
<

<footer class="custom-footer">
    <p>&copy; 2025 Şirket Adı - Tüm Hakları Saklıdır.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
