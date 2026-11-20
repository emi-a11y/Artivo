<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $method?->name) }}" maxlength="40" placeholder="GCash">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="account_name" class="form-label">Account name <span class="text-muted">(optional)</span></label>
        <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" value="{{ old('account_name', $method?->account_name) }}" maxlength="80">
        @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="account_number" class="form-label">Mobile number <span class="text-muted">(optional)</span></label>
        <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number', $method?->account_number) }}" inputmode="numeric" placeholder="09171234567" autocomplete="off">
        @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">11 digits, starting with 09.</div>
    </div>
    <div class="col-12">
        <label for="instructions" class="form-label">Payment steps <span class="text-muted">(optional)</span></label>
        <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="2" maxlength="500" placeholder="Example: Scan the QR code, send the exact total, then keep the receipt.">{{ old('instructions', $method?->instructions) }}</textarea>
        @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label for="qr" class="form-label">QR code image {{ $method ? '(leave empty to keep the current one)' : '' }}</label>
        <input type="file" class="form-control @error('qr') is-invalid @enderror" id="qr" name="qr" accept="image/png,image/jpeg,image/webp">
        @error('qr')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Choose the QR image from your computer (JPG, PNG or WEBP, up to 4 MB).</div>
    </div>
    <div class="col-md-4 d-flex align-items-end gap-3">
        @if ($method)
            <img src="{{ $method->qr_url }}" alt="Current QR code" id="qrPreview" width="96" height="96" style="object-fit: contain;" class="border rounded bg-white">
        @else
            <img alt="Preview of the selected QR code" id="qrPreview" width="96" height="96" style="object-fit: contain;" class="border rounded bg-white" hidden>
        @endif
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $method?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Show to buyers</label>
        </div>
    </div>
</div>
<script>
    (function () {
        var num = document.getElementById('account_number');
        if (num) {
            num.addEventListener('input', function () { num.value = num.value.replace(/\D/g, '').slice(0, 11); });
        }
        var input = document.getElementById('qr'), img = document.getElementById('qrPreview');
        if (!input || !img) return;
        input.addEventListener('change', function () {
            var f = input.files && input.files[0];
            if (f) { img.src = URL.createObjectURL(f); img.hidden = false; }
        });
    })();
</script>
