<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Note;
use App\Models\User;
use App\Services\NoteImageService;
use App\Services\NoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteServiceTest extends TestCase
{
    use RefreshDatabase;

    private NoteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NoteService(new NoteImageService);
    }

    // ── create() ──────────────────────────────────────────────────────────────

    public function test_create_assigns_position_zero_for_first_note(): void
    {
        $user = $this->makeUser();

        $note = $this->service->create($user);

        $this->assertSame(0, $note->position);
    }

    public function test_create_increments_position_for_subsequent_notes(): void
    {
        $user = $this->makeUser();
        $this->service->create($user); // position 0

        $second = $this->service->create($user);

        $this->assertSame(1, $second->position);
    }

    public function test_create_positions_child_independently_from_root(): void
    {
        $user   = $this->makeUser();
        $parent = $this->service->create($user); // root position 0

        $child = $this->service->create($user, $parent->id);

        $this->assertSame($parent->id, $child->parent_id);
        $this->assertSame(0, $child->position); // first child, independent counter
    }

    public function test_create_belongs_to_user(): void
    {
        $user = $this->makeUser();

        $note = $this->service->create($user);

        $this->assertSame($user->id, $note->user_id);
    }

    // ── update() ──────────────────────────────────────────────────────────────

    public function test_update_persists_title_and_content(): void
    {
        $user = $this->makeUser();
        $note = $this->service->create($user);

        $updated = $this->service->update($note, ['title' => 'New title', 'content' => 'Body text']);

        $this->assertSame('New title', $updated->title);
        $this->assertSame('Body text', $updated->content);
    }

    public function test_update_renames_wiki_links_in_other_notes_when_title_changes(): void
    {
        $user  = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $user->id, 'title' => 'Old Title', 'content' => 'Hello']);
        $other = Note::factory()->create(['user_id' => $user->id, 'content' => 'See [[Old Title]] here']);

        $this->service->update($note, ['title' => 'New Title', 'content' => 'Hello']);

        $this->assertSame('See [[New Title]] here', $other->fresh()->content);
    }

    public function test_update_does_not_rename_wiki_links_when_title_unchanged(): void
    {
        $user  = $this->makeUser();
        $note  = Note::factory()->create(['user_id' => $user->id, 'title' => 'My Note', 'content' => 'Body']);
        $other = Note::factory()->create(['user_id' => $user->id, 'content' => 'Link: [[My Note]]']);

        $this->service->update($note, ['title' => 'My Note', 'content' => 'Updated body']);

        $this->assertSame('Link: [[My Note]]', $other->fresh()->content);
    }

    public function test_update_does_not_rename_wiki_links_in_notes_of_other_users(): void
    {
        $owner   = $this->makeUser();
        $other   = $this->makeUser();
        $note    = Note::factory()->create(['user_id' => $owner->id, 'title' => 'Shared', 'content' => 'x']);
        $foreign = Note::factory()->create(['user_id' => $other->id, 'content' => 'See [[Shared]]']);

        $this->service->update($note, ['title' => 'Renamed', 'content' => 'x']);

        $this->assertSame('See [[Shared]]', $foreign->fresh()->content);
    }

    // ── move() ────────────────────────────────────────────────────────────────

    public function test_move_updates_parent_id(): void
    {
        $user   = $this->makeUser();
        $parent = Note::factory()->create(['user_id' => $user->id]);
        $note   = Note::factory()->create(['user_id' => $user->id, 'parent_id' => null]);

        $this->service->move($note, $parent->id);

        $this->assertSame($parent->id, $note->fresh()->parent_id);
    }

    public function test_move_to_null_removes_parent(): void
    {
        $user   = $this->makeUser();
        $parent = Note::factory()->create(['user_id' => $user->id]);
        $note   = Note::factory()->create(['user_id' => $user->id, 'parent_id' => $parent->id]);

        $this->service->move($note, null);

        $this->assertNull($note->fresh()->parent_id);
    }

    // ── reorder() ─────────────────────────────────────────────────────────────

    public function test_reorder_updates_positions(): void
    {
        $user  = $this->makeUser();
        $noteA = Note::factory()->create(['user_id' => $user->id, 'position' => 0]);
        $noteB = Note::factory()->create(['user_id' => $user->id, 'position' => 1]);

        $this->service->reorder($user, [$noteB->id, $noteA->id]); // swap

        $this->assertSame(0, $noteB->fresh()->position);
        $this->assertSame(1, $noteA->fresh()->position);
    }

    // ── backlinks() ───────────────────────────────────────────────────────────

    public function test_backlinks_returns_empty_when_note_has_no_title(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id, 'title' => null]);

        $this->assertSame([], $this->service->backlinks($user, $note));
    }

    public function test_backlinks_finds_notes_with_wiki_link(): void
    {
        $user   = $this->makeUser();
        $target = Note::factory()->create(['user_id' => $user->id, 'title' => 'Target']);
        $source = Note::factory()->create(['user_id' => $user->id, 'content' => 'See [[Target]] here']);

        $links = $this->service->backlinks($user, $target);

        $this->assertCount(1, $links);
        $this->assertSame($source->id, $links[0]['id']);
    }

    public function test_backlinks_is_case_insensitive(): void
    {
        $user   = $this->makeUser();
        $target = Note::factory()->create(['user_id' => $user->id, 'title' => 'My Note']);
        Note::factory()->create(['user_id' => $user->id, 'content' => 'Link [[my note]] exists']);

        $links = $this->service->backlinks($user, $target);

        $this->assertCount(1, $links);
    }

    public function test_backlinks_excludes_the_note_itself(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id, 'title' => 'Self', 'content' => 'See [[Self]]']);

        $this->assertSame([], $this->service->backlinks($user, $note));
    }

    public function test_backlinks_excludes_notes_of_other_users(): void
    {
        $owner   = $this->makeUser();
        $other   = $this->makeUser();
        $target  = Note::factory()->create(['user_id' => $owner->id, 'title' => 'Secret']);
        Note::factory()->create(['user_id' => $other->id, 'content' => '[[Secret]] link']);

        $this->assertSame([], $this->service->backlinks($owner, $target));
    }

    // ── unlinkedMentions() ────────────────────────────────────────────────────

    public function test_unlinked_mentions_returns_empty_when_no_title(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id, 'title' => null]);

        $this->assertSame([], $this->service->unlinkedMentions($user, $note));
    }

    public function test_unlinked_mentions_finds_plain_text_references(): void
    {
        $user   = $this->makeUser();
        $target = Note::factory()->create(['user_id' => $user->id, 'title' => 'Onyx']);
        $source = Note::factory()->create(['user_id' => $user->id, 'content' => 'Onyx is great']);

        $mentions = $this->service->unlinkedMentions($user, $target);

        $this->assertCount(1, $mentions);
        $this->assertSame($source->id, $mentions[0]['id']);
    }

    public function test_unlinked_mentions_excludes_notes_with_wiki_link(): void
    {
        $user   = $this->makeUser();
        $target = Note::factory()->create(['user_id' => $user->id, 'title' => 'Onyx']);
        Note::factory()->create(['user_id' => $user->id, 'content' => '[[Onyx]] is already linked']);

        $this->assertSame([], $this->service->unlinkedMentions($user, $target));
    }

    // ── graph() ───────────────────────────────────────────────────────────────

    public function test_graph_returns_nodes_for_all_user_notes(): void
    {
        $user = $this->makeUser();
        Note::factory()->count(3)->create(['user_id' => $user->id]);

        $graph = $this->service->graph($user);

        $this->assertCount(3, $graph['nodes']);
    }

    public function test_graph_returns_edges_for_wiki_links(): void
    {
        $user  = $this->makeUser();
        $noteA = Note::factory()->create(['user_id' => $user->id, 'title' => 'Alpha']);
        $noteB = Note::factory()->create(['user_id' => $user->id, 'title' => 'Beta', 'content' => '[[Alpha]]']);

        $graph = $this->service->graph($user);

        $this->assertCount(1, $graph['edges']);
        $this->assertSame($noteB->id, $graph['edges'][0]['source']);
        $this->assertSame($noteA->id, $graph['edges'][0]['target']);
    }

    public function test_graph_excludes_self_links(): void
    {
        $user = $this->makeUser();
        Note::factory()->create(['user_id' => $user->id, 'title' => 'Loop', 'content' => '[[Loop]]']);

        $graph = $this->service->graph($user);

        $this->assertCount(0, $graph['edges']);
    }

    public function test_graph_excludes_links_to_unknown_notes(): void
    {
        $user = $this->makeUser();
        Note::factory()->create(['user_id' => $user->id, 'title' => 'Orphan', 'content' => '[[NonExistent]]']);

        $graph = $this->service->graph($user);

        $this->assertCount(0, $graph['edges']);
    }

    // ── delete() ──────────────────────────────────────────────────────────────

    public function test_delete_removes_the_note(): void
    {
        $user = $this->makeUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $this->service->delete($note);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
