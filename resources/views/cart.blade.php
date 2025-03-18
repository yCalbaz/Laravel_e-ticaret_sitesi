<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet</title>
    @vite(['resources/js/app.js' , 'resources/css/style.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body> 

@include('layouts.header')    
<form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
@auth
    <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">Çıkış</button>
    </form>
    @endauth
    </form>
<div class="container mt-5">
    <h2 class="text-center mb-4">Sepetim</h2>
    @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

    <div class="row"> 
        @if(isset($cartItems) && (is_array($cartItems) ? count($cartItems) > 0 : $cartItems->count() > 0))
            @foreach($cartItems as $item)
                <div class="col-12 mb-3"> 
                    <div class="card custom-card-2">
                        <div class="row g-0">
                            <div class="col-md-3"> 
                                <img src="{{ $item->product_image }}" class="img-fluid rounded-start custom-img-2" alt="{{ $item->product_name }}">
                            </div>
                            <div class="col-md-9 d-flex justify-content-between align-items-center"> 
                                <div class="card-body">
                                    
                                    <h5 class="card-title"> {{ $item->product_name }}</h5>
                                    <p class="card-text"> Fiyat: {{ $item->product_price }} TL</p>
                                    <p class="card-text">Adet: {{ $item->product_piece }} </p>
                                </div>
                                
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="cartDelete(' {{ $item->id}}')">Sil</button>
                                
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

            <a href="{{ route('sepet.approvl') }}" class="btn btn-primary">Sepeti Onayla</a>
        @else
            <p class="text-center text-muted">Sepette ürün yok</p>
        @endif
    </div>
</div>

<footer class="custom-footer">
    <p>&copy; 2025 Flo - Tüm Hakları Saklıdır.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function cartDelete(productId){
    $.ajax({
        url:"{{ route('cart.delete', ':id') }}".replace(':id', productId),
        type:"DELETE",
        data:{
            _token: "{{ csrf_token() }}",
        },
        success: function(response){
            alert("Ürün silindi");
            location.reload();
        },
        error: function(xhr ){
            console.log(xhr);
            alert("hata oluştu" + xhr.responseText);
        }

    });}
</script>