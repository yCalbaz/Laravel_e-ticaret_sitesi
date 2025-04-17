<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depo Seçimi</title>
    @vite(['resources/js/app.js','resources/css/seller.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        
    </style>
</head>
<body>

@include('layouts.panel_header')

<div class="container mt-5">
    <h2>Depolarım</h2>

    @if(count($stores) > 0)
        <div class="row">
            @foreach ($stores as $store)
                @php
                    $hasNewOrders = \App\Models\OrderLine::where('store_id', $store->id)
                        ->where('order_status', 'sipariş alındı') 
                        ->exists();

                    $hasRefundRequests = \App\Models\OrderLine::where('store_id', $store->id)
                        ->where('order_status', 'iptal talebi alındı')
                        ->exists();
                @endphp
                <div class="col-md-4 mb-3">
                    <div class="card {{ $hasNewOrders ? 'has-new-orders' : '' }} {{ $hasRefundRequests ? 'has-refund-requests' : '' }}">
                        <div class="card-header">
                            <span class="card-header-text">{{ $store->store_name }}</span>
                            <div class="card-header-icons">
                                @if($hasRefundRequests)
                                    @endif
                                @if($hasNewOrders)
                                    @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('seller.orders', ['storeId' => $store->id]) }}" class="order-store-button">Gelen Siparişler</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>Yetkili olduğunuz depo bulunamadı.</p>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-4 mx-2 mb-4">
            <a href="{{ route('seller.product') }}" class="panel-box">
                <div class="box">
                    <h2>Ürünlerim</h2>
                </div>
            </a>
        </div>
        
        
        <div class="col-md-4 mx-2 mb-4">
            <a href="{{ route('store.index.form' )}}" class="panel-box">
                <div class="box">
                    <h2>Depo Ekle</h2>
                </div>
            </a>
        </div>
        
    </div>
</div>

</body>
</html>