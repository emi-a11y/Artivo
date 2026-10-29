<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use App\Models\Genre;
use App\Models\User;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PageController extends Controller
{
    private const PER_PAGE = 6;

    private function published()
    {
        return Artwork::with(['artist', 'category', 'genre'])->where('status', 'Published');
    }

    public function home()
    {
        return view('home', [
            'categories' => Category::orderBy('id')->get(),
            'featured' => $this->published()->orderByDesc('rating')->orderByDesc('reviews')->take(5)->get(),
            'newArrivals' => $this->published()->latest()->latest('id')->take(5)->get(),
            'artists' => User::where('role', 'Artist')
                ->withCount(['artworks' => fn ($q) => $q->where('status', 'Published')])
                ->orderByDesc('artworks_count')->orderBy('name')->take(5)->get(),
        ]);
    }

    public function browse(Request $request)
    {
        $query = $this->published();

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                    ->orWhereHas('artist', fn ($a) => $a->where('name', 'like', "%{$search}%"));
            });
        }

        $selectedCategories = array_filter(Arr::wrap($request->query('category')));
        if ($selectedCategories) {
            $query->whereHas('category', fn ($c) => $c->whereIn('name', $selectedCategories));
        }

        $selectedGenres = array_filter(Arr::wrap($request->query('genre')));
        if ($selectedGenres) {
            $query->whereHas('genre', fn ($g) => $g->whereIn('name', $selectedGenres));
        }

        if (is_numeric($request->query('min'))) {
            $query->where('price', '>=', (float) $request->query('min'));
        }
        if (is_numeric($request->query('max'))) {
            $query->where('price', '<=', (float) $request->query('max'));
        }

        $sorts = [
            'newest' => 'Newest',
            'price_asc' => 'Price: low to high',
            'price_desc' => 'Price: high to low',
            'popular' => 'Most popular',
        ];
        $sort = array_key_exists($request->query('sort'), $sorts) ? $request->query('sort') : 'newest';
        match ($sort) {
            'price_asc' => $query->orderBy('price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price')->orderBy('id'),
            'popular' => $query->orderByDesc('reviews')->orderBy('id'),
            default => $query->latest()->latest('id'),
        };

        return view('browse', [
            'categories' => Category::orderBy('id')->get(),
            'genres' => Genre::orderBy('id')->get(),
            'artworks' => $query->paginate(self::PER_PAGE)->withQueryString(),
            'selectedCategories' => $selectedCategories,
            'selectedGenres' => $selectedGenres,
            'sorts' => $sorts,
            'sort' => $sort,
        ]);
    }

    public function artwork(int $id)
    {
        $art = $this->published()->findOrFail($id);

        // Same artist first, then the rest, so the row fills up with up to five cards.
        $more = $this->published()->where('id', '!=', $art->id)
            ->orderByRaw('artist_id = ? desc', [$art->artist_id])
            ->latest()->latest('id')->take(5)->get();

        $user = auth()->user();

        return view('artwork', [
            'art' => $art,
            'more' => $more,
            'licenses' => Cart::LICENSES,
            'wished' => $user ? $user->wishlist()->where('artworks.id', $art->id)->exists() : false,
        ]);
    }

    public function cart()
    {
        $items = Cart::items();
        $subtotal = $items->sum('price');
        $fee = $items->isEmpty() ? 0.0 : Cart::SERVICE_FEE;

        return view('cart', [
            'items' => $items,
            'subtotal' => $subtotal,
            'fee' => $fee,
            'total' => $subtotal + $fee,
        ]);
    }

    /** Pages that are linked from the wireframes but are not part of the lab screens. */
    public function page(string $slug)
    {
        $pages = [
            'artists' => 'Artists',
            'sell' => 'Sell your art',
            'about' => 'About Artivo',
            'help' => 'Help Center',
            'terms' => 'Terms of Use',
            'privacy' => 'Privacy Policy',
        ];
        abort_unless(isset($pages[$slug]), 404);

        return view('page', ['title' => $pages[$slug]]);
    }
}
