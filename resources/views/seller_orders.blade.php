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

@include('layouts.panel_header')

<div class="container mt-5">
    <h2>Gelen Siparişler</h2>

    @php
        $groupedByOrder = collect($siparisler)->groupBy('order_id');
    @endphp

    @foreach ($groupedByOrder as $orderId => $orderLines)
    <div class="card mb-3">
        <div class="card-header">
            <span>Sipariş İD: {{ $orderId }}</span>
        </div>
        <div class="card-body">
            @php
                $groupedByStore = $orderLines->groupBy('store_id');
            @endphp

            @foreach ($groupedByStore as $storeId => $storeOrderLines)
                <h5>Depo: {{ App\Models\Store::find($storeId)->store_name ?? 'Bilinmeyen Depo' }}</h5>
                <div class="d-flex justify-content-end mb-2">
                    @if ($storeOrderLines->contains(function ($line) { return $line->order_status == 'iptal talebi alındı'; }))
                        <form method="POST" action="{{ route('seller.approveCancellation') }}" class="form-inline">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $orderId }}">
                            <input type="hidden" name="store_id" value="{{ $storeId }}">
                            <button type="submit" class="btn btn-warning btn-sm">Bu Depodaki İptal Taleplerini Onayla</button>
                        </form>
                    @elseif (!$storeOrderLines->contains(function ($line) { return $line->order_status == 'iptal talebi onaylandı'; }))
                        <form method="POST" action="{{ route('seller.updateLineStatusForStore') }}" class="form-inline">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $orderId }}">
                            <input type="hidden" name="store_id" value="{{ $storeId }}">
                            <select name="order_status" class="form-control form-control-sm mr-2">
                                <option value="sipari alındı" {{ $storeOrderLines->every(function ($line) { return $line->order_status == 'sipari alındı'; }) ? 'selected' : '' }}>Sipariş Alındı</option>
                                <option value="hazırlanıyor" {{ $storeOrderLines->every(function ($line) { return $line->order_status == 'hazırlanıyor'; }) ? 'selected' : '' }}>Hazırlanıyor</option>
                                <option value="kargoya verildi" {{ $storeOrderLines->every(function ($line) { return $line->order_status == 'kargoya verildi'; }) ? 'selected' : '' }}>Kargoya Verildi</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Tümünü Güncelle</button>
                        </form>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Ürün Resmi</th>
                                <th>Ürün Adı</th>
                                <th>Adet</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($storeOrderLines as $line)
                                <tr>
                                    <td>
                                        @if(App\Models\Product::where('product_sku', $line->product_sku)->first())
                                            <img src="{{ asset(App\Models\Product::where('product_sku', $line->product_sku)->first()->product_image) }}" class="order_image img-fluid">
                                        @else
                                            Ürün Bulunamadı.
                                        @endif
                                    </td>
                                    <td>{{ $line->product_name }}</td>
                                    <td>{{ $line->quantity }}</td>
                                    <td>{{ $line->order_status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
@endforeach
</div>

</body>
</html>