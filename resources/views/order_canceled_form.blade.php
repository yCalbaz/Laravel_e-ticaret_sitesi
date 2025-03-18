<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İade Formu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Ürün İade Formu</h2>
        
        <form action="{{ route('order.processReturn') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ request('order_id') }}">
            <input type="hidden" name="product_sku" value="{{ request('product_sku') }}">
            
            <div class="mb-3">
                <label for="details" class="form-label">İade Nedeni:</label>
                <textarea name="details" id="details" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">İade Talebi Gönder</button>
        </form>
    </div>
</body>
</html>
