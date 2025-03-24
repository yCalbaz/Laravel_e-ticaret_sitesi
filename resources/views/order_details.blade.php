<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
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

<div class="container mt-5">
    <h1>Siparişlerim</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Sipariş ID</th>
                <th>Adres</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer_address }}</td>
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
