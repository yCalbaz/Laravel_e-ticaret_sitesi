<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün</title>
    @vite(['resources/css/seller_panel.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>

    @include('layouts.panel_header')
    <br>
    <div class="container mt-3">
        <div class="card shadow-lg">
            <div class="card-body p-4"> <h2 class="text-center mb-4">ÜRÜN EKLE</h2>
                

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_name" class="form-label">Ürün Adı</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" required> @error('product_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="product_sku" class="form-label">Ürün Kodu</label>
                        <input type="text" name="product_sku" id="product_sku" class="form-control" required> @error('product_sku')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_price" class="form-label">Ürün Fiyatı</label>
                        <input type="number" name="product_price" id="product_price" class="form-control" step="0.01" required> @error('product_price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="product_image" class="form-label">Ürün Resmi</label>
                        <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*" required> @error('product_image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="details" class="form-label">Ürün Detayı</label>
                        <input type="text" name="details" id="details" class="form-control" required> @error('product_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="categorySelect" class="form-label">Kategori</label>
                        <select name="category_ids[]" id="categorySelect" class="form-control select2-multiple" multiple required> @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                        <div id="selectedCategories" class="mt-2"></div>
                        @error('category_ids')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div> </div>

                    <button type="submit" class="btn btn-primary w-100">ÜRÜNÜ EKLE</button> </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2();

            $('#categorySelect').on('change', function() {
                var selectedOptions = $(this).select2('data');
                var selectedCategoriesDiv = $('#selectedCategories');
                selectedCategoriesDiv.empty();

                selectedOptions.forEach(function(option) {
                    var categoryName = option.text;
                    var categoryBadge = $('<span class="badge bg-primary me-1"></span>').text(categoryName);
                    selectedCategoriesDiv.append(categoryBadge);
                });
            });
            $('#productForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let formData = new FormData(form[0]); 

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    processData: false, 
                    contentType: false, 
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
                        } else {
                            
                            errorMessages = xhr.responseJSON.message || 'Bir hata oluştu. Lütfen konsolu kontrol edin.';
                            console.error('AJAX Hatası:', xhr); 
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