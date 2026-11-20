<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Simple key/value store for the admin Settings page. */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $value = static::where('key', $key)->value('value');

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
