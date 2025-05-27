<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün</title>
    @vite(['resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <style>
        h2{
            color: white;
        }
    </style>
</head>
<body> 
    @include('layouts.admin_header')
    <div class="header">
    <br><br>
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-4 mx-2 mb-4">
                <a href="{{ route('members.index' )}}" class="panel-box">
                    <div class="box">
                        <h2>Kullanıcılar</h2>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mx-2 mb-4">
                <a href="{{ route('orders.indexAdmin' )}}" class="panel-box">
                    <div class="box">
                        <h2>Sipariş Bilgileri</h2>
                    </div>
                </a>
            </div>
            
        </div>
    </div>
</body>
</html>
