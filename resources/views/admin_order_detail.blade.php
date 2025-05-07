<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tüm Siparişler (Admin)</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <style>
        .table td {
        word-break: break-word;
        overflow-wrap: break-word;
        vertical-align: top;
        padding: 5px;
        font-size: 18px; 
        }

        .table td:nth-child(2) {
            max-width: 120px;
            word-break: break-all;
        }

        .table td:nth-child(4) {
            white-space: nowrap;
        }
            @media (max-width: 768px) {
            .table td, .custom-details-button {
                font-size: 12px; 
                padding: 3px; 
            }

            .table td:nth-child(2) {
                max-width: 80px; 
            }
        }
    </style>
</head>
<body>

@include('layouts.panel_header')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container mt-5">
    <div class="card mb-3">
        <div class="card-header">
            <h5>Tüm Siparişler</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sipariş ID</th>
                            <th>Adres</th>
                            <th>İsim</th>
                            <th>Tarih</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->customer_address }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->created_at->format('d.m.Y') }}<br>{{ $order->created_at->format('H:i') }}</td>
                                <td>
                                    <a href="{{ route('order.showAdminDetails', $order->id) }}" class="custom-details-button" style=" width: 80px; /* Buton genişliğini ayarla, değeri ihtiyacına göre değiştir */
        white-space: nowrap;">
                                        Detay
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>
        </div>
    </div><div class="abc">
    {{ $orders->links() }}</div>
</div>

</body>
</html>