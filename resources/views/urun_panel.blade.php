@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 classs="text-center mb-4">ÜRÜN EKLE </h2>
            @include('components.alert')  
            @include('components.product-form')
        </div>
    </div>
</div>
@endsection
