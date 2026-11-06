<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artwork extends Model
{
    public const STATUSES = ['Published', 'Pending', 'Draft'];

    protected $fillable = [
        'title', 'artist_id', 'category_id', 'genre_id', 'price',
        'file_type', 'resolution', 'file_size', 'description', 'image_path', 'status', 'rating', 'reviews',
    ];

    protected $casts = [
        'price' => 'float',
        'rating' => 'float',
    ];

    /** Public URL of the uploaded image, or null when the artwork has none (views fall back to the placeholder). */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }
}
