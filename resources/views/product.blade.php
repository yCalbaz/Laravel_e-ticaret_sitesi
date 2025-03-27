<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler</title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">

</head>
<body>
@include('layouts.header')   
<div class="container mt-5">
    <div class="row">
   
    

</div>
        
        
            <div class="row" id="product-list">
            @foreach($urunler as $urun)
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 d-flex justify-content-center">
                        <div class="card shadow-sm custom-card">
                            <a href="{{ route('product.details', ['sku' => $urun->product_sku]) }}">
                                <img src="{{ asset($urun->product_image) }}" class="card-img-top custom-img">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $urun->product_name }}</h5>
                                <p class="card-text">{{ $urun->product_price }} TL</p>
                                <button type="submit" class=" cart-add-btn" onclick="addCart('{{ $urun->product_sku }}')">Sepete Ekle</button>
                            </div>
                        </div>
                    </div>
                @endforeach
                
        </div>
    </div>
</div>
<footer class="custom-footer">
        <p>&copy; 2025 flo - Tüm Hakları Saklıdır.</p>
    </footer>
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
                    if  (result.dismiss === Swal.DismissReason.cancel) {
                        
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
                }
            });
    }

    $(document).ready(function() {
    $('.category-filter').change(function() {
        var selectedCategories = $('.category-filter:checked').map(function() {
            return this.value;
        }).get();

        $.ajax({
            url: "{{ route('get.products.by.category') }}",
            type: "GET",
            data: {
                categories: selectedCategories
            },
                success: function(response) {
                    var productList = $('#product-list');
                    productList.empty();

                    if (response.length > 0) {
                        $.each(response, function(index, urun) {
                            var productHtml = `
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex justify-content-center">
                                    <div class="card shadow-sm custom-card">
                                        <a href="{{ route('product.details', ['sku' => '` + urun.product_sku + `']) }}">
                                            <img src="{{ asset('` + urun.product_image + `') }}" class="card-img-top custom-img">
                                        </a>
                                        <div class="card-body">
                                            <h5 class="card-title">` + urun.product_name + `</h5>
                                            <p class="card-text">` + urun.product_price + ` TL</p>
                                            <button type="submit" class="btn btn-primary btn-sm" onclick="addCart('` + urun.product_sku + `')">Sepete Ekle</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            productList.append(productHtml);
                        });
                    } else {
                        productList.html('<div class="col-12 text-center">Ürün bulunamadı.</div>');
                    }
                },
                error: function(xhr) {
                    console.log("Hata oluştu! Durum kodu:", xhr.status);
                    console.log("Hata mesajı:", xhr.responseText);
                    alert("Hata oluştu! " + xhr.responseText);
                }
            });
        });
    });
</script>
</body>
</html>
