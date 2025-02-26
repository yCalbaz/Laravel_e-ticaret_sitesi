@extends('layouts.app')

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 350px;">
        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn" >Çıkış</button>
    </form>
            <h2 class="text-center">Üye Ol</h2>
            @include('components.alert')
            <form action="{{ route('admin.uye_ol') }}" method="POST">
                @csrf
                

                <div class="mb-3">
                    <label for="name" class="form-label">Ad Soyad</label>
                    <input type="name" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-Posta</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="margin-bottom: 10px;">Üye Ol</button>
                <button type="submit" class="btn btn-primary w-100" onclick="window.location='{{ route('login')}}'"> Giriş Yap</button>
    </form>
          
        </div>
    </div>
</body>
</html>
<style>
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
</style>




