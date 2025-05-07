<!DOCTYPE html>
<html lang="tr">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün İade Formu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    @vite(['resources/js/app.js' ,'resources/css/style.css'])
</head>
<body>
    @include('layouts.header')
    <div class="container mt-4">
        <h2>Ürün İade Formu</h2>

        <div class="mb-4">
            <h4>Sipariş Detayları</h4>
            <p>Sipariş Numarası: {{ $order->orderLines->first()->order_id }}</p>
            <p>Sipariş Tarihi: {{ $order->created_at }}</p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Ürün Adı</th>
                        <th></th>
                        <th>Fiyat</th>
                    </tr>
                </thead>
                <tbody>
                    @if($order->orderLines)
                        @foreach($order->orderLines as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>
                                                    <div class="col-md-6">
                                                        <img src="{{ $item->product->product_image }}"  class="order_image" alt="">
                                                    </div>
                                                </td>
                                <td>{{ $item->product->product_price }} TL</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <p>Toplam Tutar: {{ $order->totalPrice }} TL</p>
        </div>

        <form id="returnForm" action="{{ route('order.processReturn') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $orderId }}">
    <input type="hidden" name="store_id" value="{{ $storeId }}">
    @if($order->orderLines)
        @foreach($order->orderLines as $item)
            <input type="hidden" name="product_sku[]" value="{{ $item->product_sku }}">
            @endforeach
    @endif
    <div class="form-group">
        <label for="details">İade Nedeni:</label>
        <textarea name="details" id="details" class="form-control" rows="4" required></textarea>
    </div>
    <div class="form-group">
        <label for="return_address">İade Adresi:</label>
        <textarea name="return_address" id="return_address" class="form-control" rows="3" placeholder="İade adresinizi giriniz."></textarea>
    </div>
            <button type="submit" class="" style="background-color: #ff671d; border: #ff671d; color:white;">Siparişi İptal Et</button>
            
        </form>
    </div><br>
    @include('layouts.footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#returnForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let formData = form.serialize();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'İade Talebi Alındı!',
                                text: response.success,
                                icon: 'success',
                                confirmButtonText: 'Tamam',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            }).then(() => {
                                window.location.href = "{{ route('order.showDetails', ['orderId' => $order->id]) }}" ;
                            });
                        }
                    },
                    error: function (xhr) {
                        let errorMessages = '';
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach(function (messages) {
                                errorMessages += `${messages[0]}<br>`;
                            });
                        } else {
                            errorMessages = xhr.responseJSON?.error || 'Bir hata oluştu.';
                        }

                        Swal.fire({
                            title: 'İade Başarısız!',
                            html: errorMessages,
                            icon: 'error',
                            confirmButtonText: 'Tamam',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>