<!DOCTYPE >
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 350px;">
            <h2 class="text-center">Müşteri Girişi</h2>
            @include('components.alert')
            <form action="{{ route('musteri_giris.post') }}" method="POST">
                @csrf
                <input type="hidden" name="authority" value="customer">
                <div class="mb-3">
                    <label for="email" class="form-label">E-Posta</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Giriş Yap</button><br>
                <button type="submit" class="btn btn-primary w-100"><a href="{{ route('musteri.uye_ol') }}" class="btn btn-primary w-100">Üye ol</a></button>
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