<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use App\Support\ReceiptMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/** Proves that the buyer's email address really exists by emailing a one-hour link. */
class EmailVerificationController extends Controller
{
    public function send(Request $request)
    {
        $user = $request->user();

        if (ReceiptMailer::isVerified($user)) {
            return back()->with('status', 'Your email is already verified.');
        }
        if (! ReceiptMailer::isRealEmail($user->email)) {
            return back()->with('status', "{$user->email} does not look like a real email address. Receipts will stay in your account.");
        }

        $url = URL::temporarySignedRoute('email.verify.confirm', now()->addHour(), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        try {
            Mail::to($user->email)->send(new VerifyEmailMail($user, $url));
        } catch (\Throwable $e) {
            Log::warning('Verification email for user ' . $user->id . ' failed: ' . $e->getMessage());

            return back()->with('status', 'The verification email could not be sent right now. Please try again later.');
        }

        return back()->with('status', "We sent a verification link to {$user->email}. It works for one hour.");
    }

    /** Opened from the email. The signed link itself is the proof, so no login is needed. */
    public function confirm(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);
        abort_unless(hash_equals(sha1($user->email), $hash), 403);

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return redirect()->route('orders')->with('status', 'Email verified. Your receipts will now be sent to ' . $user->email . '.');
    }
}
