<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupTempFiles extends Command
{
    protected $signature = 'app:cleanup-temp-files';

    protected $description = 'Remove temporary carta responsiva files older than configured threshold';

    public function handle(): int
    {
        $tempPath = storage_path('app/temp');
        $maxAgeHours = config('inventory.temp_file_max_age_hours', 1);

        if (! File::isDirectory($tempPath)) {
            $this->info('Temp directory does not exist. Nothing to clean.');

            return self::SUCCESS;
        }

        $files = File::files($tempPath);
        $deleted = 0;
        $cutoff = now()->subHours($maxAgeHours);

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoff->timestamp) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        $this->info("Cleaned up {$deleted} temporary file(s) older than {$maxAgeHours} hour(s).");

        return self::SUCCESS;
    }
}
