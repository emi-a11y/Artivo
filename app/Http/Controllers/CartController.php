<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function add(Request $request, int $id)
    {
        $artwork = Artwork::where('status', 'Published')->findOrFail($id);
        $data = $request->validate(['license' => ['nullable', Rule::in(Cart::LICENSES)]]);

        Cart::add($artwork->id, $data['license'] ?? Cart::LICENSES[0]);

        if ($request->boolean('buy')) {
            return redirect()->route('cart');
        }

        return back()->with('status', '"' . $artwork->title . '" was added to your cart.');
    }

    public function remove(int $id)
    {
        Cart::remove($id);

        return redirect()->route('cart')->with('status', 'Item removed from your cart.');
    }

    public function checkout()
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->route('cart')
                ->with('status', 'Please log in to check out.')
                ->with('openLogin', true);
        }

        $items = Cart::items();
        if ($items->isEmpty()) {
            return redirect()->route('cart')->with('status', 'Your cart is empty.');
        }

        $order = DB::transaction(fn () => Order::place(
            $user,
            $items->map(fn (Artwork $a) => ['artwork' => $a, 'license' => $a->license])->all(),
            'Paid'
        ));
        Cart::clear();

        return redirect()->route('cart')->with('status', "Order {$order->code} placed. Payment is simulated in this school project.");
    }
}
