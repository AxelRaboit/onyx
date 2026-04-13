<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $roleName): HttpResponse
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $implied = $roleName === 'ROLE_USER' && $user->hasRole('ROLE_DEV');

        if (! $implied && ! $user->hasRole($roleName)) {
            abort(HttpResponse::HTTP_FORBIDDEN, 'Unauthorized - insufficient role');
        }

        return $next($request);
    }
}
