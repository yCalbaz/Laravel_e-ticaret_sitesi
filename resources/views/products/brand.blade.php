<!DOCTYPE html>
<html lang="tr">
<head>
    <title>{{ $category->category_name }} Ürünleri</title>
</head>
<body>
    <h1>{{ $category->category_name }} Ürünleri</h1>

    @if($products->count() > 0)
        <ul>
            @foreach($products as $product)
                <li>{{ $product->product_name }} - {{ $product->product_price }} TL</li>
            @endforeach
        </ul>
    @else
        <p>Bu markaya ait ürün bulunamadı.</p>
    @endif
</body>
</html>