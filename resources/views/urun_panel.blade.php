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
            <h2 classs="text-center mb-4">ÜRÜN EKLE </h2>
            @include('components.alert')  
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Ürün adı </label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"> Ürün kodu </label>
                    <input type="text" name="product_sku" class="form-control" required >
                </div>

                <div class="mb-3">
                    <label class="form-label"> Ürün fiyatı </label>
                    <input type="number" name="product_price" class="form-control" step="0.01" required >
                </div>

                <div class="mb-3">
                    <label class="form-label"> Ürün resmi </label>
                    <input type="file" name="product_image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary w-100"> ÜRÜNÜ EKLE </button>
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