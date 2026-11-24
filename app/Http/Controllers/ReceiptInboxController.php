<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\ReceiptMailer;
use Illuminate\Http\Request;

/** "My Receipts": every receipt is kept in the buyer's account, and shows whether it was also emailed. */
class ReceiptInboxController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $newIds = $user->unreadNotifications
            ->map(fn ($n) => $n->data['order_id'] ?? null)->filter()->all();

        $orders = Order::with('items')
            ->where('user_id', $user->id)->where('status', 'Paid')
            ->latest('paid_at')->latest('id')->paginate(10);

        $user->unreadNotifications->markAsRead();

        return view('receipts', [
            'orders' => $orders,
            'newIds' => $newIds,
            'verified' => ReceiptMailer::isVerified($user),
        ]);
    }
}
