<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /** Pay page: the QR code(s), the amount, and the form for the reference number and receipt screenshot. */
    public function show(Request $request, Order $order)
    {
        $this->ownOrFail($request, $order);

        if ($order->status !== 'Pending') {
            return redirect()->route('orders')->with('status', "{$order->code} is {$order->status} and cannot be paid.");
        }
        if ($order->payment_state === 'Under review') {
            return redirect()->route('orders')->with('status', "Your payment for {$order->code} is being checked by the admin.");
        }

        return view('pay', [
            'order' => $order->load('items'),
            'methods' => PaymentMethod::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /** The buyer says "I paid", with the reference number and a screenshot of the receipt. */
    public function submit(Request $request, Order $order)
    {
        $this->ownOrFail($request, $order);

        if ($order->status !== 'Pending' || $order->payment_state === 'Under review') {
            return redirect()->route('orders')->with('status', 'This order cannot take a new payment right now.');
        }

        // Spaces are allowed when typing or pasting ("1234 567 890123"); only the 13 digits are kept.
        $request->merge(['payment_reference' => preg_replace('/\s+/', '', (string) $request->input('payment_reference'))]);

        $data = $request->validate([
            'payment_method_id' => ['required', Rule::exists('payment_methods', 'id')->where('is_active', true)],
            'payment_reference' => ['required', 'regex:/^\d{13}$/', Rule::unique('orders', 'payment_reference')->ignore($order->id)],
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ], [
            'payment_method_id.required' => 'Please choose the method you paid with.',
            'payment_method_id.exists' => 'That payment method is not available.',
            'payment_reference.required' => 'Please enter the reference number from your receipt.',
            'payment_reference.regex' => 'The reference number must be exactly 13 digits (numbers only), as shown on your GCash receipt.',
            'payment_reference.unique' => 'That reference number was already used on another order.',
            'proof.required' => 'Please upload a screenshot of your payment receipt.',
            'proof.image' => 'The receipt must be an image (JPG or PNG).',
            'proof.mimes' => 'The receipt must be a JPG or PNG image.',
            'proof.max' => 'The receipt image must be 4 MB or smaller.',
        ]);

        if ($order->payment_proof_path) {
            Storage::disk('public')->delete($order->payment_proof_path);   // replaced by the new upload
        }

        $order->update([
            'payment_method_id' => $data['payment_method_id'],
            'payment_reference' => trim($data['payment_reference']),
            'payment_proof_path' => $request->file('proof')->store('payment-proofs', 'public'),
            'payment_submitted_at' => now(),
            'payment_note' => null,
        ]);

        return redirect()->route('orders')
            ->with('status', "Payment details for {$order->code} were sent. The admin will confirm your payment soon.");
    }

    /** The buyer cancels an unpaid order. Not possible while the admin is checking a receipt, or once it is paid. */
    public function cancel(Request $request, Order $order)
    {
        $this->ownOrFail($request, $order);

        if ($order->status !== 'Pending' || $order->payment_state === 'Under review') {
            return redirect()->route('orders')->with('status', "{$order->code} cannot be cancelled right now.");
        }

        $order->update(['status' => 'Cancelled']);

        return redirect()->route('orders')->with('status', "{$order->code} was cancelled.");
    }

    /** Printable receipt, only for a paid order and only for its owner (or an admin). */
    public function receipt(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($order->user_id === $user->id || $user->isAdmin(), 404);
        abort_unless($order->status === 'Paid', 404);

        return view('receipt', ['order' => $order->load(['items', 'user', 'paymentMethod'])]);
    }

    private function ownOrFail(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }
}
