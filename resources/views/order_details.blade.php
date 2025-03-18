<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>
@include('layouts.header') 
@if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

<form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
@auth
    <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">Çıkış</button>
    </form>
    @endauth
    </form>
    <div class="container">
        <h1>Siparişlerim</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Sipariş ID</th> 
                    <th>Adres</th>
                    <th>Fiyat</th>
                    <th>Ürün Adı</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
            
                    @foreach ($order->orderLines as $line)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer_address }}</td>
                            <td>{{ $order->product_price }}</td>
                            <td>{{ $line->product_name }}</td>
                            <td>
                            
                            <a href="{{ route('order.returnForm', ['order_id' => $order->id, 'product_sku' => $line->product_sku]) }}" class="btn btn-danger btn-sm">
                            İade Et
                        
                            </td>
                        </tr>
                    @endforeach
                   
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
