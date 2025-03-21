<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kategori }} </title>
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body>
@include('layouts.header')   
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-3">
            <h5>Kategori</h5>
            <ul class="list-group category-list-scrollable">
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="ayakkabı" id="ayakkabı">
                    <label for="ayakkabı">Ayakkabı</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="günlük-ayakkabı" id="günlük-ayakkabı">
                    <label for="günlük-ayakkabı">Günlük Ayakkabısı</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="spor-ayakkabı" id="spor-ayakkabı">
                    <label for="spor-ayakkabı">Spor Ayakkabı</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="bot" id="bot">
                    <label for="bot">Bot</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="giyim" id="giyim">
                    <label for="giyim">Giyim</label>
                </li>
                
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="tişört" id="tişört">
                    <label for="tişört">Tişört</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="eşofman" id="eşofman">
                    <label for="eşofman">Eşofman</label>
                </li>
                
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="adidas" id="adidas">
                    <label for="adidas">Adidas</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="basic-relax" id="basic-relax">
                    <label for="basic-relax">Basic Relax</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="nike" id="nike">
                    <label for="nike">Nike</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="lumberjack" id="lumberjack">
                    <label for="lumberjack">Lumberjack</label>
                </li>
                
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="kadin" id="kadin">
                    <label for="kadin">Kadın</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="kadın-ayakkabı" id="kadın-ayakkabı">
                    <label for="kadın-ayakkabı">Kadın Ayakkkabı</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="kadın-çanta" id="kadın-çanta">
                    <label for="kadın-çanta">Kadın Çanta</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="kadın-giyim" id="kadın-giyim">
                    <label for="kadın-giyim">Kadın Giyim</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="erkek" id="erkek">
                    <label for="erkek">Erkek</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="erkek-ayakkabı" id="erkek-ayakkabı">
                    <label for="erkek-ayakkabı">Erkek Ayakkkabı</label>
                </li>
                
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="erkek-giyim" id="erkek-giyim">
                    <label for="erkek-giyim">Erkek Giyim</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="çocuk" id="çocuk">
                    <label for="çocuk">Çocuk</label>
                </li>
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="çocuk-ayakkabı" id="çocuk-ayakkabı">
                    <label for="çocuk-ayakkabı">Çocuk Ayakkkabı</label>
                </li>
                
                <li class="list-group-item">
                    <input type="checkbox" class="category-filter" value="çocuk-giyim" id="çocuk-giyim">
                    <label for="çocuk-giyim">Çocuk Giyim</label>
                </li>
            </ul>
        </div>
        <div class="col-lg-9">
            <div class="row" id="product-list">
                @foreach($urunler as $urun)
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex justify-content-center">
                        <div class="card shadow-sm custom-card">
                            <a href="{{ route('product.details', ['sku' => $urun->product_sku]) }}">
                                <img src="{{ asset($urun->product_image) }}" class="card-img-top custom-img">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $urun->product_name }}</h5>
                                <p class="card-text">{{ $urun->product_price }} TL</p>
                                <button type="submit" class="btn btn-primary btn-sm" onclick="addCart('{{ $urun->product_sku }}')">Sepete Ekle</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

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
                alert("Ürün sepete eklendi!");
            },
            error: function (xhr) {
                console.log("Hata oluştu! Durum kodu:", xhr.status);
                console.log("Hata mesajı:", xhr.responseText);
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
</script>
</body>
</html>