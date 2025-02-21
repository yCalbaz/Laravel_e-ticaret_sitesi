<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sepet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center mb-4">Sepet</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Fiyat</th>
                        <th>Adet</th>
                        <th>Toplam</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ürün 1</td>
                        <td>100 TL</td>
                        <td>2</td>
                        <td>200 TL</td>
                        <td><button class="btn btn-danger btn-sm">Sil</button></td>
                    </tr>
                   

            <div class="d-flex justify-content-between mt-4">
                <h4>Toplam: <span class="fw-bold">675 TL</span></h4>
                <button class="btn btn-success">Satın Al</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
