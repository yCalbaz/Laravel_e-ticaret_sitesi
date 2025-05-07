<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body> 
    @include('layouts.panel_header')
    <br>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-body p-5"> 
            
                <h2 class="text-center mb-4">Depo Ekle</h2>


                <form id="storeForm" action="{{ route('store.store') }}" method="POST">
                    @csrf 
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Depo Adı</label>
                            <input type="text" name="store_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maksimum Kapasite</label>
                            <input type="number" name="store_max" class="form-control" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Öncelik </label>
                        <input type="number" name="store_priority" class="form-control" min="1" required>
                    </div>

                    <div id="ajaxAlert"></div>

                    <button type="submit" class="btn btn-primary w-100">Depo Ekle</button>
                </form>

            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $('#storeForm').on('submit', function(e) {
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
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: response.success,
                        icon: 'success',
                        confirmButtonText: 'Tamam',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then(() => {
                        
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                let errorMessages = '';
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.values(errors).forEach(function(messages) {
                        errorMessages += `${messages[0]}<br>`;
                    });
                } else if (xhr.status === 401) {
                    errorMessages = xhr.responseJSON.error || 'Bir hata oluştu.';
                }

                Swal.fire({
                    title: 'Hata!',
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
    </script>
</body>

</html>
