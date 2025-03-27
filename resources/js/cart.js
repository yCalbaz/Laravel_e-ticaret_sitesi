export function cartDelete(productId, csrfToken, cartDeleteRoute) {
    $.ajax({
        url: cartDeleteRoute.replace(':id', productId),
        type: "DELETE",
        data: {
            _token: csrfToken,
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            console.log(xhr);
            alert("Hata oluştu: " + xhr.responseText);
        }
    });
}

export function updateCart(productId, adet, csrfToken, cartUpdateRoute) {
    $.ajax({
        url: cartUpdateRoute.replace(':id', productId),
        type: "PUT",
        data: {
            _token: csrfToken,
            adet: adet,
        },
        success: function(response) {
            $('#total-price').text(response.totalPrice);
        },
        error: function(xhr) {
            console.log(xhr);
            alert("Hata oluştu: " + xhr.responseText);
        }
    });
}

export function addCart(productSku, csrfToken, cartAddRoute, updateCartCountCallback) {
    $.ajax({
        url: cartAddRoute.replace(':sku', productSku),
        type: "POST",
        data: {
            _token: csrfToken,
            quantity: 1
        },
        success: function (response) {
            console.log("Başarıyla eklendi:", response);
            alert("Ürün sepete eklendi!");
            if (updateCartCountCallback) {
                updateCartCountCallback(response.cartCount);
            }
        },
        error: function (xhr) {
            console.log("Hata oluştu! Durum kodu:", xhr.status);
            console.log("Hata mesajı:", xhr.responseText);
            alert("Hata oluştu! " + xhr.responseText);
        }
    });
}