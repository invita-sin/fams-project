<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run
                            {--disk=local : Disk to store the backup}
                            {--keep=7 : Number of backups to keep}';

    protected $description = 'Backup the database to a file';

    public function handle(): int
    {
        $disk = Storage::disk($this->option('disk'));
        $keep = (int) $this->option('keep');

        $filename = 'backup-' . now()->format('Y-m-d-His') . '.sql';

        $this->components->task('Creating database backup', function () use ($disk, $filename, $keep) {
            $dbName = config('database.connections.' . config('database.default') . '.database');
            $dbUser = config('database.connections.' . config('database.default') . '.username');
            $dbPass = config('database.connections.' . config('database.default') . '.password');
            $dbHost = config('database.connections.' . config('database.default') . '.host');

            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s',
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbHost),
                escapeshellarg($dbName)
            );

            $output = null;
            $resultCode = null;

            exec($command . ' 2>&1', $output, $resultCode);

            if ($resultCode !== 0) {
                $this->components->error('mysqldump failed: ' . implode("\n", $output));
                return false;
            }

            $disk->put('backups/' . $filename, implode("\n", $output));

            // Clean old backups
            $files = collect($disk->files('backups'))
                ->filter(fn ($f) => str_starts_with(basename($f), 'backup-'))
                ->sort()
                ->values();

            if ($files->count() > $keep) {
                $toDelete = $files->take($files->count() - $keep);
                foreach ($toDelete as $file) {
                    $disk->delete($file);
                }
            }

            return true;
        });

        $this->components->info("Backup saved: backups/{$filename}");

        return Command::SUCCESS;
    }
}
