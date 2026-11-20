<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Gives the buyer the artwork file, but only for items of their own Paid orders. */
class DownloadController extends Controller
{
    public function show(Request $request, Order $order, int $item)
    {
        // Someone else's order looks like it does not exist.
        abort_unless($order->user_id === $request->user()->id, 404);

        if ($order->status !== 'Paid') {
            return redirect()->route('orders')
                ->with('status', "Downloads unlock once {$order->code} is paid. This order is {$order->payment_state}.");
        }

        $line = $order->items()->with('artwork')->findOrFail($item);
        $artwork = $line->artwork;
            if ($artwork?->original_path) {
                $disk = Storage::disk('local');          // private original
                $path = $artwork->original_path;
            } else {
                $disk = Storage::disk('public');         // older artwork not yet protected
                $path = $artwork?->image_path;
            }

        if (! $path || ! $disk->exists($path)) {
            return redirect()->route('orders')
                ->with('status', "The file for \"{$line->title}\" is not available right now. Please contact the admin.");
        }

        // Example: moonlit-samurai-personal-license.png
        $name = Str::slug($line->title . ' ' . $line->license) . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return $disk->download($path, $name);
    }
}
