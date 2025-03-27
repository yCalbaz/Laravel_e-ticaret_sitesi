<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama Sonuçları</title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    @include('layouts.header')

    <div class="container mt-5">
        <h2>Arama Sonuçları: "{{ $query }}"</h2>

        @if($products->count() > 0)
            <div class="row">
                @foreach($products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 d-flex justify-content-center">
                        <div class="card shadow-sm custom-card">
                        <a href="{{ route('product.details', ['sku' => $product->product_sku]) }}"><img src="{{ asset($product->product_image) }}" class="card-img-top custom-img"></a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->product_name }}</h5>
                                <p class="card-text font-weight-bold">{{ $product->product_price }} TL</p>
                                <button type="submit" class="btn btn-primary btn-sm" onclick="addCart( '{{$product->product_sku }}')">Sepete Ekle</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>Aradığınız kriterlere eşleşen ürün yok.</p>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
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

</script>