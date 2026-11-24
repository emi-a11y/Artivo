<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/** Admin > Artists: upload the picture that appears on the public Artists page. */
class ArtistPhotoController extends Controller
{
    public function index()
    {
        return view('admin.artists', [
            'artists' => User::where('role', 'Artist')
                ->withCount('artworks')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /** Creates a new artist account (optionally with a picture). */
    public function add(Request $request)
    {
        $data = $request->validateWithBag('artistPhoto', [
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'name.required' => 'The artist name is required.',
            'email.required' => 'The email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'That email is already used by another account.',
            'photo.max' => 'The picture must be 5 MB or smaller.',
            'photo.image' => 'The file must be an image (JPG, PNG or WEBP).',
            'photo.mimes' => 'The picture must be a JPG, PNG or WEBP file.',
        ]);

        $user = User::create([
            'name' => trim($data['name']),
            'email' => strtolower($data['email']),
            'password' => Hash::make(\Illuminate\Support\Str::random(32)),
            'role' => 'Artist',
            'status' => 'Active',
        ]);
        if ($request->hasFile('photo')) {
            $user->update(['photo_path' => $request->file('photo')->store('artists', 'public')]);
        }

        return back()->with('status', "Artist {$user->name} was added.");
    }

    public function store(Request $request, User $user)
    {
        abort_unless($user->role === 'Artist', 404);

        $request->validateWithBag('artistPhoto', [
            'photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'photo.required' => 'Choose a picture first.',
            'photo.uploaded' => 'The picture could not be uploaded. It may be larger than the server limit.',
            'photo.max' => 'The picture must be 5 MB or smaller.',
            'photo.image' => 'The file must be an image (JPG, PNG or WEBP).',
            'photo.mimes' => 'The picture must be a JPG, PNG or WEBP file.',
        ]);

        $old = $user->photo_path;
        $user->update(['photo_path' => $request->file('photo')->store('artists', 'public')]);
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return back()->with('status', "Picture for {$user->name} was saved.");
    }

    public function rename(Request $request, User $user)
    {
        abort_unless($user->role === 'Artist', 404);

        $data = $request->validateWithBag('artistPhoto', [
            'name' => ['required', 'string', 'max:60'],
        ], [
            'name.required' => 'The artist name cannot be empty.',
            'name.max' => 'The artist name must be 60 characters or fewer.',
        ]);

        $user->update(['name' => trim($data['name'])]);

        return back()->with('status', 'Artist name was saved.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'Artist', 404);

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
            $user->update(['photo_path' => null]);
        }

        return back()->with('status', "Picture for {$user->name} was removed.");
    }
        /** Deletes an artist account, only when it has no artworks. */
    public function delete(User $user)
    {
        abort_unless($user->role === 'Artist', 404);

        if ($user->artworks()->count() > 0) {
            return back()->with('status', "{$user->name} still has artworks. Delete or reassign them first.");
        }

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }
        $name = $user->name;
        $user->delete();

        return back()->with('status', "Artist {$name} was deleted.");
    }
}
