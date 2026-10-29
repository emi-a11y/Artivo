<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArtworkController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $artworks = Artwork::with(['artist', 'category', 'genre'])
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                    ->orWhereHas('artist', fn ($a) => $a->where('name', 'like', "%{$search}%"));
            }))
            ->when($request->query('category'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->query('genre'), fn ($q, $v) => $q->where('genre_id', $v))
            ->latest()->latest('id')->paginate(6)->withQueryString();

        return view('admin.artworks', [
            'artworks' => $artworks,
            'categories' => Category::orderBy('id')->get(),
            'genres' => Genre::orderBy('id')->get(),
            'artists' => User::where('role', 'Artist')->orderBy('name')->get(),
            'statuses' => Artwork::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $artwork = Artwork::create($this->validated($request));

        return redirect()->route('admin.artworks')->with('status', "\"{$artwork->title}\" was added.");
    }

    public function update(Request $request, Artwork $artwork)
    {
        $artwork->update($this->validated($request));

        return redirect()->route('admin.artworks', $request->only('page', 'q', 'category', 'genre'))
            ->with('status', "\"{$artwork->title}\" was updated.");
    }

    public function destroy(Artwork $artwork)
    {
        $title = $artwork->title;
        $artwork->delete();

        return redirect()->route('admin.artworks')->with('status', "\"{$title}\" was deleted.");
    }

    private function validated(Request $request): array
    {
        return $request->validateWithBag('artwork', [
            'title' => ['required', 'string', 'max:120'],
            'artist_id' => ['required', Rule::exists('users', 'id')->where('role', 'Artist')],
            'category_id' => ['required', 'exists:categories,id'],
            'genre_id' => ['required', 'exists:genres,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999'],
            'status' => ['required', Rule::in(Artwork::STATUSES)],
            'file_type' => ['nullable', 'string', 'max:60'],
            'resolution' => ['nullable', 'string', 'max:60'],
            'file_size' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
