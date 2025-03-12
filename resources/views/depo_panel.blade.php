<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body> 

@include('layouts.panel_header')

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn" >Çıkış</button>
    </form>
            <h2 class="text-center mb-4">Depo Ekle</h2>

            @include('components.alert')

            <form action="{{ route('store.store') }}" method="POST">
                @csrf 
                <div class="mb-3">
                    <label class="form-label">Depo Adı</label>
                    <input type="text" name="store_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Maksimum Kapasite</label>
                    <input type="number" name="store_max" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Öncelik </label>
                    <input type="number" name="store_priority" class="form-control" min="1" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Depo Ekle</button>
            </form>
        </div>
    </div>
</div>

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
</body>
</html>
