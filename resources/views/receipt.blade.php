<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $order->code }} | Artivo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #f6f4f0; color: #1A1A1A; }
        .receipt { max-width: 720px; margin: 2rem auto; background: #fff; border: 1px solid #e3ded6; border-radius: 12px; padding: 2.25rem; }
        .receipt-brand { font-weight: 700; font-size: 1.75rem; letter-spacing: .04em; color: #1A1A1A; }
        .receipt-brand span { color: #FF6200; }
        .receipt-paid { border: 2px solid #FF6200; color: #FF6200; font-weight: 700; letter-spacing: .12em; padding: .25rem .9rem; border-radius: 6px; display: inline-block; }
        .receipt table td, .receipt table th { padding: .6rem 0; }
        .muted { color: #666; }
        .receipt-note { background: #fff1e6; color: #7a3300; border-radius: 10px; padding: .75rem 1rem; font-size: .9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="receipt">
            @if (session('status'))
                <div class="receipt-note mb-4" role="status">{{ session('status') }}</div>
            @endif
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <div class="receipt-brand">ART<span>I</span>VO</div>
                    <div class="small muted">Digital Art Marketplace</div>
                </div>
                <div class="text-end">
                    <div class="receipt-paid">PAID</div>
                    <div class="small muted mt-2">Receipt for {{ $order->code }}</div>
                </div>
            </div>

            <div class="row g-3 mb-4 small">
                <div class="col-sm-6">
                    <div class="muted">Billed to</div>
                    <div class="fw-semibold">{{ $order->user->name }}</div>
                    <div>{{ $order->user->email }}</div>
                </div>
                <div class="col-sm-6">
                    <div class="muted">Order date</div>
                    <div class="fw-semibold">{{ $order->created_at->format('F j, Y') }}</div>
                    <div class="muted mt-2">Payment confirmed</div>
                    <div class="fw-semibold">{{ ($order->paid_at ?? $order->updated_at)->format('F j, Y g:i A') }}</div>
                </div>
            </div>

            <table class="table mb-3 align-middle">
                <thead>
                    <tr class="small muted"><th scope="col">Artwork</th><th scope="col">License</th><th scope="col" class="text-end">Price</th></tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td class="small">{{ $item->license }}</td>
                            <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="ms-auto" style="max-width: 280px;">
                <div class="d-flex justify-content-between small muted"><span>Subtotal</span><span>₱{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between small muted"><span>Service fee</span><span>₱{{ number_format($order->service_fee, 2) }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold fs-5"><span>Total paid</span><span>₱{{ number_format($order->total, 2) }}</span></div>
            </div>

            <div class="small mt-4">
                <div class="muted">Paid with</div>
                <div class="fw-semibold">{{ $order->paymentMethod->name ?? 'QR payment' }}@if ($order->payment_reference), reference {{ $order->payment_reference }}@endif</div>
            </div>

            <p class="small muted mt-4 mb-0">Thank you for supporting independent artists. School project prototype, not an official tax receipt.</p>

            @php $verified = \App\Support\ReceiptMailer::isVerified($order->user); @endphp
            <div class="mt-4 d-flex flex-wrap gap-2">
                @if ($verified)
                    <form method="post" action="{{ route('orders.receipt.email', $order) }}">
                        @csrf
                        <button type="submit" class="btn btn-dark">{{ $order->receipt_emailed_at ? 'Send the email again' : 'Email me a copy' }}</button>
                    </form>
                @else
                    <form method="post" action="{{ route('email.verify.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-dark">Verify my email to receive receipts</button>
                    </form>
                @endif
                <a href="{{ route('orders') }}" class="btn btn-outline-dark">Back to My Orders</a>
            </div>
            <p class="small muted mt-3 mb-0">
                @if ($order->receipt_emailed_at)
                    A copy was emailed to {{ $order->user->email }} on {{ \Illuminate\Support\Carbon::parse($order->receipt_emailed_at)->format('F j, Y g:i A') }}.
                @elseif (! $verified)
                    {{ $order->user->email }} has not been verified yet, so receipts are not emailed to it.
                @endif
                This receipt always stays available here in your account under My Orders.
            </p>
        </div>
    </div>
</body>
</html>
