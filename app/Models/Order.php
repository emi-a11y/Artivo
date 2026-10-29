<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = ['Pending', 'Paid', 'Refunded'];

    protected $fillable = ['code', 'user_id', 'subtotal', 'service_fee', 'total', 'status'];

    protected $casts = [
        'subtotal' => 'float',
        'service_fee' => 'float',
        'total' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Creates an order and its lines in one go; $lines = [['artwork' => Artwork, 'license' => string], ...]. */
    public static function place(User $user, iterable $lines, string $status = 'Paid', $createdAt = null): self
    {
        $subtotal = 0.0;
        foreach ($lines as $line) {
            $subtotal += $line['artwork']->price;
        }
        $fee = $subtotal > 0 ? 1.00 : 0.00;

        $order = self::create([
            'user_id' => $user->id,
            'subtotal' => $subtotal,
            'service_fee' => $fee,
            'total' => $subtotal + $fee,
            'status' => $status,
        ]);
        $order->code = 'ORD-' . (1000 + $order->id);
        if ($createdAt !== null) {
            $order->created_at = $createdAt;
        }
        $order->save();

        foreach ($lines as $line) {
            $order->items()->create([
                'artwork_id' => $line['artwork']->id,
                'title' => $line['artwork']->title,
                'price' => $line['artwork']->price,
                'license' => $line['license'],
            ]);
        }

        return $order;
    }
}
