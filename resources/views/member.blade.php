<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üyeler</title>
    @vite(['resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        h2{
            color: white;
        }
    </style>
</head>
<body>
    @include('layouts.panel_header')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                
                <h2 class="text-center mb-4">KULLANICILAR</h2>
                @include('components.alert')  
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ad</th>
                                <th>E-posta</th>
                                <th class="d-none d-md-table-cell">Oluşturulma Tarihi</th>
                                <th class="d-none d-md-table-cell">Güncelleme Tarihi</th>
                                <th class="d-none d-lg-table-cell">Müşteri ID</th>
                                <th >Yetki ID</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td class="d-none d-md-table-cell">{{ $member->created_at }}</td>
                                    <td class="d-none d-md-table-cell">{{ $member->updated_at }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $member->customer_id }}</td>
                                    <td >{{ $member->authority_id }}</td>
                                    <td>
                                        <form action="{{ route('members.delete', $member->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm uyeDelete" data-id="{{ $member->id }}">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
    $('.uyeDelete').click(function() {
        var memberId = $(this).data('id');

        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu kullanıcıyı silmek istediğinize emin misiniz?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'Hayır, vazgeç!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('members.delete', ':id') }}".replace(':id', memberId),
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Silindi!',
                                response.success,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else if (response.error) {
                            Swal.fire(
                                'Hata!',
                                response.error,
                                'error'
                            );
                        }
                    },
                    error: function(error) {
                        console.error("Silme hatası:", error);
                        Swal.fire({
                            title: "Hata!",
                            text: "Kullanıcı silinirken bir hata oluştu.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    });
});
    </script>
</body>
</html>