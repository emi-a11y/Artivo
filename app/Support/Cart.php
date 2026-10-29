<?php

namespace App\Support;

use App\Models\Artwork;
use Illuminate\Support\Collection;

/** Session-based shopping cart: one entry per artwork id, value = chosen license. */
class Cart
{
    public const LICENSES = ['Personal license', 'Commercial license'];
    public const SERVICE_FEE = 1.00;

    public static function raw(): array
    {
        return session('cart', []);
    }

    public static function count(): int
    {
        return count(self::raw());
    }

    public static function add(int $artworkId, string $license): void
    {
        $cart = self::raw();
        $cart[$artworkId] = $license;
        session(['cart' => $cart]);
    }

    public static function remove(int $artworkId): void
    {
        $cart = self::raw();
        unset($cart[$artworkId]);
        session(['cart' => $cart]);
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }

    /** @return Collection<int, Artwork> with a ->license attribute added */
    public static function items(): Collection
    {
        $raw = self::raw();
        if ($raw === []) {
            return collect();
        }

        return Artwork::with('artist')
            ->whereIn('id', array_keys($raw))
            ->where('status', 'Published')
            ->get()
            ->each(fn (Artwork $art) => $art->setAttribute('license', $raw[$art->id]));
    }
}
