<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satıcı Üye Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 350px;">
            <h2 class="text-center">Satıcı Üye Ol</h2>
            @include('components.alert')
            <form action="{{ route('satici.uye_ol.kayit') }}" method="POST">
                @csrf
                <input type="hidden" name="authority" value="seller">
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
                <button type="submit" class="btn btn-primary w-100">Üye Ol</button>
            </form>
        </div>
    </div>
</body>
</html>