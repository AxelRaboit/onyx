<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ApplicationParameter;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DevDashboardTest extends TestCase
{
    use RefreshDatabase;

    // ── Access control ────────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_access_dev_dashboard(): void
    {
        $this->get('/dev/dashboard')->assertRedirect('/login');
    }

    public function test_regular_user_without_role_dev_is_forbidden(): void
    {
        $this->createRoles();
        $user = $this->makeUser();
        $user->assignRole('ROLE_USER');

        $this->actingAs($user)->get('/dev/dashboard')->assertForbidden();
    }

    public function test_dev_user_can_access_stats(): void
    {
        $this->actingAs($this->makeDevUser())
            ->get('/dev/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Dev/Dashboard')
                ->has('stats')
                ->where('tab', 'stats')
            );
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    public function test_stats_include_user_and_note_counts(): void
    {
        $dev = $this->makeDevUser();
        User::factory()->count(2)->create();
        Note::factory()->count(4)->create(['user_id' => $dev->id]);

        $this->actingAs($dev)
            ->get('/dev/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('stats.notes', 4)
                ->where('stats.users.total', 3) // dev + 2 created
            );
    }

    // ── Users list ────────────────────────────────────────────────────────────

    public function test_dev_user_can_list_users(): void
    {
        $dev = $this->makeDevUser();
        User::factory()->count(3)->create();

        $this->actingAs($dev)
            ->get('/dev/dashboard/users')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Dev/Dashboard')
                ->where('tab', 'users')
                ->has('users.data')
            );
    }

    public function test_users_list_supports_search(): void
    {
        $dev = $this->makeDevUser();
        User::factory()->create(['name' => 'Alice Smith', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob Jones',  'email' => 'bob@example.com']);

        $this->actingAs($dev)
            ->get('/dev/dashboard/users?search=alice')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('search', 'alice')
                ->has('users.data', 1)
            );
    }

    // ── Toggle role ───────────────────────────────────────────────────────────

    public function test_dev_can_assign_role_dev_to_user(): void
    {
        $this->createRoles();
        $dev    = $this->makeDevUser();
        $target = $this->makeUser();

        $this->actingAs($dev)
            ->post("/dev/dashboard/users/{$target->id}/toggle-role")
            ->assertRedirect();

        $this->assertTrue($target->fresh()->hasRole('ROLE_DEV'));
    }

    public function test_dev_can_remove_role_dev_from_user(): void
    {
        $this->createRoles();
        $dev    = $this->makeDevUser();
        $target = $this->makeUser();
        $target->assignRole('ROLE_DEV');

        $this->actingAs($dev)
            ->post("/dev/dashboard/users/{$target->id}/toggle-role")
            ->assertRedirect();

        $this->assertFalse($target->fresh()->hasRole('ROLE_DEV'));
    }

    // ── Delete user ───────────────────────────────────────────────────────────

    public function test_dev_can_delete_another_user(): void
    {
        $dev    = $this->makeDevUser();
        $target = $this->makeUser();

        $this->actingAs($dev)
            ->delete("/dev/dashboard/users/{$target->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_dev_cannot_delete_own_account(): void
    {
        $dev = $this->makeDevUser();

        $this->actingAs($dev)
            ->delete("/dev/dashboard/users/{$dev->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $dev->id]);
    }

    // ── Invitations ───────────────────────────────────────────────────────────

    public function test_dev_can_view_invitations_tab(): void
    {
        $this->actingAs($this->makeDevUser())
            ->get('/dev/dashboard/invitations')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('tab', 'invitations'));
    }

    public function test_dev_can_send_invitation(): void
    {
        Notification::fake();

        $this->actingAs($this->makeDevUser())
            ->post('/dev/dashboard/invitations', [
                'email'   => 'invite@example.com',
                'message' => 'Welcome to Onyx!',
            ])
            ->assertRedirect();

        Notification::assertSentOnDemand(
            \App\Notifications\AppInvitationNotification::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'invite@example.com'
        );
    }

    public function test_invitation_requires_email_and_message(): void
    {
        $this->actingAs($this->makeDevUser())
            ->post('/dev/dashboard/invitations', [])
            ->assertSessionHasErrors(['email', 'message']);
    }

    public function test_invitation_rejects_invalid_email(): void
    {
        $this->actingAs($this->makeDevUser())
            ->post('/dev/dashboard/invitations', [
                'email'   => 'not-an-email',
                'message' => 'Hello',
            ])
            ->assertSessionHasErrors('email');
    }

    // ── Parameters ────────────────────────────────────────────────────────────

    public function test_dev_can_view_parameters_tab(): void
    {
        $this->actingAs($this->makeDevUser())
            ->get('/dev/dashboard/parameters')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('tab', 'parameters'));
    }

    public function test_dev_can_update_a_parameter(): void
    {
        $dev = $this->makeDevUser();
        ApplicationParameter::create(['key' => 'registration_enabled', 'value' => '1']);

        $this->actingAs($dev)
            ->patchJson('/dev/dashboard/parameters/registration_enabled', ['value' => '0'])
            ->assertOk()
            ->assertJson(['key' => 'registration_enabled', 'value' => '0']);

        $this->assertDatabaseHas('application_parameters', [
            'key'   => 'registration_enabled',
            'value' => '0',
        ]);
    }

    public function test_update_parameter_creates_it_if_absent(): void
    {
        $this->actingAs($this->makeDevUser())
            ->patchJson('/dev/dashboard/parameters/new_param', ['value' => 'hello'])
            ->assertOk();

        $this->assertDatabaseHas('application_parameters', ['key' => 'new_param', 'value' => 'hello']);
    }

    public function test_regular_user_cannot_access_dev_routes(): void
    {
        $this->createRoles();
        $user = $this->makeUser();

        foreach ([
            ['GET',    '/dev/dashboard'],
            ['GET',    '/dev/dashboard/users'],
            ['GET',    '/dev/dashboard/invitations'],
            ['GET',    '/dev/dashboard/parameters'],
        ] as [$method, $uri]) {
            $this->actingAs($user)
                ->call($method, $uri)
                ->assertForbidden();
        }
    }
}
