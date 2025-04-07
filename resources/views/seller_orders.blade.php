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
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <span>Sipariş İD: {{ $orderId }}</span>
                <form method="POST" action="{{ route('seller.updateLineStatus', ['lineId' => $orderLines->first()->id]) }}" class="form-inline">
                    @csrf
                    <select name="order_status" class="form-control form-control-sm mr-2 mb-2">
                        <option value="sipari alındı" {{ $orderLines->first()->order_status == 'sipari alındı' ? 'selected' : '' }}>Sipariş Alındı</option>
                        <option value="hazırlanıyor" {{ $orderLines->first()->order_status == 'hazırlanıyor' ? 'selected' : '' }}>Hazırlanıyor</option>
                        <option value="kargoya verildi" {{ $orderLines->first()->order_status == 'kargoya verildi' ? 'selected' : '' }}>Kargoya Verildi</option>
                        <option value="iptal edildi" {{ $orderLines->first()->order_status == 'iptal edildi' ? 'selected' : '' }}>İptal Edildi</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm mb-2">Güncelle</button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ürün Resmi</th>
                                <th>Ürün Adı</th>
                                
                                <th>Adres</th>
                                <th>Sipariş Durumu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderLines as $line)
                                <tr>
                                    <td>
                                        @if(App\Models\Product::where('product_sku', $line->product_sku)->first())
                                            <img src="{{ asset(App\Models\Product::where('product_sku', $line->product_sku)->first()->product_image) }}" class="order_image img-fluid">
                                        @else
                                            Ürün Bulunamadı.
                                        @endif
                                    </td>
                                    <td>{{ $line->product_name }}</td>
                                    <td>
                                        @if(App\Models\OrderBatch::where('id', $line->order_id)->first())
                                            {{ App\Models\OrderBatch::where('id', $line->order_id)->first()->customer_address }}
                                        @else
                                            Adres Bulunamadı
                                        @endif
                                    </td>
                                    <td>
                                        {{ $line->order_status }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>

</body>
</html>