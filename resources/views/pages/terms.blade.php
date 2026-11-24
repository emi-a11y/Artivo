<p class="updated">Last updated: November 22, 2026</p>
<p class="lead-text">By creating an account or buying from Artivo, you agree to these terms. Please read them carefully.</p>

<h2>1. Accounts</h2>
<ul>
    <li>Provide accurate information when you register and keep your password private.</li>
    <li>You are responsible for activity under your account.</li>
    <li>Accounts that break these terms may be blocked by an administrator.</li>
</ul>

<h2>2. Buying and payment</h2>
<ul>
    <li>All prices are shown in Philippine pesos (₱).</li>
    <li>An order stays Pending until the payment is verified. You pay by QR code, then submit a receipt screenshot, the 13-digit reference number and your mobile number.</li>
    <li>An administrator checks each payment manually and may confirm or reject it. A rejected payment can be submitted again.</li>
    <li>Unpaid orders may be cancelled automatically after a set number of days.</li>
    <li>Artivo does not charge your card or wallet directly; the payment is made in your own payment app.</li>
</ul>

<h2>3. License to use purchased artwork</h2>
<ul>
    <li>When an order is Paid, you receive a license for the license type shown on the order (for example, a personal license).</li>
    <li>You may use the artwork as that license allows, such as personal projects, wallpapers or study references.</li>
    <li>You may not resell, redistribute, share or upload the original file as your own work, and you may not claim to be its creator.</li>
    <li>Copyright stays with the artist. Always credit the artist where reasonable.</li>
</ul>

<h2>4. Refunds</h2>
<p>Because digital files cannot be returned, refunds are decided case by case by an administrator, for example for duplicate payments or damaged files. Contact us with your order code. A refunded order loses its download access.</p>

<h2>5. Artist content</h2>
<ul>
    <li>Artworks and artist photos are added through the administrators on behalf of the artists.</li>
    <li>Only original work, or work you have permission to sell, may be listed. Content that copies someone else’s work will be removed.</li>
    <li>Artwork previews are watermarked and low-resolution. Removing or bypassing the watermark or protection is not allowed.</li>
</ul>

<h2>6. Prohibited use</h2>
<ul>
    <li>Uploading fake receipts or reference numbers.</li>
    <li>Trying to access other users’ orders, files or the admin area.</li>
    <li>Attempting to disrupt, scrape or overload the website.</li>
</ul>

<h2>7. Changes and availability</h2>
<p>These terms and the website may change from time to time. Continued use of Artivo after a change means you accept the updated terms. The service is provided as is, without guarantee of uninterrupted availability.</p>

<h2>8. About this project</h2>
<p>Artivo is a school project created for ITEL 304 at Laguna State Polytechnic University – Siniloan Host Campus. It is intended for learning and demonstration purposes.</p>

@if ($contact = \App\Models\Setting::get('contact_email'))
    <h2>9. Contact</h2>
    <p>Questions about these terms can be sent to <a href="mailto:{{ $contact }}">{{ $contact }}</a>.</p>
@endif
