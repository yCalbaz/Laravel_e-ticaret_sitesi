<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İade Formu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Ürün İade Formu</h2>

        <form action="{{ route('order.processReturn') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $orderId }}">
            <input type="hidden" name="product_sku" value="{{ $productSku }}">
            <input type="hidden" name="product_price" value="{{ $productPrice }}">

            <div class="mb-3">
                <label for="details" class="form-label">İade Nedeni:</label>
                <textarea name="details" id="details" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">İade Talebi Gönder</button>
        </form>
    </div>
</body>
</html>