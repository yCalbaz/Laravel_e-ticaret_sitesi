<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayları</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>

@include('layouts.header') 
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
<div class="container mt-5">
    <h1>Sipariş Detayları:</h1>
    @php
        $groupedOrderLines = $order->orderLines->groupBy('store_id');
    @endphp

    @foreach ($groupedOrderLines as $storeId => $lines)
        <div class="card mb-3">
            <div class="card-header">
            Satıcı: {{ $lines->first()->store->depo_name ?? 'Satıcı Bilgisi Bulunamadı' }}
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr> 
                            <th>Ürün Resmi</th>
                            <th>Ürün Adı</th>
                            <th>Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lines as $line)
                            <tr>
                                <td>
                                    @if($line->product)
                                        <img src="{{ asset($line->product->product_image) }}" class="order_image">
                                    @else
                                        Ürün Bulunamadı.
                                    @endif
                                </td>
                                <td>{{ $line->product_name }}</td>
                                <td>
                                    @if($line->product)
                                        {{ $line->product->product_price }}TL
                                    @else
                                        Ürün Bulunamadı.
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('order.returnForm', ['orderId' => $order->id, 'store_id' => $storeId]) }}" class="btn btn-danger btn-sm">
                    Siparişi İptal Et
                </a>
            </div>
        </div>
    @endforeach
</div>

</body>
</html>