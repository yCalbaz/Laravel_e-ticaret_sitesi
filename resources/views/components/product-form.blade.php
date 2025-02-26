<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
     @csrf
     <div class="mb-3">
        <label class="form-label">Ürün adı </label>
        <input type="text" name="product_name" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label"> Ürün kodu </label>
    <input type="text" name="product_sku" class="form-control" required >
</div>

<div class="mb-3">
    <label class="form-label"> Ürün fiyatı </label>
    <input type="number" name="product_price" class="form-control" step="0.01" required >
</div>

<div class="mb-3">
    <label class="form-label"> Ürün resmi </label>
    <input type="file" name="product_image" class="form-control" accept="image/*" required>
</div>

<button type="submit" class="btn btn-primary w-100"> ÜRÜNÜ EKLE </button>
</form>