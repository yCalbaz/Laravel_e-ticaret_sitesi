<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Başlığı</title>
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png" >
    @vite(['resources/js/app.js' ,'resources/css/footer.css'])
</head>
<body>
    <footer class="container-fluid bg-grey py-5">
        <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 ">
                    <div class="logo-part">
                        
                    <img src="{{ asset('storage/images/flo-logo-Photoroom.png') }}" alt="" height="50">
                    <br> <br>
                        <p>Ayakkabıda Yeni Bir Soluk, Hayatına Renk Katan Seçenekler Sunar: Flo...</p>
                    </div>
                    </div>
                    <div class="col-md-6 px-4">
                    <h6> Hakkında</h6>
                    <a  class="btn-footer" href="{{ route('cart.index') }}"> Sepet </a><br>
                    <a  href="{{ route('orders.index') }}" class="btn-footer"> Siparişlerim</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 px-4">
                    <h6> Kategoriler</h6>
                    <div class="row ">
                        <div class="col-md-6">
                            <ul>
                                <li> <a href="{{ route('category.product', ['category_slug' => 'kadin']) }}"> Kadın</a> </li>
                                <li> <a href="{{ route('category.product', ['category_slug' => 'erkek']) }}"> Erkek</a> </li>
                                <li> <a href="{{ route('category.product', ['category_slug' => 'cocuk']) }}"> Çocuk</a> </li>
                            </ul>
                        </div>
                        <div class="col-md-6 px-4">
                            <ul>
                                <li> <a href="{{ route('category.product', ['category_slug' => 'ayakkabi']) }}"> Ayakkabı</a> </li>
                                <li> <a href="{{ route('category.product', ['category_slug' => 'giyim']) }}"> Giyim</a> </li>
                                <li> <a href="{{ route('category.product', ['category_slug' => 'aksesuar']) }}"> Aksesuar</a> </li>
                            </ul>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-6 ">
                    <h6> Markalar</h6>
                    
                    
                    <div class="row brand-row" style="gap: 60px;">
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                            <a href="{{ route('category.product', ['category_slug' => 'nike']) }}">
                                <img src="{{ asset('storage/images/nike.png') }}" style="width: 70px; " alt="nike">
                            </a>
                        </div> 
                        <div style="width: 30px;" class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                            <a href="{{ route('category.product', ['category_slug' => 'adidas']) }}">
                                <img src="{{ asset('storage/images/adidas.png') }}" style="width: 70px;" alt="adidas">
                            </a>
                        </div>
                        <div style="width: 30px;" class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                            <a href="{{ route('category.product', ['category_slug' => 'lumberjack']) }}">
                                <img src="{{ asset('storage/images/lumberjack.png') }}" style="width: 70px;" alt="lumberjack">
                            </a>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
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

    /* sepet onay mesajları*/
    $(document).ready(function () {
        $('#cartApprovl').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');
            let formData = form.serialize();

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'Tamam',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then(() => {
                            window.location.href = "{{ route('orders.index') }}" ;
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessages = '';
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(function (messages) {
                            errorMessages += `${messages[0]}<br>`;
                        });
                    } else {
                        errorMessages = xhr.responseJSON?.error || 'Bir hata oluştu.';
                    }

                    Swal.fire({
                        title: 'İade Başarısız!',
                        html: errorMessages,
                        icon: 'error',
                        confirmButtonText: 'Tamam',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                }
            });
        });
    });
    </script>
</html>