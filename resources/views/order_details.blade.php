<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>

@include('layouts.header') 


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container mt-5">
    <h1>Siparişlerim</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Sipariş ID</th>
                <th>Adres</th>
                <th>Tarih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer_address }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>
                        <a href="{{ route('order.showDetails', $order->id) }}" class="btn btn-info btn-sm">
                            Ayrıntılar
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> 
</div>

</body>
</html>
