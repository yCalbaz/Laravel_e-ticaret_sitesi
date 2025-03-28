<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş</title>
    @vite(['resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    
</head>

<body class="bg-light">
    @include('layouts.header')
    <div class="container d-flex justify-content-center align-items-center" style="height: 60vh; ">
        <div class="card p-4" style="width: 350px; border-color: rgba(255, 104, 29, 0.25); ">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        
            <h2 class="text-center">Merhaba,</h2>
            <p class="text-center">Giriş yap veya Hesap Oluştur, fırsatları kaçırma!</p>
            <div class="d-flex justify-content-center mb-3">
                <a href="#" class="btn btn-outline-secondary me-2" style=" background-color: #ff671d; border-color: #ff671d; color:white;">Giriş Yap</a>
                <a href="{{ route('musteri.uye_ol') }}" class="btn btn-outline-secondary" style="border-color: #ff671d;">Üye Ol</a>
            </div>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">E-Posta Adresi</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="margin-bottom: 10px; background-color: #ff671d; border-color: #ff671d;"  >Giriş Yap</button>
            </form>
           </div>
    </div>
    @include('layouts.footer')
</body>
</html>