<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

final class CatalogsCache
{
    public const KEY = 'frontend.catalogs.v2';

    public static function remember(Closure $builder): array
    {
        return Cache::remember(self::KEY, now()->addHour(), $builder);
    }

    public static function forget(): void
    {
        Cache::forget(self::KEY);
    }
}
