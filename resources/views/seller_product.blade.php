<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler</title>
    @vite(['resources/js/app.js' ,'resources/css/seller.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   
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
                                <p class="card-text" style="font-size: 15px; margin-bottom: 2px;">Fiyat: {{ $urun->product_price }} TL</p>

                                @if ($urun->discount_rate > 0)
                                    @php
                                        $indirimliFiyat = $urun->product_price - ($urun->product_price * ($urun->discount_rate / 100));
                                    @endphp
                                    <p class="text-success"style="font-size: 14px; margin-bottom: 2px;"> İndirim Oranı: (%{{ $urun->discount_rate }})</p>
                                    <p class="text-success"style="font-size: 25px; margin-bottom: 2px;"> {{ number_format($indirimliFiyat, 2) }} TL</p>
                                @endif
                                <div>
                                <a href="{{ route('stock.create.form', ['product_sku' => $urun->product_sku]) }}" class="add-to-cart-button">Stok Ekle</a> </div><br>
                                <div class="kampanya-ekle-button-container">
                                    <button type="button" class="btn btn-sm btn-warning mt-2 kampanya-ekle-button" data-toggle="modal" data-target="#kampanyaEkleModal{{ $urun->id }}">
                                    Kampanya Ekle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="kampanyaEkleModal{{ $urun->id }}" tabindex="-1" role="dialog" aria-labelledby="kampanyaEkleModalLabel{{ $urun->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="kampanyaEkleModalLabel{{ $urun->id }}">Kampanya Ekle</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('seller.products.kampanya.ekle', ['id' => $urun->id]) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="discount_rate">İndirim Oranı (%)</label>
                                            <input type="number" class="form-control" id="discount_rate" name="discount_rate" min="0" max="100" step="0.01">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                        <button type="submit" class="btn btn-primary">Kaydet</button>
                                    </div>
                                </form>
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