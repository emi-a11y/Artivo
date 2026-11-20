<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function show(Request $request)
    {
        return view('admin.settings', [
            'user' => $request->user(),
            'store' => [
                'site_name' => Setting::get('site_name', 'Artivo'),
                'contact_email' => Setting::get('contact_email', ''),
                'auto_cancel_days' => (int) Setting::get('auto_cancel_days', 3),
            ],
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required' => 'Your name is required.',
            'email.required' => 'Your email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'That email is already used by another account.',
        ]);

        $user->update(['name' => trim($data['name']), 'email' => strtolower($data['email'])]);

        return redirect()->route('admin.settings')->with('status', 'Profile saved.');
    }

    public function password(Request $request)
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'regex:/[A-Za-z]/', 'regex:/\d/', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Enter your current password.',
            'current_password.current_password' => 'The current password is not correct.',
            'password.required' => 'Enter a new password.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password.regex' => 'The new password needs at least one letter and one number.',
            'password.confirmed' => 'The two new passwords do not match.',
            'password.different' => 'The new password must be different from the current one.',
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('admin.settings')->with('status', 'Password changed.');
    }

    public function store(Request $request)
    {
        $data = $request->validateWithBag('store', [
            'site_name' => ['required', 'string', 'max:60'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'auto_cancel_days' => ['required', 'integer', 'between:1,30'],
        ], [
            'site_name.required' => 'The store name is required.',
            'contact_email.email' => 'Enter a valid contact email.',
            'auto_cancel_days.between' => 'Auto-cancel must be between 1 and 30 days.',
            'auto_cancel_days.integer' => 'Auto-cancel must be a whole number of days.',
        ]);

        Setting::put('site_name', trim($data['site_name']));
        Setting::put('contact_email', $data['contact_email'] ?? '');
        Setting::put('auto_cancel_days', (int) $data['auto_cancel_days']);

        return redirect()->route('admin.settings')->with('status', 'Store settings saved.');
    }
}
