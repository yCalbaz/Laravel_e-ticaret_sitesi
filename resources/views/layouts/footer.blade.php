<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Başlığı</title>
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png" >
  
</head>
<body>
<footer class="custom-footer footer">
   <div><h4>@Flo</h4></div>
</footer>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
         let selectedSizeId = null;

$(document).on('click', '.size-button', function() {
    selectedSizeId = $(this).data('size-id');
    $('.size-button').removeClass('selected-size');
    $(this).addClass('selected-size');
});

function addCart(productSku) {
    if (!selectedSizeId) {
        Swal.fire({
            title: "Lütfen bir beden seçin!",
            icon: "warning",
            confirmButtonText: "Beden seç"
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm) {
                window.location.href = "/urun/" + productSku;
            } else {
                location.reload();
            }
        });
        return;
    }

    $.ajax({
        url: "{{ route('cart.add', ':sku') }}".replace(':sku', productSku),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            quantity: 1,
            size_id: selectedSizeId
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
                icon: "success",
                showCancelButton: true,
                confirmButtonText: "Alışverişe Devam et",
                cancelButtonText: "Sepete Git",
                reverseButtons: true
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = "/sepet";
                } else {
                    location.reload();
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

    /*iade butonu*/
    $(document).ready(function() {
    $('.iptalEtBtnClass').click(function() {
        var orderId = $(this).data('id');
        var storeId = $(this).data('store-id');

        $.ajax({
            url: '/orders/' + orderId + '/return/' + storeId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'İptal etmek istediğine emin misin?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Evet, iptal et!',
                        cancelButtonText: 'Hayır, vazgeç!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/order-canseled-form?orderId=' + orderId + '&storeId=' + storeId;
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            location.reload();
                        }
                    });
                } else if (response.error) {
                    Swal.fire({
                        title: 'Hata!',
                        text: response.error,
                        icon: 'error',
                        confirmButtonText: 'Tamam'
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Bir hata oluştu: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire({
                    title: 'Hata!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    });
});
    </script>
</html>