<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

/** In-system notice in the buyer's account: "your receipt is ready". Always created, whether or not an email is sent. */
class ReceiptReadyNotification extends Notification
{
    public function __construct(public Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'code' => $this->order->code,
            'total' => $this->order->total,
        ];
    }
}
