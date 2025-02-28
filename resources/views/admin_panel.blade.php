<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body> 


@include('layouts.header')
<div class="header">
    <h1>Admin Panel</h1>

    <!-- Çıkış Butonu Sağ Üste -->
    <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn" >Çıkış</button>
    </form>
</div>

<br>

<div class="container text-center">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <a href="{{ route('product.create.form') }}" class="panel-box">
                <div class="box">
                    <h2>Ürün Paneli</h2>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('store.create.form' )}}" class="panel-box">
                <div class="box">
                    <h2>Depo Paneli</h2>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('stock.create.form' )}}" class="panel-box">
                <div class="box">
                    <h2>Stok Paneli</h2>
                </div>
            </a>
        </div>
   
    </div>
</div>



<style>
    body {
        background-color: white;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .header {
        background-color: white; 
        padding: 20px;
        text-align: center;
        position: relative; 
    }

    .logout-form {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .logout-btn {
        background-color: red;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .logout-btn:hover {
        background-color: darkred;
    }

    .container {
        flex: 1;
    }

    .panel-box {
        text-decoration: none;
    }

    .box {
        background-color: #ff671d; /* Kutular turuncu */
        padding: 50px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .box:hover {
        transform: translateY(-5px);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
    }

    h2 {
        color: white; /* Yazılar beyaz */
    }
</style>
</body>
</html>
