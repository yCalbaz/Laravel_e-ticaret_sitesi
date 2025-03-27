<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>
    @include('layouts.header')

    <div class="container-fluid mt-5">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner slay">
                <div class="carousel-item active">
                    <a href="{{ route('urun')}}"><img src="{{ asset('storage/images/slaydir3.png') }}" class="d-block w-100" alt="Slider 1"></a>
                </div>
                <div class="carousel-item">
                    <a href="{{ route('urun')}}"><img src="{{ asset('storage/images/slaydir2.png') }}" class="d-block w-100" alt="Slider 2"></a>
                </div>
                <div class="carousel-item">
                    <a href="{{ route('urun')}}"><img src="{{ asset('storage/images/slaydır0.png') }}" class="d-block w-100" alt="Slider 3"></a>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Geri</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">İleri</span>
            </a>
        </div>
    </div>

    <div class="container mt-5">
        

        <h2>Popüler Ürünler</h2>
<div class="container mt-5"> 
            <div class="row" id="product-list">
    
            @foreach($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
                    <div class="card shadow-sm custom-card">
                        <a href="{{ route('product.details', ['sku' => $product->product_sku]) }}">
                            <img src="{{ asset($product->product_image) }}" class="card-img-top custom-img">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->product_name }}</h5>
                            <p class="card-text font-weight-bold">{{ $product->product_price }} TL</p>
                            @csrf
                            <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                            <input type="hidden" name="product_price" value="{{ $product->product_price }}">
                            <input type="hidden" name="product_image" value="{{ $product->image }}">
                            <input type="hidden" name="product_piece" value="1">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addCart('{{ $product->product_sku }}')">Sepete Ekle</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <a href="{{ route('products.brand', ['brand' => 'adidas']) }}">
                <img src="{{ asset('storage/images/banner2.png') }}" class="img-fluid" alt="Banner">
            </a>
        </div>

        <div class="mt-5 brand-container">
            <div class="row brand-row">
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <a href="{{ route('products.brand', ['brand' => 'nike']) }}">
                        <img src="{{ asset('storage/images/nike.png') }}" class="img-fluid" alt="nike">
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <a href="{{ route('products.brand', ['brand' => 'adidas']) }}">
                        <img src="{{ asset('storage/images/adidas.png') }}" class="img-fluid" alt="adidas">
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <a href="{{ route('products.brand', ['brand' => 'lumberjack']) }}">
                        <img src="{{ asset('storage/images/lumberjack.png') }}" class="img-fluid" alt="lumberjack">
                    </a>
                </div>
            </div>
            <div class="mt-4">
                <a href="#">
                    <img src="{{ asset('storage/images/banner.png') }}" class="img-fluid" alt="Banner">
                </a>
            </div>
        </div>
        <br>
    </div> </div>

    @include('layouts.footer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                title: "Ürün Sepete Eklendi",
                text: "Alışverişe Devammı Etmek İstersin Sepete Gitmek Mi",
                icon: "success",
                showCancelButton: true,
                confirmButtonText: "Devam et",
                cancelButtonText: "Sepete Git",
                reverseButtons: true
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        
                        window.location.href = "/cart"; 
                    }
                });
                    updateCartCount(response.cartCount);
                },
                error: function (xhr) {
                    console.log("Hata oluştu! Durum kodu:", xhr.status);
                    console.log("Hata mesajı:", xhr.responseText);
                    Swal.fire({
                    title: "Hata oluştu! Daha Sonra Tekrar Deneyiniz ",
                    icon: "warning"
                    });
                    alert("Hata oluştu! " + xhr.responseText);
                }
            });
           

        
        }
    </script>
</body>
</html>