<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
  
</head>
<body>
 

@include('layouts.header')   

<div class="container-fluid mt-5">
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <a href=""><img src="{{ asset('storage/images/slaydir.png') }}" class="d-block w-100" alt="Slider 1"></a>
            </div>
            <div class="carousel-item">
                <a href=""><img src="{{ asset('storage/images/slaydir2.png') }}" class="d-block w-100" alt="Slider 2"></a>
            </div>
            <div class="carousel-item">
                <a href=""><img src="{{ asset('storage/images/slaydir3.png') }}" class="d-block w-100" alt="Slider 3"></a>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    </div>
    <div class="container mt-5">
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

    </form>
@if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div> 
            @endif

      
    <div class="row"> 
        <H2>Popüler Ürünler</H2>
        @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
                <div class="card shadow-sm custom-card">
                <a href="{{ route('product.details', ['sku' => $product->product_sku]) }}"><img src="{{ asset($product->product_image) }}" class="card-img-top custom-img"></a>
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->product_name }}</h5>
                        <p class="card-text font-weight-bold">{{ $product->product_price }} TL</p>
                       
                            @csrf
                            <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                            <input type="hidden" name="product_price" value="{{ $product->product_price }}">
                            <input type="hidden" name="product_image" value="{{ $product->image }}">
                            <input type="hidden" name="product_piece" value="1">
                            
                            <button type="submit" class="btn btn-primary btn-sm" onclick="addCart( '{{$product->product_sku }}')">Sepete Ekle</button>
                            
                                
                    </div>
                </div>
            </div>
        @endforeach
    </div> 
    <div class="mt-4">
        <a href=""><img src="{{ asset('storage/images/banner.png') }}" class="img-fluid" alt="Banner"></a>
    </div>
<br>
</div>

<footer class="custom-footer">
   
</footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function addCart(productSku) {
    $.ajax({
        url: "{{ route('cart.add', ':sku') }}".replace(':sku', productSku), 
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            quantity: 1
        },
        success: function (response) {
            console.log("Başarıyla eklendi:", response);
            alert("Ürün sepete eklendi!");
        },
        error: function (xhr) {
            console.log("Hata oluştu! Durum kodu:", xhr.status);
            console.log("Hata mesajı:", xhr.responseText);
            alert("Hata oluştu! " + xhr.responseText);
        }
    });
}

</script>