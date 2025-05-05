<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Ekle</title>
    @vite(['resources/css/seller_panel.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .product-info {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .product-image {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

@include('layouts.panel_header')
<br>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body p-4">
            <h2 class="text-center mb-4">Stok Ekle</h2>
            @include('components.alert')

            <div class="product-info text-center">
                @if ($product->product_image)
                    <img src="{{ asset($product->product_image) }}" alt="{{ $product->product_name }}" class="product-image">
                @else
                    <p>Görsel Yok</p>
                @endif
                <h3>{{ $product->product_name }}</h3>
                <p>Fiyat: {{ $product->product_price }} TL</p>
                <p>Detaylar: {{ $product->details }}</p>
            </div>

            <form action="{{ route('stock.store') }}" method="POST" id="storeForm">
                @csrf
                <input type="hidden" name="product_sku" value="{{ $product->product_sku }}">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="store_id" class="form-label">Depo Seçin</label>
                        <select name="store_id" id="store_id" class="form-control form-control-lg" required>
                            <option value="">Lütfen bir depo seçin</option>
                            @foreach ($memberStore as $store)
                                <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                            @endforeach
                        </select>
                        @error('store_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="sizeSelect" class="form-label">Ürün Bedeni</label>
                        <select name="size_ids[]" id="sizeSelect" class="form-control form-control-lg select2-multiple" multiple>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->size_name }}</option>
                            @endforeach
                        </select>
                        @error('size_ids')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>  

                <div id="sizeInputsContainer"></div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Stok Ekle</button>
                    <a href="{{ route('seller.products') }}" class="btn btn-secondary mt-2 w-100">Geri</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2();
        var sizeInputsContainer = $('#sizeInputsContainer');

        $('#sizeSelect').on('change', function() {
            var selectedSizes = $(this).select2('data');
            sizeInputsContainer.empty();

            if (selectedSizes.length > 0) {
                selectedSizes.forEach(function(size) {
                    var sizeId = size.id;
                    var sizeName = size.text;

                    var inputGroup = $('<div class="row size-input-group mb-3"></div>');
                    var labelCol = $('<div class="col-md-6"><label class="form-label">' + sizeName + ' Adet</label></div>');
                    var inputCol = $('<div class="col-md-6"><input type="number" name="sizes[' + sizeId + ']" class="form-control form-control-lg" min="1" required></div>');

                    inputGroup.append(labelCol).append(inputCol);
                    sizeInputsContainer.append(inputGroup);
                });
            }
        });

        $('#storeForm').on('submit', function(e) {
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
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Başarılı!',
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'Tamam',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then(() => {
                            window.location.href = "{{ route('seller.products') }}";
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessages = '';
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(function(messages) {
                            errorMessages += `${messages[0]}<br>`;
                        });
                    } else if (xhr.status === 401) {
                        errorMessages = xhr.responseJSON.error || 'Bir hata oluştu.';
                    }

                    Swal.fire({
                        title: 'Hata!',
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
</body>
</html>
