@extends('layouts.app')

@section('title', 'Pay for ' . $order->code)

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders') }}">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pay {{ $order->code }}</li>
        </ol>
    </nav>
    <h1 class="page-title mb-4">Pay for {{ $order->code }}</h1>

    @if ($order->payment_state === 'Payment rejected' && $order->payment_note)
        <div class="alert alert-light border mb-4" role="alert">
            <strong>Your last payment was not accepted.</strong> {{ $order->payment_note }}
        </div>
    @endif

    <div class="row g-4 g-xl-5">
        {{-- Order summary --}}
        <aside class="col-lg-4 order-lg-2">
            <div class="order-summary">
                <h2 class="fs-5 fw-semibold mb-3">Order summary</h2>
                @foreach ($order->items as $item)
                    <div class="d-flex justify-content-between gap-3 mb-2">
                        <span>{{ $item->title }}<small class="d-block text-secondary-dark">{{ $item->license }}</small></span>
                        <span>₱{{ number_format($item->price, 2) }}</span>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between small text-secondary-dark mb-1"><span>Service fee</span><span>₱{{ number_format($order->service_fee, 2) }}</span></div>
                <div class="d-flex justify-content-between fw-bold"><span>Total to pay</span><span class="price">₱{{ number_format($order->total, 2) }}</span></div>
            </div>
        </aside>

        <div class="col-lg-8 order-lg-1">
            @if ($methods->isEmpty())
                <div class="alert alert-light border py-4 text-center" role="status">
                    Payment is not set up yet. Please contact the admin.
                </div>
            @else
                <form action="{{ route('orders.pay.submit', $order) }}" method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <h2 class="fs-5 fw-semibold mb-1">1. Scan the QR code and pay ₱{{ number_format($order->total, 2) }}</h2>
                    <p class="small text-secondary-dark mb-3">Open your payment app, scan the code, and send the exact total. Then choose the method you used.</p>

                    <div class="row g-3 mb-2">
                        @foreach ($methods as $m)
                            <div class="col-md-6">
                                <label class="border rounded p-3 h-100 d-block pay-method" for="method{{ $m->id }}">
                                    <span class="form-check mb-2 d-flex align-items-center gap-2">
                                        <input class="form-check-input mt-0 @error('payment_method_id') is-invalid @enderror" type="radio"
                                            name="payment_method_id" id="method{{ $m->id }}" value="{{ $m->id }}"
                                            @checked((int) old('payment_method_id', $methods->count() === 1 ? $m->id : 0) === $m->id)>
                                        <span class="fw-semibold">{{ $m->name }}</span>
                                    </span>
                                    <img src="{{ $m->qr_url }}" alt="{{ $m->name }} QR code" class="img-fluid d-block mx-auto mb-2" style="max-height: 260px;">
                                    @if ($m->account_name)<span class="d-block small"><span class="text-secondary-dark">Name:</span> {{ $m->account_name }}</span>@endif
                                    @if ($m->account_number)
                                        <span class="d-block small"><span class="text-secondary-dark">Number:</span> <span>{{ $m->account_number_display }}</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline" data-copy="{{ $m->account_number }}">Copy</button></span>
                                    @endif
                                    @if ($m->instructions)<span class="d-block small text-secondary-dark mt-1">{{ $m->instructions }}</span>@endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('payment_method_id')<div class="invalid-feedback d-block mb-3">{{ $message }}</div>@enderror

                    <h2 class="fs-5 fw-semibold mt-4 mb-3">2. Send your receipt</h2>

                    <div class="mb-3">
                        <label for="payment_reference" class="form-label fw-semibold">Reference number</label>
                        <input type="text" class="form-control @error('payment_reference') is-invalid @enderror" id="payment_reference"
                            name="payment_reference" value="{{ old('payment_reference') }}" inputmode="numeric" autocomplete="off"
                            placeholder="13-digit number, e.g. 1234567890123">
                        @error('payment_reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div class="form-text">Find it on your GCash receipt under "Ref No." It has 13 digits.</div>
                    </div>

                    <div class="mb-3">
                        <label for="proof" class="form-label fw-semibold">Screenshot of your payment receipt</label>
                        <input type="file" class="form-control @error('proof') is-invalid @enderror" id="proof" name="proof" accept="image/png,image/jpeg">
                        @error('proof')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div class="form-text">JPG or PNG, up to 4 MB. The reference number and the amount should be visible.</div>
                        <img id="proofPreview" alt="Preview of the selected receipt" class="img-fluid border rounded mt-2" style="max-height: 280px;" hidden>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2">Send receipt</button>
                    <a href="{{ route('orders') }}" class="btn btn-outline-dark ms-2">Pay later</a>
                    <p class="small text-secondary-dark mt-3 mb-0">The admin checks the receipt and confirms the payment. Your order shows as Paid once it is confirmed.</p>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    // Show the chosen screenshot before it is sent, and let the buyer copy the account number.
    (function () {
        // Reference number: digits only, at most 13 (spaces from a pasted "1234 567 890123" are dropped).
        var ref = document.getElementById('payment_reference');
        if (ref) {
            ref.addEventListener('input', function () { ref.value = ref.value.replace(/\D/g, '').slice(0, 13); });
        }
        var input = document.getElementById('proof');
        var preview = document.getElementById('proofPreview');
        if (input && preview) {
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (!file) { preview.hidden = true; return; }
                preview.src = URL.createObjectURL(file);
                preview.hidden = false;
            });
        }
        document.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var text = btn.dataset.copy;
                if (navigator.clipboard) { navigator.clipboard.writeText(text); }
                btn.textContent = 'Copied';
                setTimeout(function () { btn.textContent = 'Copy'; }, 1500);
            });
        });
    })();
</script>
@endsection
