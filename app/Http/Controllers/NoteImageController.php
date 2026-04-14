<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\HttpStatus;
use App\Http\Requests\NoteImageRequest;
use App\Services\NoteImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NoteImageController extends Controller
{
    public function __construct(
        private readonly NoteImageService $imageService,
    ) {}

    public function upload(NoteImageRequest $request): JsonResponse
    {
        $filename = $this->imageService->store($request->file('image'));

        return response()->json([
            'url' => route('notes.images.serve', ['filename' => $filename]),
        ]);
    }

    public function serve(string $filename): Response
    {
        abort_unless($this->imageService->exists($filename), HttpStatus::NotFound->value);

        $path = $this->imageService->path($filename);

        return response(file_get_contents($path), HttpStatus::Ok->value, [
            'Content-Type' => mime_content_type($path) ?: 'application/octet-stream',
            'Cache-Control' => 'private, max-age=31536000',
        ]);
    }
}
