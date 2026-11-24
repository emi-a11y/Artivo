<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
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

        if ($request->user() && $request->user()->isAdmin()) {
            return back()->with('status', 'Admin accounts cannot purchase artworks. Use a buyer account.');
        }
        // Signed-in buyers cannot add their own work, or something they already own or have an open order for.
        if ($user = $request->user()) {
            if ($message = $this->blockedReason($user, $artwork)) {
                return redirect()->route($artwork->artist_id === $user->id ? 'cart' : 'orders')->with('status', $message);
            }
        }

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
            return redirect()->guest(route('login'))
                ->with('status', 'Please log in to check out.');
        }

        if ($user->isAdmin()) {
            return redirect()->route('cart')->with('status', 'Admin accounts cannot purchase artworks. Use a buyer account.');
        }
        $items = Cart::items();
        if ($items->isEmpty()) {
            return redirect()->route('cart')->with('status', 'Your cart is empty.');
        }

        // The cart may have been filled before the artwork was bought, so check every item again.
        $blocked = [];
        foreach ($items as $art) {
            if ($this->blockedReason($user, $art)) {
                Cart::remove($art->id);
                $blocked[] = '"' . $art->title . '"';
            }
        }
        if ($blocked) {
            return redirect()->route('cart')->with(
                'status',
                'Removed from your cart: ' . implode(', ', $blocked) . '. You already own them, have an unpaid order for them, or they are your own work. Check out again.'
            );
        }

        $order = DB::transaction(fn () => Order::place(
            $user,
            $items->map(fn (Artwork $a) => ['artwork' => $a, 'license' => $a->license])->all(),
            'Pending'
        ));
        Cart::clear();

        return redirect()->route('orders.pay', $order)
            ->with('status', "Order {$order->code} created. Scan the QR code to pay, then send the receipt.");
    }

    /** A short explanation when the buyer should not get this artwork again, or null when it is fine. */
    private function blockedReason(User $user, Artwork $artwork): ?string
    {
        if ($artwork->artist_id === $user->id) {
            return "You can't buy your own artwork.";
        }

        $held = Order::holdingArtwork($user, $artwork->id);
        if (! $held) {
            return null;
        }

        return $held->status === 'Paid'
            ? "You already own \"{$artwork->title}\" (order {$held->code})."
            : "\"{$artwork->title}\" is already in your unpaid order {$held->code}. Pay for it or cancel that order first.";
    }
}
