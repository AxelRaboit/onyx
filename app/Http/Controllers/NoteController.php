<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PolicyAction;
use App\Http\Requests\NoteRequest;
use App\Http\Requests\ReorderRequest;
use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    public function __construct(
        private readonly NoteService $noteService,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Notes/Index', [
            'notes' => $this->noteService->list($request->user()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $parentId = $request->input('parent_id') ? (int) $request->input('parent_id') : null;
        $note = $this->noteService->create($request->user(), $parentId);

        return response()->json($note);
    }

    public function show(Note $note, Request $request): JsonResponse
    {
        $this->authorize(PolicyAction::View->value, $note);

        return response()->json($note);
    }

    public function update(NoteRequest $request, Note $note): JsonResponse
    {
        $this->authorize(PolicyAction::Update->value, $note);
        $updated = $this->noteService->update($note, $request->validated());

        return response()->json($updated);
    }

    public function move(Request $request, Note $note): JsonResponse
    {
        $this->authorize(PolicyAction::Update->value, $note);
        $parentId = $request->input('parent_id') ? (int) $request->input('parent_id') : null;
        $this->noteService->move($note, $parentId);

        return response()->json(['ok' => true]);
    }

    public function reorder(ReorderRequest $request): JsonResponse
    {
        $this->noteService->reorder($request->user(), $request->validated()['ids']);

        return response()->json(['ok' => true]);
    }

    public function backlinks(Request $request, Note $note): JsonResponse
    {
        $this->authorize(PolicyAction::View->value, $note);

        return response()->json($this->noteService->backlinks($request->user(), $note));
    }

    public function graph(Request $request): JsonResponse
    {
        return response()->json($this->noteService->graph($request->user()));
    }

    public function unlinkedMentions(Request $request, Note $note): JsonResponse
    {
        $this->authorize(PolicyAction::View->value, $note);

        return response()->json($this->noteService->unlinkedMentions($request->user(), $note));
    }

    public function destroy(Note $note): JsonResponse
    {
        $this->authorize(PolicyAction::Delete->value, $note);
        $this->noteService->delete($note);

        return response()->json(['ok' => true]);
    }
}
