<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\HttpStatus;
use App\Http\Requests\SendAppInvitationRequest;
use App\Models\Note;
use App\Models\User;
use App\Services\AppInvitationService;
use App\Services\ApplicationParameterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DevDashboardController extends Controller
{
    public function __construct(
        private readonly AppInvitationService $invitationService,
        private readonly ApplicationParameterService $applicationParameterService,
    ) {}

    public function stats(): Response
    {
        $now = now();

        return Inertia::render('Dev/Dashboard', [
            'tab' => 'stats',
            'stats' => [
                'users' => [
                    'total' => User::count(),
                    'newThisMonth' => User::whereYear('created_at', $now->year)
                        ->whereMonth('created_at', $now->month)
                        ->count(),
                ],
                'notes' => Note::count(),
            ],
        ]);
    }

    public function users(Request $request): Response
    {
        $search = $request->query('search', '');

        $users = User::with('roles')
            ->when($search, fn ($q) => $q->where('name', 'like', sprintf('%%%s%%', $search))->orWhere('email', 'like', sprintf('%%%s%%', $search)))
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
            $user->assignRole('ROLE_USER');
        } else {
            $user->removeRole('ROLE_USER');
            $user->assignRole('ROLE_DEV');
        }

        return back();
    }

    public function destroyUser(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), HttpStatus::Forbidden->value, 'Cannot delete your own account.');

        $user->delete();

        return back();
    }

    public function invitations(): Response
    {
        return Inertia::render('Dev/Dashboard', [
            'tab' => 'invitations',
        ]);
    }

    public function sendInvitation(SendAppInvitationRequest $request): RedirectResponse
    {
        $this->invitationService->send(
            $request->input('email'),
            $request->input('message'),
            $request->input('credential_email'),
            $request->input('credential_password'),
        );

        return back()->with('success', __('admin.invitations.sent', ['email' => $request->input('email')]));
    }

    public function parameters(): Response
    {
        return Inertia::render('Dev/Dashboard', [
            'tab' => 'parameters',
            'parameters' => $this->applicationParameterService->all(),
            'parameterUpdatePath' => route('dev.dashboard.parameters.update', ['key' => '__key__']),
        ]);
    }

    public function updateParameter(string $key, Request $request): JsonResponse
    {
        $value = $request->input('value');
        $this->applicationParameterService->set($key, $value !== null ? (string) $value : null);

        return response()->json(['key' => $key, 'value' => $value]);
    }
}
