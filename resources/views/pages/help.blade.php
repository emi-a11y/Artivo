<p class="lead-text">Quick answers to the most common questions about buying, paying and downloading on Artivo.</p>

<div class="accordion mt-4" id="helpFaq">
    @php
        $faqs = [
            ['How do I buy an artwork?', 'Open the artwork, choose Add to Cart, then go to the cart and select Checkout. An order is created with a unique order code and the status Pending. You must be logged in to check out.'],
            ['How do I pay?', 'After checkout, the payment page shows the shop’s QR code. Scan it with your e-wallet app (for example GCash), send the exact amount, then upload a screenshot of the receipt (JPG or PNG, up to 4 MB), type the 13-digit reference number shown on the receipt, and enter your 11-digit mobile number starting with 09.'],
            ['How long does payment confirmation take?', 'Your order shows Under review after you submit the receipt. An administrator compares the receipt and reference number with the payment records and then confirms or rejects it. Confirmation is done by a person, so it may take some time.'],
            ['My payment was rejected. What now?', 'Open My Orders. A rejected order shows the reason given by the administrator. Fix the problem, such as a wrong reference number or an unreadable screenshot, and submit the receipt again.'],
            ['Where do I download my artwork?', 'Go to My Orders. When an order is Paid, a Download button appears beside each artwork. Only the buyer who paid for the order can download the file.'],
            ['Can I cancel an order?', 'Yes. An unpaid order can be cancelled from My Orders. Orders that stay unpaid for several days are cancelled automatically. Orders already under review or paid cannot be cancelled by the buyer.'],
            ['Why are the images on the site small and watermarked?', 'To protect the artists. The preview is a low-resolution copy with a watermark. The full-quality original is delivered only after payment is confirmed.'],
            ['I forgot my password.', 'On the login page choose “Forgot password?”, enter your email, and follow the reset link. You can also choose a new password later in your account.'],
            ['Why is my login locked?', 'After five wrong password attempts the login form is locked for 60 seconds to protect accounts. Wait for the countdown to end and try again.'],
            ['Can I get a refund?', 'Refunds are handled case by case by the administrator, for example when a file is damaged or was charged twice. Contact us with your order code.'],
        ];
    @endphp
    @foreach ($faqs as $i => [$q, $a])
        <div class="accordion-item">
            <h2 class="accordion-header m-0">
                <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" aria-expanded="{{ $i ? 'false' : 'true' }}" aria-controls="faq{{ $i }}">{{ $q }}</button>
            </h2>
            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#helpFaq">
                <div class="accordion-body">{{ $a }}</div>
            </div>
        </div>
    @endforeach
</div>

<h2>Still need help?</h2>
@if ($contact = \App\Models\Setting::get('contact_email'))
    <p>Send us a message at <a href="mailto:{{ $contact }}">{{ $contact }}</a> and include your order code so we can find your order quickly.</p>
@else
    <p>Contact the Artivo administrators and include your order code so your order can be found quickly.</p>
@endif
