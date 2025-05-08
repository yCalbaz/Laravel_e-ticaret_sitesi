<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayları</title>
    @vite(['resources/js/app.js',  'resources/css/seller.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    @include('layouts.panel_header')

    <div class="container mt-5">
        <h2>Gelen Siparişler</h2>
        <br>
        <div class="container">
            <form method="GET" action="{{ url()->current() }}" class="form-inline">
            <button type="submit" class="color">Filtrele</button>
                <select name="order_status" id="status_filter" class="form-control form-control-sm mr-2">
                    <option value="">Tümü</option>
                    <option value="sipariş alındı" {{ $orderStatusFilter == 'sipariş alındı' ? 'selected' : '' }}>Gelen Siparişler</option>
                    <option value="hazırlanıyor" {{ $orderStatusFilter == 'hazırlanıyor' ? 'selected' : '' }}>Hazırlanan Siparişler </option>
                    <option value="kargoya verildi" {{ $orderStatusFilter == 'kargoya verildi' ? 'selected' : '' }}>Kargoya Verilen Siparişler</option>
                    <option value="iptal talebi alındı" {{ $orderStatusFilter == 'iptal talebi alındı' ? 'selected' : '' }}>İptal Talebi Gelen</option>
                    <option value="iptal talebi onaylandı" {{ $orderStatusFilter == 'iptal talebi onaylandı' ? 'selected' : '' }}>İptal Talebi Onaylanan</option>
                </select>
            </form>
        </div>

        @foreach ($groupedOrders as $orderId => $storeOrders)
            <div class="card mb-3">
                <div class="card-header">
                    <span>Sipariş İD: {{ $orderId }}</span>
                </div>
                <div class="card-body">
                    @foreach ($storeOrders as $storeId => $orderLines)
                        <h5>
                            Depo: {{ App\Models\Store::find($storeId)->store_name ?? 'Bilinmeyen Depo' }}
                            @if ($orderLines->contains(function ($line) { return $line->order_status == 'sipariş alındı' || $line->order_status == 'iptal talebi alındı'; }))
                                <i class="fas fa-exclamation-triangle warning-icon" title="Yeni sipariş veya iptal talebi var!"></i>
                            @endif
                        </h5>
                        <div class="d-flex justify-content-end mb-2">
                            @if ($orderLines->contains(function ($line) { return $line->order_status == 'iptal talebi alındı'; }))
                                <form method="POST" action="{{ route('seller.orders.approve-cancellation') }}" class="form-inline approve-cancellation-form">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $orderId }}">
                                    <input type="hidden" name="store_id" value="{{ $storeId }}">
                                    <button type="submit" class="btn btn-warning btn-sm">Bu Depodaki İptal Taleplerini Onayla</button>
                                </form>
                            @elseif (!$orderLines->contains(function ($line) { return $line->order_status == 'iptal talebi onaylandı'; }))
                                <form method="POST" action="{{ route('seller.updateLineStatusForStore') }}" class="update-store-status-form form-inline">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $orderId }}">
                                    <input type="hidden" name="store_id" value="{{ $storeId }}">
                                    <select name="order_status" class="form-control form-control-sm mr-2">
                                        <option value="sipariş alındı" {{ $orderLines->first()->order_status == 'sipariş alındı' ? 'selected' : '' }}>Sipariş Alındı</option>
                                        <option value="hazırlanıyor" {{ $orderLines->first()->order_status == 'hazırlanıyor' ? 'selected' : '' }}>Hazırlanıyor</option>
                                        <option value="kargoya verildi" {{ $orderLines->first()->order_status == 'kargoya verildi' ? 'selected' : '' }}>Kargoya Verildi</option>
                                    </select>
                                    <button type="submit" class="approvl"> Güncelle</button>
                                </form>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ürün Resmi</th>
                                        <th>Ürün Adı</th>
                                        <th>Beden</th>
                                        <th>Adet</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderLines as $line)
                                        <tr>
                                            <td>
                                                @if($line->product)
                                                    <img src="{{ asset($line->product->product_image) }}" class="order_image img-fluid">
                                                @else
                                                    Ürün Bulunamadı.
                                                @endif
                                            </td>
                                            <td>{{ $line->product_name }}</td>
                                            <td>{{ $line->size->size_name }}</td>
                                            <td>{{ $line->quantity }}</td>
                                            <td>{{ $line->order_status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateStoreForms = document.querySelectorAll('.update-store-status-form');
        updateStoreForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const orderId = this.querySelector('input[name="order_id"]').value;
                const storeId = this.querySelector('input[name="store_id"]').value;
                const orderStatus = this.querySelector('select[name="order_status"]').value;
                const csrfToken = this.querySelector('input[name="_token"]').value;
                const url = this.getAttribute('action'); 

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ order_id: orderId, store_id: storeId, order_status: orderStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: data.error || 'Bir hata oluştu!',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'İstek sırasında bir hata oluştu: ' + error,
                    });
                });
            });
        });

        const approveCancellationForms = document.querySelectorAll('.approve-cancellation-form');
        approveCancellationForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const orderId = this.querySelector('input[name="order_id"]').value;
                const storeId = this.querySelector('input[name="store_id"]').value;
                const csrfToken = this.querySelector('input[name="_token"]').value;
                const url = this.getAttribute('action');
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: 'İptal taleplerini onaylamak istediğinize emin misiniz?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, onayla!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ order_id: orderId, store_id: storeId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Onaylandı!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload()
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: data.error || 'İptal talepleri onaylanırken bir hata oluştu!',
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'İstek sırasında bir hata oluştu: ' + error,
                            });
                        });
                    }
                });
            });
        });
    });
</script>
</html>