import $ from 'jquery';

$(function cartDelete(productId){
$.ajax({
    url:"{{ route('cart.delete', ':id') }}".replace(':id', productId),
    type:"DELETE",
    data:{
        _token: "{{ csrf_token() }}",
    },
    success: function(response){
        alert("Ürün silindi");
        location.reload();
    },
    error: function(xhr ){
        console.log(xhr);
        alert("hata oluştu" + xhr.responseText);
    }

});
});
