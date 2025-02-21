<style>
    .custom-navbar {
        background-color: white !important; /* Arka plan beyaz */
    }
    .navbar-brand {
        color: orange !important; /* FLO yazısı turuncu */
        font-weight: 1000;
        font-size: 55px;
    }
    .navbar-nav .nav-link {
        color: black !important; /* Giriş ve Sepet yazıları siyah */
        font-weight: bold;
        font-size: 25px;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light custom-navbar">
    <div class="container">
        <a class="navbar-brand" href="#">FLO</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.login.form') }}">Giriş</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Sepet</a></li>
            </ul>
        </div>
    </div>
</nav>