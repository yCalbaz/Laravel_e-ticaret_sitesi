<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün</title>
    @vite(['resources/css/style.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body> 
@include('layouts.panel_header')
<div class="header">
    <h1>Satıcı Panel</h1>
</div>

<br>

<div class="container text-center">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2>Siparişler</h2>
            @php
                $siparisler = $siparisler ?? []; // siparisler tanımlı değilse boş dizi ata
            @endphp
            @if(count($siparisler) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sipariş ID</th>
                            <th>Depo ID</th>
                            <th>Ürün SKU</th>
                            <th>Adet</th>
                            <th>Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siparisler as $siparis)
                            <tr>
                                <td>{{ $siparis->order_id }}</td>
                                <td>{{ $siparis->store_id }}</td>
                                <td>{{ $siparis->product_sku }}</td>
                                <td>{{ $siparis->product_piece }}</td>
                                <td>{{ $siparis->product_price }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Henüz sipariş yok.</p>
            @endif
        </div>
    </div>
</div>

<style>
    h2{
        color: white;
    }
</style>
</body>
</html>