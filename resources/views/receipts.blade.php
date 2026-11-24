@extends('layouts.app')

@section('title', 'My Receipts')

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Receipts</li>
        </ol>
    </nav>
    <h1 class="page-title mb-2">My Receipts</h1>
    <p class="text-secondary-dark mb-4">Every receipt is saved here in your account. If your email is verified, a copy is also sent to {{ auth()->user()->email }}.</p>

    @if (session('status'))
        <div class="alert alert-light border" role="status">{{ session('status') }}</div>
    @endif

    @unless ($verified)
        <div class="border rounded p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background:#fff1e6;">
            <span class="small">Your email <strong>{{ auth()->user()->email }}</strong> is not verified, so receipts stay in your account only. Verify it to get them by email too.</span>
            <form method="post" action="{{ route('email.verify.send') }}">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm px-3">Verify my email</button>
            </form>
        </div>
    @endunless

    @forelse ($orders as $order)
        <section class="border rounded p-3 p-md-4 mb-3" aria-label="Receipt {{ $order->code }}">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="fs-5 fw-semibold mb-0">
                        {{ $order->code }}
                        @if (in_array($order->id, $newIds, true))<span class="badge text-bg-warning ms-1">New</span>@endif
                    </h2>
                    <span class="small text-secondary-dark">
                        Paid {{ ($order->paid_at ?? $order->updated_at)->format('M j, Y g:i A') }}
                        · {{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('artwork', $order->items->count()) }}
                        · ₱{{ number_format($order->total, 2) }}
                    </span>
                </div>
                <a href="{{ route('orders.receipt', $order) }}" class="btn btn-outline-dark btn-sm px-3">View receipt</a>
            </div>
            <div class="small mt-2">
                <span class="badge text-bg-light border">Saved in your account</span>
                @if ($order->receipt_emailed_at)
                    <span class="badge text-bg-success ms-1">Emailed to {{ auth()->user()->email }}</span>
                @else
                    <span class="badge text-bg-secondary ms-1">Not emailed</span>
                @endif
            </div>
        </section>
    @empty
        <p class="text-secondary-dark">No receipts yet. A receipt appears here once the administrator confirms a payment.</p>
    @endforelse

    {{ $orders->links() }}
</div>
@endsection
