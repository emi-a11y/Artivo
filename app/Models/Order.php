<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = ['Pending', 'Paid', 'Refunded', 'Cancelled'];

    protected $fillable = [
        'code', 'user_id', 'subtotal', 'service_fee', 'total', 'status',
        'payment_method_id', 'payment_reference', 'payment_proof_path', 'payment_submitted_at', 'payment_note', 'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'service_fee' => 'float',
        'total' => 'float',
        'payment_submitted_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * The buyer's order that already holds this artwork: a Paid one (owned) or an unpaid one still open.
     * Cancelled and Refunded orders do not count, so the artwork can be bought again.
     */
    public static function holdingArtwork(User $user, int $artworkId): ?self
    {
        return self::where('user_id', $user->id)
            ->whereIn('status', ['Paid', 'Pending'])
            ->whereHas('items', fn ($q) => $q->where('artwork_id', $artworkId))
            ->orderByRaw("status = 'Paid' desc")
            ->first();
    }

    /**
     * Statuses the admin may switch this order to by hand. Payment is confirmed only on the
     * Payments page (after checking the receipt), so only a Paid order can be moved on, to Refunded.
     */
    public function adminStatusChoices(): array
    {
        return $this->status === 'Paid' ? ['Paid', 'Refunded'] : [];
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /** What the buyer and admin see: Paid, Refunded, Under review, Payment rejected or Awaiting payment. */
    public function getPaymentStateAttribute(): string
    {
        if ($this->status !== 'Pending') {
            return $this->status;
        }
        if ($this->payment_submitted_at) {
            return 'Under review';
        }

        return $this->payment_note ? 'Payment rejected' : 'Awaiting payment';
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->payment_proof_path ? asset('storage/' . $this->payment_proof_path) : null;
    }

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
