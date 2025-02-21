<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


     @include('layouts.header') 



    <div class="container mt-5">
        <h2 class="mt-4 text-orange">Ürünler</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="https://via.placeholder.com/300" class="card-img-top" alt="Ürün 1">
                    <div class="card-body">
                        <h5 class="card-title">Ürün 1</h5>
                        <p class="card-text">Açıklama burada.</p>
                        <p><strong>100 TL</strong></p>
                        <a href="#" class="btn btn-primary">Sepete Ekle</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="https://via.placeholder.com/300" class="card-img-top" alt="Ürün 2">
                    <div class="card-body">
                        <h5 class="card-title">Ürün 2</h5>
                        <p class="card-text">Açıklama burada.</p>
                        <p><strong>150 TL</strong></p>
                        <a href="#" class="btn btn-primary">Sepete Ekle</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="https://via.placeholder.com/300" class="card-img-top" alt="Ürün 3">
                    <div class="card-body">
                        <h5 class="card-title">Ürün 3</h5>
                        <p class="card-text">Açıklama burada.</p>
                        <p><strong>200 TL</strong></p>
                        <a href="#" class="btn btn-primary">Sepete Ekle</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        © 2025 Laravel Projesi - Tüm Hakları Saklıdır.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
