<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayları</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>

@include('layouts.header') 


<div class="container mt-5">
    <h1>Sipariş Detayları:</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Ürün Resmi</th>
                <th>Ürün Adı</th>
                <th>Depo</th>
                <th>Fiyat</th>
                <th>İade Et</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedOrderLines = $order->orderLines->groupBy('store_id');
            @endphp

            @foreach ($groupedOrderLines as $storeId => $lines)
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
                        <td>{{ $line->store_id }}</td>
                        <td>
                            @if($line->product)
                                {{ $line->product->product_price }}TL
                            @else
                                Ürün Bulunamadı.
                            @endif
                        </td>
                        @if ($loop->first)
                            <td rowspan="{{ $lines->count() }}">
                                <a href="{{ route('order.returnForm', ['orderId' => $order->id, 'store_id' => $storeId]) }}" class="btn btn-danger btn-sm">
                                    Siparişi iptal et
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
