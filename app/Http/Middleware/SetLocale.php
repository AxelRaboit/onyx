<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['en', 'fr', 'de', 'es'];
    private const DEFAULT_LOCALE = 'en';

    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();
        $locale = $user?->locale ?? config('app.fallback_locale', self::DEFAULT_LOCALE);

        App::setLocale(in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : self::DEFAULT_LOCALE);

        return $next($request);
    }
}
