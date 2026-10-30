<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /** My Wishlist page: the published artworks the logged-in user saved, newest first. */
    public function index(Request $request)
    {
        $artworks = $request->user()->wishlist()
            ->with('artist')
            ->where('artworks.status', 'Published')
            ->orderByDesc('wishlists.created_at')
            ->get();

        return view('wishlist', ['artworks' => $artworks]);
    }

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
