@extends('layouts.app')
@include('layouts.header')
@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn" >Çıkış</button>
    </form>
            <h2 classs="text-center mb-4">ÜRÜN EKLE </h2>
            @include('components.alert')  
            @include('components.product-form')
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
@endsection
