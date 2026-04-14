<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Note;
use App\Services\NoteImageService;
use Illuminate\Console\Command;

class PruneOrphanedImagesCommand extends Command
{
    protected $signature = 'onyx:prune-orphaned-images {--dry-run : Affiche les fichiers à supprimer sans les supprimer}';

    protected $description = 'Supprime les images uploadées qui ne sont plus référencées dans aucune note.';

    private const string IMAGE_URL_PATTERN = '/\/notes\/images\/([^\s\)\"\']+)/';

    public function __construct(
        private readonly NoteImageService $imageService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->line('<comment>Mode dry-run — aucun fichier ne sera supprimé.</comment>');
        }

        $referenced = $this->referencedFilenames();
        $deleted = 0;

        foreach ($this->storedFilenames() as $filename) {
            if (in_array($filename, $referenced, true)) {
                continue;
            }

            $this->line(sprintf('  <fg=red>-</fg=red> %s (orphelin)', $filename));
            $deleted++;

            if (! $dryRun) {
                @unlink($this->imageService->path($filename));
            }
        }

        if ($deleted === 0) {
            $this->info('Aucune image orpheline trouvée.');
        } else {
            $this->info(sprintf('%d image(s) orpheline(s) %s.', $deleted, $dryRun ? 'détectée(s)' : 'supprimée(s)'));
        }

        return self::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function referencedFilenames(): array
    {
        $filenames = [];

        Note::whereNotNull('content')->each(function (Note $note) use (&$filenames): void {
            preg_match_all(self::IMAGE_URL_PATTERN, (string) $note->content, $matches);
            foreach ($matches[1] as $filename) {
                $filenames[] = basename($filename);
            }
        });

        return array_unique($filenames);
    }

    /**
     * @return string[]
     */
    private function storedFilenames(): array
    {
        $directory = storage_path('app/private/note-images');

        if (! is_dir($directory)) {
            return [];
        }

        return array_map(
            basename(...),
            glob($directory.'/*') ?: [],
        );
    }
}
