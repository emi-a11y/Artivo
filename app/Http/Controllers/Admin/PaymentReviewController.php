<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** The admin checks each submitted payment against the receipt, then confirms or rejects it. */
class PaymentReviewController extends Controller
{
    private const PER_PAGE = 8;

    private const FILTERS = ['Under review', 'Paid', 'Rejected', 'Awaiting payment'];

    public function index(Request $request)
    {
        $filter = in_array($request->query('state'), self::FILTERS, true) ? $request->query('state') : null;

        $query = Order::with(['user', 'paymentMethod'])->withCount('items');
        match ($filter) {
            'Under review' => $query->where('status', 'Pending')->whereNotNull('payment_submitted_at'),
            'Paid' => $query->where('status', 'Paid'),
            'Rejected' => $query->where('status', 'Pending')->whereNull('payment_submitted_at')->whereNotNull('payment_note'),
            'Awaiting payment' => $query->where('status', 'Pending')->whereNull('payment_submitted_at')->whereNull('payment_note'),
            default => $query->whereIn('status', ['Pending', 'Paid']),
        };
        // Waiting reviews first, then newest.
        $orders = $query->orderByRaw('(payment_submitted_at is not null and status = ?) desc', ['Pending'])
            ->latest()->latest('id')->paginate(self::PER_PAGE)->withQueryString();

        $tabs = [];
        foreach (['All' => null] + array_combine(self::FILTERS, self::FILTERS) as $label => $value) {
            $tabs[] = ['label' => $label, 'value' => $value, 'active' => $value === $filter];
        }

        return view('admin.payments', ['orders' => $orders, 'tabs' => $tabs]);
    }

    public function confirm(Request $request, int $order)
    {
        $result = DB::transaction(function () use ($order) {
            $o = Order::whereKey($order)->lockForUpdate()->firstOrFail();
            if ($o->status !== 'Pending' || ! $o->payment_submitted_at) {
                return null;
            }
            $o->update(['status' => 'Paid', 'paid_at' => now(), 'payment_note' => null]);

            return $o;
        });

        return back()->with('status', $result
            ? "{$result->code} is now Paid."
            : 'That payment was already reviewed.');
    }

    public function reject(Request $request, int $order)
    {
        $data = $request->validate(['payment_note' => ['nullable', 'string', 'max:200']]);

        $result = DB::transaction(function () use ($order, $data) {
            $o = Order::whereKey($order)->lockForUpdate()->firstOrFail();
            if ($o->status !== 'Pending' || ! $o->payment_submitted_at) {
                return null;
            }
            // The buyer can send a corrected reference and screenshot again.
            $o->update([
                'payment_submitted_at' => null,
                'payment_note' => $data['payment_note'] ?: 'The payment could not be verified. Please check and send it again.',
            ]);

            return $o;
        });

        return back()->with('status', $result
            ? "Payment for {$result->code} was rejected. The buyer can submit it again."
            : 'That payment was already reviewed.');
    }
}
