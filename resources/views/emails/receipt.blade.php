@php $first = \Illuminate\Support\Str::before($order->user->name, ' '); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Artivo receipt</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f3ec;">
    {{-- Email clients ignore CSS files, so every style is inline on purpose. --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f3ec;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border-radius:16px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
                <tr>
                    <td bgcolor="#FF6200" style="background-color:#FF6200;padding:28px 32px;">
                        <span style="font-size:26px;font-weight:800;letter-spacing:4px;color:#ffffff;">ARTIVO</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px 32px 8px;">
                        <h1 style="margin:0 0 10px;font-size:24px;line-height:1.25;color:#1a1a1a;">Thank you, {{ $first }}!</h1>
                        <p style="margin:0 0 22px;font-size:15px;line-height:1.6;color:#4d4d4d;">Your payment for order <strong>{{ $order->code }}</strong> was confirmed. Your artworks are ready to download from My Orders, and this email is your receipt.</p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;margin-bottom:6px;">
                            <tr>
                                <td style="padding:4px 0;color:#777;">Order date</td>
                                <td align="right" style="padding:4px 0;font-weight:bold;">{{ $order->created_at->format('F j, Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:4px 0;color:#777;">Payment confirmed</td>
                                <td align="right" style="padding:4px 0;font-weight:bold;">{{ ($order->paid_at ?? $order->updated_at)->format('F j, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:4px 0;color:#777;">Paid with</td>
                                <td align="right" style="padding:4px 0;font-weight:bold;">{{ $order->paymentMethod->name ?? 'QR payment' }}@if ($order->payment_reference), ref. {{ $order->payment_reference }}@endif</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 32px 0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;border-top:1px solid #eee;">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                        <strong>{{ $item->title }}</strong><br>
                                        <span style="font-size:12px;color:#777;">{{ $item->license }}</span>
                                    </td>
                                    <td align="right" valign="top" style="padding:12px 0;border-bottom:1px solid #eee;">₱{{ number_format($item->price, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td style="padding:10px 0 2px;color:#777;">Subtotal</td>
                                <td align="right" style="padding:10px 0 2px;color:#777;">₱{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0;color:#777;">Service fee</td>
                                <td align="right" style="padding:2px 0;color:#777;">₱{{ number_format($order->service_fee, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0;font-size:17px;font-weight:bold;">Total paid</td>
                                <td align="right" style="padding:10px 0;font-size:17px;font-weight:bold;color:#FF6200;">₱{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:14px 32px 8px;">
                        <a href="{{ route('orders') }}" style="display:inline-block;background-color:#FF6200;color:#ffffff;text-decoration:none;font-weight:bold;font-size:15px;padding:13px 28px;border-radius:10px;">Go to My Orders</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 32px 30px;">
                        <p style="margin:0;font-size:12px;line-height:1.6;color:#8a8a8a;text-align:center;">Thank you for supporting independent artists. Artivo is a school project prototype, so this is not an official tax receipt.</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
