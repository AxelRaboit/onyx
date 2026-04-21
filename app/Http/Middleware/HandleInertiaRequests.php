<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\ImpersonationService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user()?->load('roles');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'locale' => $user !== null ? $user->locale : config('app.fallback_locale', 'en'),
            'appVersion' => file_exists(base_path('VERSION')) ? trim(file_get_contents(base_path('VERSION'))) : 'dev',
            'impersonating' => app(ImpersonationService::class)->isImpersonating(),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ];
    }
}
