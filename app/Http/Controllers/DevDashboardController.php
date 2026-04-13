<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DevDashboardController extends Controller
{
    public function stats(): Response
    {
        return Inertia::render('Dev/Dashboard', [
            'tab' => 'stats',
            'stats' => [
                'users' => User::count(),
            ],
        ]);
    }

    public function users(Request $request): Response
    {
        $search = $request->query('search', '');

        $users = User::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Dev/Dashboard', [
            'tab' => 'users',
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function toggleRole(User $user): RedirectResponse
    {
        if ($user->hasRole('ROLE_DEV')) {
            $user->removeRole('ROLE_DEV');
        } else {
            $user->assignRole('ROLE_DEV');
        }

        return back();
    }

    public function destroyUser(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Cannot delete your own account.');

        $user->delete();

        return back();
    }
}
