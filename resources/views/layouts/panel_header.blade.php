<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Başlığı</title>
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>

<style>
    .custom-navbar {
        background-color: white !important; 
    }
    .navbar-brand {
        color: #ff671d !important; 
        font-weight: 1000;
        font-size: 55px;
    }
    .navbar-nav .nav-link {
        color: black !important; 
        font-weight: bold;
        font-size: 25px;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light custom-navbar">
    <div class="container">
    <a class="navbar-brand" >
            <img src="{{ asset('storage/images/flo-logo-Photoroom.png') }}" alt="" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
    </div>
</nav>

</body>
</html>