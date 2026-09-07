<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Read a setting by key, e.g. SiteSetting::get('contact_email').
     */
    public static function get(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Create or update a setting by key, e.g. SiteSetting::set('site_name', 'Naksha').
     */
    public static function set(string $key, $value): self
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
