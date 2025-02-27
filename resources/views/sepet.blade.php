<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body> 

@include('layouts.header')   
<div class="container mt-5">
    <h2 class="text-center mb-4">Sepetim</h2>

    <div class="row"> 
        @if(isset($cartItems) && $cartItems->count() > 0)
            @foreach($cartItems as $item)
                <div class="col-12 mb-3"> 
                    <div class="card custom-card">
                        <div class="row g-0">
                            <div class="col-md-3"> 
                                <img src="{{ $item->product_image }}" class="img-fluid rounded-start custom-img" alt="{{ $item->product_name }}">
                            </div>
                            <div class="col-md-9 d-flex justify-content-between align-items-center"> 
                                <div class="card-body">
                                    
                                    <h5 class="card-title"> {{ $item->product_name }}</h5>
                                    <p class="card-text"> Fiyat: {{ $item->product_price }} TL</p>
                                    <p class="card-text">Adet: {{ $item->product_piece }} </p>
                                </div>
                                <form action="{{ route('cart.delete', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @php
                $totalPrice = 0;
                foreach($cartItems as $item){
                    $totalPrice += ($item->product_price * $item->product_piece);
                }
            @endphp
            <p class="text-right font-weight-bold"> Toplam: {{ $totalPrice }} TL</p>

            <form action="{{ route('sepet.approvl') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Sepeti Onayla</button>
            </form>
        @else
            <p class="text-center text-muted">Sepette ürün yok</p>
        @endif
    </div>
</div>

<style>
    .custom-footer {
        background-color: #ff671d; 
        color: white; 
        padding: 15px 0;
        text-align: center;
    }

    .custom-card {
        max-width: 50%; 
    }

    .custom-img {
        height: 120px; 
        object-fit: cover;
    }
</style>

<footer class="custom-footer">
    <p>&copy; 2025 Flo - Tüm Hakları Saklıdır.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>