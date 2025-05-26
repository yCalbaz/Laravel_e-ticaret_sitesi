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
    <nav class="category-menu" style="padding-top: 110px;">
    </nav>

    <nav class="navbar navbar-expand-lg navbar-light custom-header fix ">
        <div class="container mt-5 ">
            <div class="container d-flex align-items-center">
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('storage/images/flo-logo-Photoroom.png') }}" alt="" height="50">
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <form class="search-form" id="searchForm" action="{{ route('search') }}" method="GET">
                        <input class="search-input" type="search" name="query" placeholder="Ara" aria-label="Ara">
                    </form>
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.logout') }}"> Çıkış </a></li>
                        @endauth
                        @guest
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}"> Giriş </a></li>
                        @endguest
                        <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">
                                Sepetim
                                @if ($sepetSayisi > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style=" color: red; font-size:13px; ">
                                        {{ $sepetSayisi }}
                                    </span>
                                    @endif
                            </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">Siparişlerim</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <nav class="category-menu  ">
        <div class="container">
        <div class="filter-container">
        <div class="filter-dropdown"> 
            <button class="filter-button">Kadın <span class="arrow">▼</span></button>
            <div class="filter-options ">
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'kadinAyakkabi']) }}">Ayakkkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'kadin-giyim']) }}">Giyim</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'gunlukAyakkabi']) }}">Günlük Ayakkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'sporAyakkabi']) }}">Spor Ayakkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'kadin-bot']) }}">Bot</a></li>
            </div>
        </div>
        <div class="filter-dropdown">
            <button class="filter-button">Erkek <span class="arrow">▼</span></button>
            <div class="filter-options">
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'erkekAyakkabi']) }}">Ayakkkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'erkek-giyim']) }}">Giyim</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'gunlukAyakkabi']) }}">Günlük Ayakkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'sporAyakkabi']) }}">Spor Ayakkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'erkek-bot']) }}">Bot</a></li>
            </div>
        </div>
        <div class="filter-dropdown">
            <button class="filter-button">Çocuk <span class="arrow">▼</span></button>
            <div class="filter-options ">
                <li> <a class="category" href="{{ route('category.product', ['category_slug' => 'cocukAyakkabi']) }}">Ayakkkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'cocuk-giyim']) }}">Giyim</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'gunlukAyakkabi']) }}">Günlük Ayakkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'sporAyakkabi']) }}">Spor Ayakkabı</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'cocuk-bot']) }}">Bot</a></li>
            </div>
        </div>
        <div class="filter-dropdown">
            <button class="filter-button">Aksesuar <span class="arrow">▼</span></button>
            <div class="filter-options ">
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'cocuk-canta']) }}">Çocuk Çanta</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'erkek-canta']) }}">Erkek Çanta</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'kadın-canta']) }}">Kadın Çanta</a></li>
                <li><a class="category" href="{{ route('category.product', ['category_slug' => 'sapka']) }}">Şapka</a></li>
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
    </nav><br>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            if (!this.querySelector('input[name="query"]').value) {
                event.preventDefault();
            }
        });

        function updateCartCount(count) {
            let cartCountElement = $('#cart-count');
            if (cartCountElement.length) {
                cartCountElement.text(count);
            } else {
                console.error("Sepet sayacı elementi bulunamadı!");
            }
        }

    </script>
</body>
</html>
