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
                <li>
                    <img src="{{ asset('storage/product_images/'.$product->product_images)}}" alt="{{$product->product_name }}" width="100">
                    <strong>{{ $product->product_name }}</strong><br>
                    Fiyat: {{ $product->product_price }}
</li>
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
