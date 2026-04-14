<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class NoteImageService
{
    private const int   MAX_DIMENSION = 1920;

    private const int   QUALITY = 80;

    private const string FORMAT = 'webp';

    public function store(UploadedFile $file): string
    {
        $name = sprintf('%s.%s', str_replace('.', '', uniqid('', true)), self::FORMAT);
        $path = $this->path($name);

        $manager = new ImageManager(new Driver);
        $image = $manager->decode($file->getRealPath());

        if ($image->width() > self::MAX_DIMENSION || $image->height() > self::MAX_DIMENSION) {
            $image->scaleDown(self::MAX_DIMENSION, self::MAX_DIMENSION);
        }

        $image->encode(new WebpEncoder(quality: self::QUALITY))->save($path);

        return $name;
    }

    public function path(string $filename): string
    {
        return sprintf('%s/%s', $this->directory(), basename($filename));
    }

    public function exists(string $filename): bool
    {
        return file_exists($this->path($filename));
    }

    private function directory(): string
    {
        return storage_path('app/private/note-images');
    }
}
