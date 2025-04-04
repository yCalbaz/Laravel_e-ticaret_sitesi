<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<body>

@include('layouts.header') 


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="container mt-5">
    
    

<div class="card mb-3">
            <div class="card-header">
            <h5>Tüm Siparişlerim:</h5>
            </div>
            <div class="card-body">
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
                    <a href="{{ route('order.showDetails', $order->id) }}" class="custom-details-button">
                        Detaylar
                    </a>
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> 
</div>
</div>
</body>
</html>
