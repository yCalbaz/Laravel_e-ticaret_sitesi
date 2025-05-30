<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet</title>
    @vite(['resources/js/app.js', 'resources/css/style.css', 'resources/css/card.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        
    </style>
</head>
<body>
    @include('layouts.header')
    <div class="container mt-5 text center">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <h2>Sepetim ({{ count($cartItems) }} Ürün)</h2>
        <div class="cart-container">
            <div class="cart-items">
                @if(isset($cartItems) && (is_array($cartItems) ? count($cartItems) > 0 : $cartItems->count() > 0))
                    
                    <div class="mb-4">
                        <div class="card custom-card-2" >
                            <div class="row g-0">
                                <div class="col-md-12"> 
                                    <table>
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Ürün İsmi</th> 
                                                <th>Fiyat</th>
                                                <th>Beden</th>
                                                <th>Adet</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cartItems as $item)
                                                <tr>
                                                    <td>
                                                        <div class="col-md-6">
                                                            <img src="{{ $item->product_image }}"  class="order_image" alt="{{ $item->product_name }}">
                                                        </div> 
                                                    </td>
                                                    <td>
                                                        <h5 class="" > {{ $item->product_name }}</h5>
                                                    </td>
                                                    <td>
                                                    @if ($item->discount_rate > 0 && $item->discounted_price !== null)
                                                        <p class="card-text original-price text-danger" style="margin-bottom: 2px;"><del>{{ $item->product_price }} TL</del></p>
                                                        <p class="  text-success"  style="font-size: 25px; margin-bottom: 2px;">{{ number_format($item->discounted_price, 2) }} TL</p>
                                                    @else
                                                        <p class="card-text">{{ $item->product_price }} TL</p>
                                                    @endif
                                                    </td>
                                                    <td>
                                                        <p class=""> {{ $item->size_name }}</p>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <label for="adet-{{ $item['id'] }}" class="me-2"></label>
                                                            <input type="number" name="adet" id="adet-{{ $item['id'] }}" value="{{ $item['product_piece'] }}" min="1" class="form-control form-control-sm" style="width: 50px;"  onchange="updateCart(`{{ $item['id']}}`,this.value)">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <i class="fa fa-trash" style="font-size:24px" onclick="cartDelete('{{ $item->id }}')"></i>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-center text-muted">Sepette ürün yok</p>
                @endif
            </div>
            <div class="cart-summary-container">
                @if(isset($cartItems) && (is_array($cartItems) ? count($cartItems) > 0 : $cartItems->count() > 0))
                    <div class="card custom-card-2">
                        <div class="row g-0">
                            <h5>Sipariş Detayları:</h5>
                            <div class="col-md-12 cart-summary">
                                <p class=> Ürünler: <span id="total-price">{{ $totalPrice }}</span> TL</p>
                                <p class="cargo" > Kargo: <span id="total-price">{{ $cargoPrice }}</span></p>
                                <p class="total-price"> Toplam: <span id="total-price">{{ $cargoTotalPrice }} </span> TL</p>
                                <a href="{{ route('sepet.approvl') }}" class="btn btn-confirm">SEPETİ ONAYLA</a>
                            </div>
                        </div>
                        
                    </div>
                @endif
            </div>
        </div>
        <div class="col-12 text-center mt-3">
            <a href="/" class="btn btn-secondary " style="background-color:rgba(255, 104, 29, 0.47); color: white; border: none;">Alışverişe Geri Dön</a>
        </div>
        <br>
    </div>
    @include('layouts.footer')
</body>
</html>