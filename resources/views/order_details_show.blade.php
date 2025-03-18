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
@auth
    <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">Çıkış</button>
    </form>
    @endauth

<div class="container mt-5">
    <h1> {{ $order->id }} ID'li Sipariş Detayları: </h1>
    <table class="table">
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Fiyat</th>
                <th>İade Et</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderLines as $line )
                <tr>
                    <td>{{ $line->product_name }}</td>
                    <td> TL</td>
                    <td>
                    <a href="{{ route('order.returnForm', ['orderId' => $order->id, 'product_sku' => $line->product_sku]) }}" class="btn btn-danger btn-sm">
                            İade Et
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
