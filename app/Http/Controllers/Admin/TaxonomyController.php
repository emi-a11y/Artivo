<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** One controller for both Categories and Genres (same shape: a unique name). */
class TaxonomyController extends Controller
{
    private function model(string $type): string
    {
        return match ($type) {
            'categories' => Category::class,
            'genres' => Genre::class,
            default => abort(404),
        };
    }

    public function index(string $type)
    {
        $model = $this->model($type);

        return view('admin.taxonomy', [
            'type' => $type,
            'title' => ucfirst($type),
            'singular' => $type === 'categories' ? 'category' : 'genre',
            'items' => $model::withCount('artworks')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        $model = $this->model($type);
        $data = $request->validateWithBag('taxonomy', [
            'name' => ['required', 'string', 'max:60', Rule::unique((new $model)->getTable(), 'name')],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // categories only, 5 MB
        ], $this->imageMessages());
        unset($data['image']);
        if ($type === 'categories' && $request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }
        $item = $model::create($data);

        return redirect()->route("admin.{$type}")->with('status', "\"{$item->name}\" was added.");
    }

    /** Adds or replaces the picture of one category (shown on the home page tiles). */
    public function image(Request $request, int $id)
    {
        $category = Category::findOrFail($id);
        $request->validateWithBag('categoryImage', [
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->imageMessages());

        $old = $category->image_path;
        $category->update(['image_path' => $request->file('image')->store('categories', 'public')]);
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return back()->with('status', "Picture for \"{$category->name}\" was saved.");
    }

    /** Removes the picture again (the tile goes back to the dark default). */
    public function removeImage(int $id)
    {
        $category = Category::findOrFail($id);
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
            $category->update(['image_path' => null]);
        }

        return back()->with('status', "Picture for \"{$category->name}\" was removed.");
    }

    private function imageMessages(): array
    {
        return [
            'image.uploaded' => 'The image could not be uploaded. It may be larger than the server limit.',
            'image.max' => 'The image must be 5 MB or smaller.',
            'image.image' => 'The file must be an image (JPG, PNG or WEBP).',
            'image.mimes' => 'The image must be a JPG, PNG or WEBP file.',
        ];
    }

    public function destroy(Request $request)
    {
        // Read by name: route defaults ('type') are not reliably passed in argument order.
        $type = (string) $request->route('type');
        $item = $this->model($type)::withCount('artworks')->findOrFail((int) $request->route('id'));

        if ($item->artworks_count > 0) {
            return back()->with('status', "\"{$item->name}\" is used by {$item->artworks_count} artwork(s), so it cannot be deleted.");
        }
        if (! empty($item->image_path)) {
            Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();

        return back()->with('status', "\"{$item->name}\" was deleted.");
    }
}
