<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('layouts.panel_header')
    <div class="container">
        <h1>Sipariş Listesi</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Sipariş ID</th> 
                    <th>Müşteri Adı</th>
                    <th>Müşteri Adresi</th>
                    <th>Tarih</th>
                    <th>Ürün Adı</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
            
                    @foreach ($order->orderLines as $line)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->customer_address }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>{{ $line->product_sku }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>