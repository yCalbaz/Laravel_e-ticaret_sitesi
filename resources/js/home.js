
import $ from 'jquery';

$( function addCart(productSku) {
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
})
    