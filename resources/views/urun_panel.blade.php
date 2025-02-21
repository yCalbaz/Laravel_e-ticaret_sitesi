@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center mb-4">Ürün Ekle</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Ürün Adı</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ürün Kodu (SKU)</label>
                    <input type="text" name="product_sku" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ürün Fiyatı</label>
                    <input type="number" name="product_price" class="form-control" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ürün Resmi</label>
                    <input type="file" name="product_image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Ürünü Ekle</button>
            </form>
        </div>
    </div>
</div>
@endsection
