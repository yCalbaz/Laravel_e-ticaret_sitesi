<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayları</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>

    @include('layouts.header')

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                @foreach ($groupedOrderLines as $storeId => $storeData)
                    <div class="card mb-3">
                        <div class="card-header">
                            Satıcı: {{ $storeData['store']->store_name ?? 'Satıcı Bilgisi Bulunamadı' }} <br>
                            Sipariş Numarası: {{ $storeData['lines']->first()->order_id }}
                            <span style="margin: 0 20px">
                                @if($storeData['isCancelable'])
                                    <button class="iptalEtBtnClass custom-details-button" data-id="{{ $order->order_id }}" data-store-id="{{ $storeId }}">
                                        İade Et
                                    </button>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Ürün Resmi</th>
                                            <th>Ürün Adı</th>
                                            <th>Beden</th>
                                            <th>Adet</th>
                                            <th>Fiyat</th>
                                            <th>Sipariş Durumu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($storeData['lines'] as $line)
                                            <tr>
                                                <td>
                                                    @if($line->product)
                                                        <a href="{{ route('product.details', ['sku' => $line->product->product_sku]) }}">
                                                            <img src="{{ asset($line->product->product_image) }}" class="order_image">
                                                        </a>
                                                    @else
                                                        Ürün Bulunamadı.
                                                    @endif
                                                </td>
                                                <td>{{ $line->product_name }}</td>
                                                <td>{{ $line->size->size_name }}</td>
                                                <td>{{ $line->product_piece }}</td>
                                                <td>
                                                    @if($line->product)
                                                        {{ $line->product->product_price }}TL
                                                    @else
                                                        Ürün Bulunamadı.
                                                    @endif
                                                </td>
                                                <td>{{ $line->order_status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Sipariş Takibi
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach ($allOrderStatuses as $status)
                                @php
                                    $isActive = in_array($status, $orderStatusHistory);
                                @endphp
                                <div class="timeline-item {{ $isActive ? 'active' : '' }}">
                                    <div class="timeline-text">
                                        {{ $status }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        Teslimat Adresi
                    </div>
                    <div >
                        <p> {{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

</body>
</html>