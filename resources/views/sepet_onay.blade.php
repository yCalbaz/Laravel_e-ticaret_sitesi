<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1.0">
    <title>Sepet Onay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
</head>
<body> 

@include('layouts.header')   
<div class="container mt-5">

    <div class="row"> 
        @if(isset($cartItems) && (is_array($cartItems) ? count($cartItems) > 0 : $cartItems->count() > 0))
            <div class="col-md-8">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Resim</th>
                            <th>Ürün Adı</th>
                            <th>Fiyat</th>
                            <th>Adet</th>
                            <th>Toplam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                            <tr>
                                <td><img src="{{ $item->product_image }}" class="img-fluid" style="max-width: 100px;"></td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->product_price }} TL</td>
                                <td>{{ $item->product_piece }}</td>
                                <td>{{ $item->product_price * $item->product_piece }} TL</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Toplam:</strong></td>
                            <td><strong>{{ $totalPrice }} TL</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-md-4">
                <form action="{{ route('sepet.approvl') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="adSoyad" class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" id="adSoyad" name="adSoyad" required>
                    </div>
                    <div class="mb-3">
                        <label for="adres" class="form-label">Adres</label>
                        <textarea class="form-control" id="adres" name="adres" rows="3" required></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Siparişi Tamamla</button>
                    </div>
                </form>
            </div>
        @else
            <p class="text-center text-muted">Sepette ürün yok</p>
        @endif
    </div>
</div>

<style>
    .custom-footer {
        background-color: #ff671d; 
        color: white; 
        padding: 15px 0;
        text-align: center;
    }
</style>

<footer class="custom-footer">
    <p>&copy; 2025 Flo </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>