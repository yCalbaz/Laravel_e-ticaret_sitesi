<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    @vite(['resources/js/app.js', 'resources/css/header.css', 'resources/css/order.css'])
    <style>.advertisement-container {
    display: flex; /* Yan yana veya alt alta sıralamak için */
    flex-direction: column; /* Alt alta sıralamak için */
    gap: 15px; /* Afişler arasında boşluk */
}

.advertisement-item {
    /* Her bir afiş öğesi için stil (gerekirse) */
    border: 1px solid #eee; /* İsteğe bağlı çerçeve */
    border-radius: 8px; /* İsteğe bağlı köşe yuvarlaklığı */
    overflow: hidden; /* Resimlerin taşımasını engellemek için */
}

.advertisement-image {
    width: 100%; /* Ana div'in genişliğinin tamamını kapla */
    height: auto; /* Oranını koru */
    display: block;
}</style>
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
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
                    <div class="order-info-line">
                        <div class="order-id">Sipariş No: {{ $order->order_id }}</div>
                        <div class="order-status">
                            Durum:
                            @if($order->orderLines->isNotEmpty())
                                @if($order->orderLines->first()->order_status == 'sipariş alındı')
                                    Sipariş Alındı
                                @elseif($order->orderLines->first()->order_status == 'hazırlanıyor')
                                    Hazırlanıyor
                                @elseif($order->orderLines->first()->order_status == 'kargoya verildi')
                                    Kargoya Verildi
                                @elseif($order->orderLines->first()->order_status == 'teslim edildi')
                                    Teslim Edildi
                                @elseif($order->orderLines->first()->order_status == 'iptal edildi')
                                    İptal Edildi
                                @elseif($order->orderLines->first()->order_status == 'iade edildi')
                                    İade Edildi
                                @else
                                    {{ $order->orderLines->first()->order_status }}
                                @endif
                            @else
                                Durum Bilgisi Yok
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('order.showDetails', $order->order_id) }}" class="custom-details-button">
                        Detaylar
                    </a>
                </div>
            </div>
        @endforeach

        {{ $orders->links() }} {{-- Sayfalama linklerini buraya ekleyin --}}
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>