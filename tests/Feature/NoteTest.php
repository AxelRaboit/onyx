<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    // ── Auth guard ────────────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_access_notes(): void
    {
        $this->get('/notes')->assertRedirect('/login');
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_notes_index_renders_for_authenticated_user(): void
    {
        $this->actingAs($this->makeUser())
            ->get('/notes')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Notes/Index'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function test_can_create_a_root_note(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->postJson('/notes');

        $response->assertOk()->assertJsonStructure(['id', 'user_id', 'position']);
        $this->assertDatabaseHas('notes', ['user_id' => $user->id, 'parent_id' => null]);
    }

    public function test_can_create_a_child_note(): void
    {
        $user   = $this->makeUser();
        $parent = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson('/notes', ['parent_id' => $parent->id]);

        $response->assertOk();
        $this->assertDatabaseHas('notes', ['user_id' => $user->id, 'parent_id' => $parent->id]);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_owner_can_view_note(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id, 'title' => 'My note']);

        $this->actingAs($user)
            ->getJson("/notes/{$note->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $note->id]);
    }

    public function test_non_owner_cannot_view_note(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->getJson("/notes/{$note->id}")
            ->assertForbidden();
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function test_owner_can_update_note(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->putJson("/notes/{$note->id}", ['title' => 'Updated', 'content' => 'New body', 'tags' => []])
            ->assertOk()
            ->assertJsonFragment(['title' => 'Updated']);
    }

    public function test_non_owner_cannot_update_note(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->putJson("/notes/{$note->id}", ['title' => 'Hacked', 'content' => '', 'tags' => []])
            ->assertForbidden();
    }

    public function test_update_validates_tags_must_be_array(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->putJson("/notes/{$note->id}", ['title' => 'T', 'content' => 'C', 'tags' => 'not-an-array'])
            ->assertUnprocessable();
    }

    public function test_update_accepts_all_nullable_fields(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // All fields are nullable — autosave can send partial data
        $this->actingAs($user)
            ->putJson("/notes/{$note->id}", ['title' => null, 'content' => null, 'tags' => null])
            ->assertOk();
    }

    // ── Move ──────────────────────────────────────────────────────────────────

    public function test_owner_can_move_note_to_new_parent(): void
    {
        $user   = $this->makeUser();
        $note   = Note::factory()->create(['user_id' => $user->id]);
        $parent = Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patchJson("/notes/{$note->id}/move", ['parent_id' => $parent->id])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame($parent->id, $note->fresh()->parent_id);
    }

    public function test_non_owner_cannot_move_note(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->patchJson("/notes/{$note->id}/move", ['parent_id' => null])
            ->assertForbidden();
    }

    // ── Reorder ───────────────────────────────────────────────────────────────

    public function test_can_reorder_own_notes(): void
    {
        $user  = $this->makeUser();
        $noteA = Note::factory()->create(['user_id' => $user->id, 'position' => 0]);
        $noteB = Note::factory()->create(['user_id' => $user->id, 'position' => 1]);

        $this->actingAs($user)
            ->patchJson('/notes/reorder', ['ids' => [$noteB->id, $noteA->id]])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(0, $noteB->fresh()->position);
        $this->assertSame(1, $noteA->fresh()->position);
    }

    // ── Backlinks ─────────────────────────────────────────────────────────────

    public function test_owner_can_get_backlinks(): void
    {
        $user   = $this->makeUser();
        $target = Note::factory()->create(['user_id' => $user->id, 'title' => 'Target']);
        Note::factory()->create(['user_id' => $user->id, 'content' => 'See [[Target]] here']);

        $this->actingAs($user)
            ->getJson("/notes/{$target->id}/backlinks")
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_non_owner_cannot_get_backlinks(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $owner->id, 'title' => 'Private']);

        $this->actingAs($other)
            ->getJson("/notes/{$note->id}/backlinks")
            ->assertForbidden();
    }

    // ── Unlinked mentions ─────────────────────────────────────────────────────

    public function test_owner_can_get_unlinked_mentions(): void
    {
        $user   = $this->makeUser();
        $target = Note::factory()->create(['user_id' => $user->id, 'title' => 'Onyx']);
        Note::factory()->create(['user_id' => $user->id, 'content' => 'Onyx is cool']);

        $this->actingAs($user)
            ->getJson("/notes/{$target->id}/unlinked-mentions")
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_non_owner_cannot_get_unlinked_mentions(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $owner->id, 'title' => 'Private']);

        $this->actingAs($other)
            ->getJson("/notes/{$note->id}/unlinked-mentions")
            ->assertForbidden();
    }

    // ── Graph ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_get_graph(): void
    {
        $user = $this->makeUser();
        Note::factory()->count(2)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->getJson('/notes/graph')
            ->assertOk()
            ->assertJsonStructure(['nodes', 'edges']);
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function test_owner_can_delete_note(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson("/notes/{$note->id}")
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_non_owner_cannot_delete_note(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->deleteJson("/notes/{$note->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('notes', ['id' => $note->id]);
    }
}
