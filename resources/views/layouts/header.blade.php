<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Başlığı</title>
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png" >
  
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
    
        
        <form class="search-form" id="searchForm" action="{{ route('search') }}" method="GET">
            <input class="search-input" type="search" name="query" placeholder="Ara" aria-label="Ara">
        </form>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="{{route('login')}}"><i class="fas fa-user"></i> Giriş Yap</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('sepet.index')}}">
                    <i class="fas fa-shopping-cart"></i> Sepetim
                    <span id="sepet-sayisi" class="badge bg-danger">{{ $sepetSayisi ?? 0 }}</span>
                </a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('orders.index')}}"><i class="fas fa-shopping-cart"></i>Siparişlerim</a></li>
            @auth
    <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">Çıkış</button>
    </form>
    @endauth
        </ul>
    </div>
    
</nav>

<nav class="category-menu">
    <div class="container">
    <div class="filter-container">
    <div class="filter-dropdown">
        <button class="filter-button">Kadın <span class="arrow">▼</span></button>
        <div class="filter-options category">
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'kadın-çanta']) }}">Kadın Çanta</a></li>
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'kadın-ayakkabı']) }}">Kadın Ayakkkabı</a></li>
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'kadın-giyim']) }}">Kadın Giyim</a></li>
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'günlük-ayakkabı']) }}">Günlük Ayakkabı</a></li>
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'ayakkabı']) }}">Spor Ayakkabı</a></li>
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'canta']) }}">Bot</a></li>
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'giyim']) }}">Tişört</a></li>
            <li><a class="category" href="{{ route('category.product', ['category_slug' => 'ayakkabı']) }}">Eşofman</a></li>
         </div>
    </div>
    <div class="filter-dropdown">
        <button class="filter-button">Erkek <span class="arrow">▼</span></button>
        <div class="filter-options">
        <li><a class="category" href="{{ route('category.product', ['category_slug' => 'erkek-ayakkabı']) }}">Erkek Ayakkkabı</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'erkek-giyim']) }}">Erkek Giyim</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'günlük-ayakkabı']) }}">Günlük Ayakkabı</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'ayakkabı']) }}">Spor Ayakkabı</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'canta']) }}">Bot</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'giyim']) }}">Tişört</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'ayakkabı']) }}">Eşofman</a></li>
        </div>
    </div>
    <div class="filter-dropdown">
        <button class="filter-button">Çocuk <span class="arrow">▼</span></button>
        <div class="filter-options ">
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'çocuk-ayakkabi']) }}">Çocuk Ayakkkabı</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'çocuk-giyim']) }}">Çocuk Giyim</a></li>
        <li><a class="category" href="{{ route('category.product', ['category_slug' => 'günlük-ayakkabı']) }}">Günlük Ayakkabı</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'ayakkabı']) }}">Spor Ayakkabı</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'canta']) }}">Bot</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'giyim']) }}">Tişört</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'ayakkabı']) }}">Eşofman</a></li>
         </div>
    </div>
    <div class="filter-dropdown">
        <button class="filter-button">Marka <span class="arrow">▼</span></button>
        <div class="filter-options">
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'nike']) }}">Nike</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'adidas']) }}">Adidas</a></li>
        <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'lumberjack']) }}">Lumberjack</a></li>
            
        </div>
    </div>
    
    
    </div>
</nav>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        if (!this.querySelector('input[name="query"]').value) {
            event.preventDefault();
        }
    });
</script>

</body>
</html>
