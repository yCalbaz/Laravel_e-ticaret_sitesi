@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center mb-4">Depo Ekle</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="'" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Depo Adı</label>
                    <input type="text" name="store_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Maksimum Kapasite</label>
                    <input type="number" name="store_max" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Öncelik (1, 2, 3 vb.)</label>
                    <input type="number" name="store_priority" class="form-control" min="1" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Depoyu Ekle</button>
            </form>
        </div>
    </div>
</div>
@endsection
