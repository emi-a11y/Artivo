<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Http\Request;
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
        ]);
        $item = $model::create($data);

        return redirect()->route("admin.{$type}")->with('status', "\"{$item->name}\" was added.");
    }

    public function destroy(Request $request)
    {
        // Read by name: route defaults ('type') are not reliably passed in argument order.
        $type = (string) $request->route('type');
        $item = $this->model($type)::withCount('artworks')->findOrFail((int) $request->route('id'));

        if ($item->artworks_count > 0) {
            return back()->with('status', "\"{$item->name}\" is used by {$item->artworks_count} artwork(s), so it cannot be deleted.");
        }
        $item->delete();

        return back()->with('status', "\"{$item->name}\" was deleted.");
    }
}
