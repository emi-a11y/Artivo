Thank you, {{ \Illuminate\Support\Str::before($order->user->name, ' ') }}!

Your payment for order {{ $order->code }} was confirmed. Your artworks are ready to download from My Orders.

Order date: {{ $order->created_at->format('F j, Y') }}
Payment confirmed: {{ ($order->paid_at ?? $order->updated_at)->format('F j, Y g:i A') }}
Paid with: {{ $order->paymentMethod->name ?? 'QR payment' }}@if ($order->payment_reference), reference {{ $order->payment_reference }}@endif


@foreach ($order->items as $item)
- {{ $item->title }} ({{ $item->license }}): ₱{{ number_format($item->price, 2) }}
@endforeach

Subtotal: ₱{{ number_format($order->subtotal, 2) }}
Service fee: ₱{{ number_format($order->service_fee, 2) }}
Total paid: ₱{{ number_format($order->total, 2) }}

My Orders: {{ route('orders') }}

Thank you for supporting independent artists. Artivo is a school project prototype, so this is not an official tax receipt.
