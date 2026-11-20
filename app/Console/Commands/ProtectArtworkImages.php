<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use App\Support\ArtworkImages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** One-time: moves artworks uploaded before this feature to private storage and makes their previews. */
class ProtectArtworkImages extends Command
{
    protected $signature = 'artworks:protect';
    protected $description = 'Move existing full-size artwork files to private storage and create small public previews';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $done = 0;

        foreach (Artwork::whereNotNull('image_path')->whereNull('original_path')->get() as $artwork) {
            if (! $public->exists($artwork->image_path)) {
                $this->warn("#{$artwork->id} {$artwork->title}: file is missing, skipped.");
                continue;
            }
            try {
                $preview = ArtworkImages::previewData($public->path($artwork->image_path));
            } catch (\Throwable $e) {
                $this->warn("#{$artwork->id} {$artwork->title}: {$e->getMessage()}, skipped. Re-upload it in Admin > Artworks > Edit instead.");
                continue;
            }

            $original = 'artworks/originals/' . basename($artwork->image_path);
            $private->put($original, $public->get($artwork->image_path));
            $previewPath = 'previews/' . Str::random(40) . '.' . $preview['ext'];
            $public->put($previewPath, $preview['bytes']);

            $old = $artwork->image_path;
            $artwork->update(['original_path' => $original, 'image_path' => $previewPath]);
            $public->delete($old);
            $done++;
            $this->line("#{$artwork->id} {$artwork->title}: protected.");
        }

        $this->info("{$done} artwork(s) protected.");

        return self::SUCCESS;
    }
}
