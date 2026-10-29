<?php

namespace App\Http\Controllers;

use App\Models\Artwork;

class WishlistController extends Controller
{
    public function toggle(int $id)
    {
        $artwork = Artwork::where('status', 'Published')->findOrFail($id);
        $user = auth()->user();

        if (! $user) {
            return back()->with('status', 'Please log in to save artworks to your wishlist.')->with('openLogin', true);
        }

        $result = $user->wishlist()->toggle($artwork->id);
        $saved = count($result['attached']) > 0;

        return back()->with('status', $saved ? 'Saved to your wishlist.' : 'Removed from your wishlist.');
    }
}
