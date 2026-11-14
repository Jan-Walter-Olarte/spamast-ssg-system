<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['name', 'value'])]
class Setting extends Model
{
    // Used until the Admin saves something different.
    public const DEFAULT_SCHOOL_NAME = 'SPAMAST Supreme Student Government';
    public const DEFAULT_FINE        = '50.00';

    /**
     * One setting by name. All settings are read once and kept in the cache
     * as a plain array of name => value, so a page costs no extra query.
     */
    public static function get(string $name, ?string $default = null): ?string
    {
        $all = Cache::remember('settings.all', now()->addHour(), function () {
            return static::pluck('value', 'name')->all();
        });

        return $all[$name] ?? $default;
    }

    /**
     * Save one setting, and forget the cached copy so the next page sees it.
     */
    public static function put(string $name, ?string $value): void
    {
        static::updateOrCreate(['name' => $name], ['value' => $value]);

        Cache::forget('settings.all');
    }

    // The school name, or the built-in default when none is saved (or it is empty).
    public static function schoolName(): string
    {
        return static::get('school_name') ?: self::DEFAULT_SCHOOL_NAME;
    }

    // The default fine as text with two decimals, such as "50.00".
    public static function defaultFine(): string
    {
        return static::get('default_fine') ?? self::DEFAULT_FINE;
    }
}
