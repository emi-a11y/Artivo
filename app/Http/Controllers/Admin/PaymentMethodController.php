<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** CRUD for the payment methods (each with its own QR code image). */
class PaymentMethodController extends Controller
{
    public function index()
    {
        return view('admin.payment-methods', ['methods' => PaymentMethod::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        $data['qr_path'] = $request->file('qr')->store('payment-qr', 'public');
        $data['is_active'] = $request->boolean('is_active');
        unset($data['qr']);

        $method = PaymentMethod::create($data);

        return redirect()->route('admin.methods')->with('status', "{$method->name} was added.");
    }

    public function edit(PaymentMethod $method)
    {
        return view('admin.payment-method-edit', ['method' => $method]);
    }

    public function update(Request $request, PaymentMethod $method)
    {
        $data = $this->validated($request, $method);
        if ($request->hasFile('qr')) {
            Storage::disk('public')->delete($method->qr_path);
            $data['qr_path'] = $request->file('qr')->store('payment-qr', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');
        unset($data['qr']);

        $method->update($data);

        return redirect()->route('admin.methods')->with('status', "{$method->name} was updated.");
    }

    public function destroy(PaymentMethod $method)
    {
        Storage::disk('public')->delete($method->qr_path);
        $method->delete();

        return redirect()->route('admin.methods')->with('status', "{$method->name} was deleted.");
    }

    private function validated(Request $request, ?PaymentMethod $method): array
    {
        // Accept 0917 123 4567, 0917-123-4567 or +63 917 123 4567 and keep only the 11 digits (09171234567).
        $number = preg_replace('/[\s\-]/', '', (string) $request->input('account_number'));
        if (preg_match('/^\+?63(9\d{9})$/', $number, $m)) {
            $number = '0' . $m[1];
        }
        $request->merge(['account_number' => $number === '' ? null : $number]);

        return $request->validate([
            'name' => ['required', 'string', 'max:40', Rule::unique('payment_methods', 'name')->ignore($method?->id)],
            'account_name' => ['nullable', 'string', 'max:80'],
            'account_number' => ['nullable', 'regex:/^09\d{9}$/'],
            'instructions' => ['nullable', 'string', 'max:500'],
            'qr' => [$method ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'name.required' => 'Please enter a name, for example GCash.',
            'name.unique' => 'A payment method with that name already exists.',
            'account_number.regex' => 'Enter an 11-digit mobile number that starts with 09, for example 09171234567.',
            'qr.required' => 'Please upload the QR code image.',
            'qr.image' => 'The QR code must be an image file.',
            'qr.mimes' => 'The QR code must be a JPG, PNG or WEBP image.',
            'qr.max' => 'The QR image must be 4 MB or smaller.',
        ]);
    }
}
