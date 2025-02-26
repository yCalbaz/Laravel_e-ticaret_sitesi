<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sepet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('layouts.header') 
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center mb-4">Sepet</h2>
            @if(isset($carts) && $carts->count() > 0)
    @foreach($carts as $cart)
        <li>
            <img src="{{ asset('storage/product_images/'.$cart->product_images)}}" alt="{{$cart->product_name }}" width="100">
            <strong>{{ $cart->product_name }}</strong><br>
            Fiyat: {{ $cart->product_price }}
        </li>
    @endforeach
@else
    <p>Sepetiniz boş.</p>
@endif

            
                   

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
