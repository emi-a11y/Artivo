<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\ReceiptMailer;
use Illuminate\Http\Request;

/** "Email me a copy" button on the receipt page. */
class ReceiptEmailController extends Controller
{
    public function send(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($order->user_id === $user->id || $user->isAdmin(), 404);
        abort_unless($order->status === 'Paid', 404);

        $order->load(['user']);
        $email = $order->user->email;

        if (ReceiptMailer::send($order)) {
            return back()->with('status', "A copy of the receipt was sent to {$email}.");
        }

        if (! ReceiptMailer::isVerified($order->user)) {
            return back()->with('status', "{$email} is not verified yet, so nothing was sent. Verify your email first. The receipt stays available here in your account.");
        }

        return back()->with('status', ReceiptMailer::isRealEmail($email)
            ? 'The email could not be sent right now. The receipt is still available here in your account.'
            : "The address {$email} does not look like a real email, so nothing was sent. The receipt stays available here in your account.");
    }
}
