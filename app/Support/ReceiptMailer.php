<?php

namespace App\Support;

use App\Mail\OrderReceiptMail;
use App\Models\Order;
use App\Models\User;
use App\Notifications\ReceiptReadyNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/** Emails the receipt of a Paid order to the buyer. The receipt itself always stays in My Orders as well. */
class ReceiptMailer
{
    private const FAKE_TLDS = ['test', 'example', 'invalid', 'local', 'localhost', 'lan'];

    private const FAKE_DOMAINS = ['example.com', 'example.org', 'example.net'];

    /** A real address: valid format, not a placeholder domain, and the domain can receive mail. */
    public static function isRealEmail(?string $email): bool
    {
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        $tld = substr(strrchr($domain, '.') ?: '.', 1);
        if (in_array($tld, self::FAKE_TLDS, true) || in_array($domain, self::FAKE_DOMAINS, true)) {
            return false;
        }

        return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
    }

    /** The buyer proved the mailbox exists: they clicked the verification link we emailed, or signed in with Google. */
    public static function isVerified(?User $user): bool
    {
        return $user && ($user->email_verified_at || $user->google_id);
    }

    /** Receipts are emailed only to a real domain AND a verified mailbox. */
    public static function canEmail(?User $user): bool
    {
        return $user && self::isVerified($user) && self::isRealEmail($user->email);
    }

    /**
     * Called when a payment is confirmed.
     * 1) The receipt always goes to the buyer's account in the system (My Receipts + a "new" badge).
     * 2) If the buyer has a verified, real email (or signed in with Google), it is emailed there too.
     * Returns true when the email was sent.
     */
    public static function deliver(Order $order): bool
    {
        $order->loadMissing(['items', 'user', 'paymentMethod']);

        $already = $order->user->notifications
            ->contains(fn ($n) => ($n->data['order_id'] ?? null) === $order->id);
        if (! $already) {
            $order->user->notify(new ReceiptReadyNotification($order));
        }

        return self::send($order);
    }

    /** Returns true when the email was handed to the mail server. Never throws, so confirming a payment cannot fail because of email. */
    public static function send(Order $order): bool
    {
        $order->loadMissing(['items', 'user', 'paymentMethod']);
        $email = $order->user->email ?? null;

        if ($order->status !== 'Paid' || ! self::canEmail($order->user)) {
            return false;
        }

        try {
            Mail::to($email)->send(new OrderReceiptMail($order));
            DB::table('orders')->where('id', $order->id)->update(['receipt_emailed_at' => now()]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Receipt email for ' . $order->code . ' failed: ' . $e->getMessage());

            return false;
        }
    }
}
