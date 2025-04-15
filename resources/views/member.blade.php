<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üyeler</title>
    @vite(['resources/css/style.css'])
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('storage/images/flo-logo-Photoroom.png') }}" type="image/png">
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
                                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
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
<style>
    h2{
        color: white;
    }
</style>
</body>
</html>