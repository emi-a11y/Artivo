@extends('layouts.admin')
@section('title', 'Payment methods')
@section('heading', 'Payment methods')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Add a payment method</h2>
            <form action="{{ route('admin.methods.store') }}" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.partials.payment-method-fields', ['method' => null])
                <button type="submit" class="btn btn-primary">Add payment method</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr><th scope="col">QR code</th><th scope="col">Name</th><th scope="col">Account</th><th scope="col">Status</th><th scope="col" class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($methods as $m)
                            <tr>
                                <td><a href="{{ $m->qr_url }}" target="_blank" rel="noopener"><img src="{{ $m->qr_url }}" alt="{{ $m->name }} QR code" width="64" height="64" style="object-fit: contain;" class="border rounded bg-white"></a></td>
                                <td class="fw-semibold">{{ $m->name }}</td>
                                <td>{{ $m->account_name }}<small class="d-block text-muted">{{ $m->account_number_display }}</small></td>
                                <td><span class="admin-status admin-status-{{ $m->is_active ? 'ok' : 'bad' }}">{{ $m->is_active ? 'Active' : 'Hidden' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.methods.edit', $m) }}" class="btn btn-sm btn-outline-dark">Edit</a>
                                    <form action="{{ route('admin.methods.destroy', $m) }}" method="post" class="d-inline"
                                        onsubmit="return confirm('Delete {{ addslashes($m->name) }}? Buyers will no longer see its QR code.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-dark">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No payment methods yet. Add one above so buyers can pay.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
