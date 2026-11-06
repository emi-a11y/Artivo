<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $data = $this->withImage($request, $this->validated($request));
        $artwork = Artwork::create($data);

        return redirect()->route('admin.artworks')->with('status', "\"{$artwork->title}\" was added.");
    }

    public function update(Request $request, Artwork $artwork)
    {
        $data = $this->withImage($request, $this->validated($request), $artwork);
        $artwork->update($data);

        return redirect()->route('admin.artworks', $request->only('page', 'q', 'category', 'genre'))
            ->with('status', "\"{$artwork->title}\" was updated.");
    }

    public function destroy(Artwork $artwork)
    {
        $title = $artwork->title;
        if ($artwork->image_path) {
            Storage::disk('public')->delete($artwork->image_path);
        }
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
            'description' => ['nullable', 'string', 'max:1000'],
            // The image is optional on edit (keeps the current one) and on add (shows the placeholder).
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:102400'], // 100 MB
        ], [
            'image.uploaded' => 'The image could not be uploaded. It may be larger than the server limit (upload_max_filesize in php.ini).',
            'image.max' => 'The image must be 100 MB or smaller.',
            'image.image' => 'The file must be an image (JPG, PNG, WEBP or GIF).',
            'image.mimes' => 'The image must be a JPG, PNG, WEBP or GIF file.',
        ]);
    }

    /**
     * Saves the uploaded image (if any) on the public disk and fills in file type,
     * resolution and file size from the real file, so nobody has to type them.
     */
    private function withImage(Request $request, array $data, ?Artwork $existing = null): array
    {
        unset($data['image']);

        $file = $request->file('image');
        if (! $file instanceof UploadedFile) {
            return $data;
        }

        // Read the metadata before the file is moved.
        $dimensions = @getimagesize($file->getRealPath());
        $data['file_type'] = strtoupper($file->guessExtension() ?: $file->getClientOriginalExtension());
        $data['resolution'] = $dimensions ? "{$dimensions[0]} x {$dimensions[1]} px" : null;
        $data['file_size'] = $this->humanSize((int) $file->getSize());

        $data['image_path'] = $file->store('artworks', 'public');

        if ($existing && $existing->image_path) {
            Storage::disk('public')->delete($existing->image_path);
        }

        return $data;
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        return max(1, (int) round($bytes / 1024)) . ' KB';
    }
}
