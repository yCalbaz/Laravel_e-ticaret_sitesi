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
        <h2 class="text-center mb-4">Öne Çıkan Ürünler</h2>
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img src="{{ $product->product_image }}" class="card-img-top" alt="Ürün Resmi">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->product_name }}</h5>
                            <p class="card-text">Fiyat: {{ $product->product_price }} TL</p>
                            <p class="card-text">Stok: {{ $product->product_stock }}</p>
                            <a href="#" class="btn btn-primary">Detay</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


       
    

    <style>
    .custom-footer {
        background-color: #ff671d; 
        color: white; 
        padding: 15px 0;
        text-align: center;
    }
</style>

<footer class="custom-footer">
   
</footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
