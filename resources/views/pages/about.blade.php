<p class="lead-text">Artivo is a digital art marketplace where independent artists share original illustrations, photography, pixel art and vector assets, and where buyers can find, purchase and download them in one place.</p>

<h2>Why Artivo exists</h2>
<p>Many Filipino artists still sell their work through scattered social media posts and private messages. Payments are hard to verify, files are easy to copy, and buyers are never sure when the file will arrive. Artivo brings the whole process into one organized website: a catalog to browse, a clear checkout, a verified payment, and a download that unlocks once the payment is confirmed.</p>

<h2>What makes it different</h2>
<div class="row g-3">
    <div class="col-md-4"><div class="info-card"><h3>Protected artwork</h3><p>Previews shown on the site are small and watermarked. The original file is kept private and is released only to the buyer after payment.</p></div></div>
    <div class="col-md-4"><div class="info-card"><h3>Simple local payment</h3><p>Pay by scanning a QR code with your e-wallet, then upload the receipt. Prices are shown in Philippine pesos (₱).</p></div></div>
    <div class="col-md-4"><div class="info-card"><h3>Made for creators</h3><p>Every artwork is credited to its artist, who also has a profile on the Artists page.</p></div></div>
</div>

<h2>How it works</h2>
<div class="row g-3">
    <div class="col-md-3"><div class="info-card"><span class="info-step">1</span><h3>Browse</h3><p>Filter by category, genre or keyword.</p></div></div>
    <div class="col-md-3"><div class="info-card"><span class="info-step">2</span><h3>Check out</h3><p>Add artworks to the cart and place the order.</p></div></div>
    <div class="col-md-3"><div class="info-card"><span class="info-step">3</span><h3>Pay</h3><p>Scan the QR code and upload your receipt.</p></div></div>
    <div class="col-md-3"><div class="info-card"><span class="info-step">4</span><h3>Download</h3><p>Once confirmed, the download button unlocks.</p></div></div>
</div>

<h2>About the project</h2>
<p>Artivo was built by a student group as a final project for ITEL 304 (Integrative Programming Technologies 2), BSIT, College of Computer Studies, Laguna State Polytechnic University – Siniloan Host Campus. It is made with Laravel, MySQL and Bootstrap 5.</p>

@if ($contact = \App\Models\Setting::get('contact_email'))
    <h2>Get in touch</h2>
    <p>Questions or feedback? Email us at <a href="mailto:{{ $contact }}">{{ $contact }}</a>.</p>
@endif
