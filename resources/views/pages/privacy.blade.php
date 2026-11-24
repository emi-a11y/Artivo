<p class="updated">Last updated: November 22, 2026</p>
<p class="lead-text">This policy explains what information Artivo collects, why it is needed and how it is protected.</p>

<h2>Information we collect</h2>
<ul>
    <li><strong>Account details:</strong> your name, email address and password. Passwords are stored in a scrambled (hashed) form and cannot be read by anyone. If you sign in with Google, we receive your name and email from Google.</li>
    <li><strong>Order details:</strong> the artworks you buy, licenses, totals and order status.</li>
    <li><strong>Payment details:</strong> the receipt screenshot you upload, the 13-digit reference number and the mobile number you enter. We never ask for card numbers or wallet passwords.</li>
    <li><strong>Wishlist and activity:</strong> artworks you save and basic information needed to keep you logged in.</li>
    <li><strong>Artist profiles:</strong> artist names and photos shown on the Artists page, provided with the artist’s consent.</li>
</ul>

<h2>How we use it</h2>
<ul>
    <li>To create and secure your account, including limiting repeated failed logins.</li>
    <li>To process orders, verify payments and unlock your downloads.</li>
    <li>To send account emails, such as a welcome message or a password reset link.</li>
    <li>To keep the shop running and fix problems.</li>
</ul>
<p>We do not sell your information or use it for advertising.</p>

<h2>Who can see it</h2>
<ul>
    <li>Administrators can see orders, receipts, reference numbers and mobile numbers because they are needed to verify payments.</li>
    <li>Other buyers cannot see your orders or files.</li>
    <li>Your information is shared outside Artivo only when the law requires it, or when you sign in with a service such as Google.</li>
</ul>

<h2>Cookies and sessions</h2>
<p>Artivo uses a session cookie to keep you logged in and to protect forms from forgery. The “Remember me” option keeps you logged in longer on the same device. We do not use advertising cookies.</p>

<h2>How we protect your data</h2>
<ul>
    <li>Passwords are hashed and logins are rate-limited.</li>
    <li>Original artwork files are stored privately and are delivered only to the buyer of a paid order.</li>
    <li>Administrator pages require an administrator account.</li>
</ul>
<p>No website can promise perfect security, so please use a strong, unique password.</p>

<h2>Keeping and deleting data</h2>
<p>Order records are kept so that purchases, downloads and refunds can be checked. You can ask to correct or delete your account information by contacting us.</p>

<h2>About this project</h2>
<p>Artivo is a school project made for learning purposes. Please do not upload sensitive personal information beyond what the checkout asks for.</p>

@if ($contact = \App\Models\Setting::get('contact_email'))
    <h2>Contact</h2>
    <p>For privacy questions, email <a href="mailto:{{ $contact }}">{{ $contact }}</a>.</p>
@endif
