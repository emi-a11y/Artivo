<?php

namespace App\Http\Controllers;

use App\Models\User;

class ArtistController extends Controller
{
    /** Public "Artists" page: a sliding showcase of every active artist. */
    public function index()
    {
        $artists = User::where('role', 'Artist')
            ->where('status', '!=', 'Blocked')
            ->withCount(['artworks' => fn ($q) => $q->where('status', 'Published')])
            ->orderByRaw('photo_path IS NULL')   // artists with a picture first
            ->orderByDesc('artworks_count')
            ->orderBy('name')
            ->get();

        return view('artists', ['artists' => $artists]);
    }
}
