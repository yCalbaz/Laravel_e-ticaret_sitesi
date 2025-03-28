<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Başlığı</title>
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>

<nav class="category-menu">
<div class="container">
</div>
</nav>

<nav class="navbar navbar-expand-lg navbar-light custom-header">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('storage/images/flo-logo-Photoroom.png') }}" alt="" height="50">
        </a>
    
        
        
        <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item"><a class="nav-link" href="{{route('admin.logout')}}"> Çıkış Yap</a></li> 
   
    @endauth   
    @guest
    <li class="nav-item"><a class="nav-link" href="{{route('login')}}"> Giriş Yap</a></li>
    @endguest
            <li class="nav-item"><a class="nav-link" href="{{route('stock.index.form')}}">Stok Ekle</a></li>
                
            <li class="nav-item"><a class="nav-link" href="{{route('product.index.form')}}">Ürün Ekle</a></li>
           
        </ul>
    </div>
    
</nav>

<nav class="category-menu">
    <div class="container">
</nav>


</body>
</html>