<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Başlığı</title>
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png" >
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        .fix{
            position: fixed;
            top: 0;
            z-index: 100;
            left: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<nav class="category-menu" style="margin-top: 55px;">
</nav>
<nav class="navbar navbar-expand-lg navbar-light custom-header fix ">
<div class="container mt-5 ">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand" href="/adminPanel">
            <img src="{{ asset('storage/images/flo-logo-Photoroom.png') }}" alt="" height="50">
        </a>
 
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.logout') }}"> Çıkış </a></li>
                @endauth
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}"> Giriş </a></li>
                @endguest
                <li class="nav-item"><a class="nav-link" href="{{route('seller.product')}}">Stok Ekle</a></li>
                
                <li class="nav-item"><a class="nav-link" href="{{route('product.index.form')}}">Ürün Ekle</a></li>
               
            </ul>
        </div>
    </div>
</div>
</nav>
<nav class="category-menu" style="padding-top: 70px;">
</nav>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
