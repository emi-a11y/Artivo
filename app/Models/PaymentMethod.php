<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'account_name', 'account_number', 'qr_path', 'instructions', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** 09171234567 shown as 0917 123 4567. */
    public function getAccountNumberDisplayAttribute(): ?string
    {
        return $this->account_number
            ? preg_replace('/^(\d{4})(\d{3})(\d{4})$/', '$1 $2 $3', $this->account_number)
            : null;
    }

    public function getQrUrlAttribute(): string
    {
        return asset('storage/' . $this->qr_path);
    }
}
