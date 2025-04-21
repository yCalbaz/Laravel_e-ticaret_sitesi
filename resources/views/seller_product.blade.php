<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler</title>
    @vite(['resources/js/app.js' ,'resources/css/seller.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <style>
        .stok-warn {
            color: red;
            font-size: 1.2em;
            margin-left: 5px;
        }
        .stok-ap {
            color: green;
            font-size: 1.2em;
            margin-left: 5px;
        }
        .custom-card {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 250px;
        }

        .add-to-cart-button {
            background-color: #ff671d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: auto;
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            align-self: center;
            text-align: center;
        }

        .add-to-cart-button:hover {
            background-color: #e65c17;
        }
        .select-filter-button {
            background-color: #ff671d;
            color: white;
            border: none;
            border-radius: 3px;
            height: 36px;
            width: 80px;
        }

        .select-filter-button:hover {
            background-color: #e65c17;
        }
        
    </style>
</head>
<body>
@include('layouts.panel_header')
<div class="container mt-5">
    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('seller.products') }}" method="GET">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <select class="form-control" id="stok_durumu" name="stok_durumu">
                            <option value="">Stok Durumu</option>
                            <option value="stokta_var">Stokta Var</option>
                            <option value="stokta_yok">Stokta Yok</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="select-filter-button">Filtrele</button>
                        <a href="{{ route('seller.products') }} " style="color:rgb(231, 192, 173);">Temizle</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        </div>
            <div class="row" id="product-list">
            @forelse($products as $urun)
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 d-flex justify-content-center">
                        <div class="card shadow-sm custom-card">
                        @if (!$urun->stokta_var)
                                        <span class="stok-war" title="Stokta Yok">⚠️ Stok Yok</span>
                                    @elseif ($urun->stokta_var)
                                        <span class="stok-ap" title="Stokta Var">✅Stok Var</span>
                                    @endif
                            <a href="{{ route('stock.create.form', ['product_sku' => $urun->product_sku]) }}">
                                <img src="{{ asset($urun->product_image) }}" class="card-img-top custom-img">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">

                                    {{ $urun->product_name }}
                                </h5>
                                <a href="{{ route('stock.create.form', ['product_sku' => $urun->product_sku]) }}" class="add-to-cart-button">Stok Ekle</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <p>Filtrelemeye uygun ürün bulunamadı.</p>
                    </div>
                @endforelse

        </div>
    </div>
</div>
</body>
</html>