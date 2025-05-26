<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    @vite(['resources/js/app.js', 'resources/css/header.css', 'resources/css/order.css'])
    <style>
        .order-info-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        }

        .order-id {
            font-weight: bold;
        }

        .order-status p {
            margin: 0;
        }

        .order-status-badge {
            padding: 4px 8px;
            border-radius: 5px;
            background-color: #eee;
        }
    </style>
</head>
<body>
        @include('layouts.header')
        <div class="container mt-4">
            <div class="row">
            <div class="col-md-8">
                    <div class="card-header">
                        <h5>Tüm Siparişlerim:</h5> 
                    </div>
                    <div class="card-body">
            @if(isset($orders) && $orders->isNotEmpty())
                @foreach ($orders as $order)
                    <div class="order-item">
                        <div class="order-images-container">
                            @if(isset($order->orderLines) && $order->orderLines->isNotEmpty())
                                @php
                                    $displayedProducts = [];
                                @endphp
                                @foreach ($order->orderLines as $orderLine)
                                    @if($orderLine->product)
                                        @php
                                            $uniqueKey = $orderLine->product->product_sku . '-' . ($orderLine->size ? $orderLine->size->size_name : 'NOSIZE');
                                        @endphp
                                        @if(!in_array($uniqueKey, $displayedProducts))
                                            <div class="order-image-container">
                                                <a href="{{ route('product.details', ['sku' => $orderLine->product->product_sku]) }}">
                                                    <img src="{{ asset($orderLine->product->product_image) }}" alt="{{ $orderLine->product->product_name }}">
                                                </a>
                                            </div>
                                            @php
                                                $displayedProducts[] = $uniqueKey;
                                            @endphp
                                        @endif  
                                    @else
                                        <div class="order-image-container">
                                            <img src="https://via.placeholder.com/80" alt="Ürün Yok">
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p>Bu siparişte ürün bulunmamaktadır.</p>
                            @endif
                        </div>
                        <div class="order-info-container">
                            <div class="order-info-line" >
                                <div class="order-id">Sipariş No: {{ $order->order_id }}</div>
                                <div class="order-status">
                                <p>
                                    <strong>Durum:</strong>
                                    <span class="order-status-badge {{ str_replace(' ', '-', $orderLine->order_status) }}">
                                        {{ $orderLine->order_status }}
                                    </span>
                                </p>
                                </div>
                            </div>
                            <a href="{{ route('order.showDetails', $order->order_id) }}" class="custom-details-button">
                                Detaylar
                            </a>
                        </div>
                    </div>
                @endforeach

                {{ $orders->links() }} 
            @else
                <p>Henüz siparişiniz bulunmamaktadır.</p>
            @endif
            </div></div><div class="col-md-4">
            <div class="advertisement-container">
                <div class="advertisement-item">
                    <img src="{{ asset('storage/images/afis11.png') }}" alt="Avantajlı Afiş 1" class="advertisement-image">
                </div>
                <div class="advertisement-item">
                    <img src="{{ asset('storage/images/afis11.png') }}" alt="Avantajlı Afiş 2" class="advertisement-image">
                </div>
                
                </div>
        </div>
        </div>
    </div>
            @include('layouts.footer')

</body>
</html>