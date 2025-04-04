<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün İade Formu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
</head>
<body>
    @include('layouts.header')
    <div class="container mt-5">
        <h2>Ürün İade Formu</h2>

        <div class="mb-4">
            <h4>Sipariş Detayları</h4>
            <p>Sipariş Numarası: {{ $order->id }}</p>
            <p>Sipariş Tarihi: {{ $order->created_at }}</p>

            <h5>Ürünler:</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ürün Adı</th>
                        <th></th>
                        <th>Fiyat</th>
                    </tr>
                </thead>
                <tbody>
                    @if($order->orderLines)
                        @foreach($order->orderLines as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>
                                                    <div class="col-md-6">
                                                        <img src="{{ $item->product->product_image }}"  class="order_image" alt="">
                                                    </div>
                                                </td>
                                <td>{{ $item->product->product_price }} TL</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <p>Toplam Sipariş Tutarı: {{ $order->totalPrice }} TL</p>
        </div>

        <form action="{{ route('order.processReturn') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $orderId }}">
            <input type="hidden" name="store_id" value="{{ $storeId }}">
            <div class="form-group">
                <label for="details">İade Nedeni:</label>
                <textarea name="details" id="details" class="form-control" rows="4" required></textarea>
            </div>

            

            <div class="form-group">
                <label for="return_address">İade Adresi:</label>
                <textarea name="return_address" id="return_address" class="form-control" rows="3" placeholder="İade adresinizi giriniz."></textarea>
            </div>

            <div class="form-group">
                <label for="return_contact">İletişim Bilgileri:</label>
                <input type="text" name="return_contact" id="return_contact" class="form-control" placeholder="Telefon numarası veya e-posta adresiniz.">
            </div>

            <button type="submit" class="btn btn-danger">İade Talebi Gönder</button>
        </form>
    </div>
</body>
</html>