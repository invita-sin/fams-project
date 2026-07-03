<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class BackupDatabaseTest extends TestCase
{
    public function test_backup_command_runs(): void
    {
        Storage::fake('local');

        $this->artisan('backup:run')
            ->assertSuccessful();
    }

    public function test_backup_creates_file(): void
    {
        Storage::fake('local');

        $this->artisan('backup:run');

        $files = Storage::disk('local')->files('backups');
        $this->assertCount(1, $files);
        $this->assertStringContainsString('backup-', $files[0]);
    }

    public function test_backup_keeps_specified_number(): void
    {
        Storage::fake('local');

        // Create existing backups
        Storage::disk('local')->put('backups/backup-2024-01-01-000000.sql', 'old');
        Storage::disk('local')->put('backups/backup-2024-01-02-000000.sql', 'old');
        Storage::disk('local')->put('backups/backup-2024-01-03-000000.sql', 'old');

        $this->artisan('backup:run --keep=2');

        $files = Storage::disk('local')->files('backups');
        $this->assertCount(2, $files);
    }
}
