<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Ekle</title>
    @vite(['resources/js/app.js', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>

@include('layouts.panel_header')

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body p-5">
            <h2 class="text-center mb-4">Stok Ekle</h2>
            @include('components.alert')

            <form action="{{ route('stock.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_sku" class="form-label">Ürün Kodu</label>
                        <input type="text" name="product_sku" id="product_sku" class="form-control form-control-lg" required>
                        @error('product_sku')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="store_id" class="form-label">Depo ID</label>
                        <input type="number" name="store_id" id="store_id" class="form-control form-control-lg" min="1" required>
                        @error('store_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="product_piece" class="form-label">Ürün Adedi</label>
                    <input type="number" name="product_piece" id="product_piece" class="form-control form-control-lg" min="1" required>
                    @error('product_piece')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Stok Ekle</button>
            </form>
        </div>
    </div>
</div>

<style>
    .logout-form {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .logout-btn {
        background-color: red;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-control-lg {
        padding: 0.75rem 1rem;
        font-size: 1.1rem;
        border-radius: 0.3rem;
    }
</style>

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
    });
</script>
</body>
</html>