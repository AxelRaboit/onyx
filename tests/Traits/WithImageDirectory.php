<?php

declare(strict_types=1);

namespace Tests\Traits;

trait WithImageDirectory
{
    private string $imageDir;

    protected function setUpImageDirectory(): void
    {
        $this->imageDir = storage_path('app/private/note-images');
        if (! is_dir($this->imageDir)) {
            mkdir($this->imageDir, 0775, true);
        }
    }

    protected function tearDownImageDirectory(): void
    {
        foreach (glob($this->imageDir . '/test_*') as $file) {
            @unlink($file);
        }
    }

    protected function createTestImage(string $filename): string
    {
        $path = $this->imageDir . '/' . $filename;
        file_put_contents($path, 'fake-image-content');

        return $path;
    }
}
