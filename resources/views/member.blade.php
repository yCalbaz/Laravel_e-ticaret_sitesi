<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üyeler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('layouts.header')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn" >Çıkış</button>
            </form>
            <h2 classs="text-center mb-4"> KULLANICILAR </h2>
            @include('components.alert')  
            <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>E-posta</th>
                    <th>Oluşturulma Tarihi</th>
                    <th>Güncelleme Tarihi</th>
                    <th>Müşteri ID</th>
                    <th>Yetki ID</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr>
                        <td>{{ $member->id }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->created_at }}</td>
                        <td>{{ $member->updated_at }}</td>
                        <td>{{ $member->customer_id }}</td>
                        <td>{{ $member->authority_id }}</td>
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
    
<style>
    .logout-form {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .logout-btn {
        background-color: red;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
</style>
</body>
</html>