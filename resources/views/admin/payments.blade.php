@extends('layouts.admin')
@section('title', 'Payments')
@section('heading', 'Payments to review')

@section('content')
    @php $map = ['Paid' => 'ok', 'Under review' => 'wait', 'Awaiting payment' => 'wait', 'Payment rejected' => 'bad', 'Refunded' => 'bad']; @endphp
    <div class="card">
        <div class="card-body">
            @include('admin.partials.tabs', ['tabs' => $tabs, 'param' => 'state', 'label' => 'Payment state'])
            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Order</th><th scope="col">Buyer</th><th scope="col">Total</th><th scope="col">Method</th>
                            <th scope="col">Reference</th><th scope="col">Receipt</th><th scope="col">State</th><th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $o)
                            <tr>
                                <td><span class="admin-order-code">{{ $o->code }}</span><small class="d-block text-muted">{{ $o->created_at->format('M j, Y') }}</small></td>
                                <td>{{ $o->user->name }}</td>
                                <td class="admin-order-total">₱{{ number_format($o->total, 2) }}</td>
                                <td>{{ $o->paymentMethod->name ?? 'None' }}</td>
                                <td>{{ $o->payment_reference ?: 'None' }}</td>
                                <td>
                                    @if ($o->proof_url)
                                        <a href="{{ $o->proof_url }}" target="_blank" rel="noopener" title="Open the full screenshot">
                                            <img src="{{ $o->proof_url }}" alt="Receipt screenshot for {{ $o->code }}" width="56" height="56" style="object-fit: cover;" class="border rounded">
                                        </a>
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-status admin-status-{{ $map[$o->payment_state] ?? 'wait' }}">{{ $o->payment_state }}</span>
                                    @if ($o->payment_state === 'Payment rejected' && $o->payment_note)<small class="d-block text-muted">{{ $o->payment_note }}</small>@endif
                                </td>
                                <td class="text-end">
                                    @if ($o->payment_state === 'Under review')
                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            <form action="{{ route('admin.payments.confirm', $o->id) }}" method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Confirm payment</button>
                                            </form>
                                            <form action="{{ route('admin.payments.reject', $o->id) }}" method="post" class="admin-inline-form">
                                                @csrf
                                                <label for="note{{ $o->id }}" class="visually-hidden">Reason for rejecting</label>
                                                <input type="text" id="note{{ $o->id }}" name="payment_note" class="form-control form-control-sm" maxlength="200" placeholder="Reason (optional)">
                                                <button type="submit" class="btn btn-sm btn-outline-dark">Reject</button>
                                            </form>
                                        </div>
                                    @elseif ($o->status === 'Paid')
                                        <a href="{{ route('orders.receipt', $o) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-dark">Receipt</a>
                                    @else
                                        <span class="text-muted small">Waiting for buyer</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No payments here yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('partials.paginator', ['paginator' => $orders, 'navClass' => 'mt-3', 'class' => 'admin-pagination justify-content-end'])
        </div>
    </div>
@endsection
