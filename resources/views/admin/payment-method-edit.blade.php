@extends('layouts.admin')
@section('title', 'Edit ' . $method->name)
@section('heading', 'Edit ' . $method->name)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.methods.update', $method) }}" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.partials.payment-method-fields', ['method' => $method])
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('admin.methods') }}" class="btn btn-outline-dark ms-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection
