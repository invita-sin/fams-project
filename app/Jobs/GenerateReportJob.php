<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ReportGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $type,
        private readonly array $filters,
        private readonly string $format,
        private readonly string $userId,
    ) {}

    public function handle(ReportGeneratorService $reportService): void
    {
        $filePath = match ($this->format) {
            'xlsx' => match ($this->type) {
                'attendance' => $reportService->exportAttendanceXlsx($this->filters),
                'documentations' => $reportService->exportDocumentationsXlsx($this->filters),
                default => throw new \InvalidArgumentException("Unknown report type: {$this->type}"),
            },
            'csv' => match ($this->type) {
                'attendance' => $reportService->exportAttendanceCsv($this->filters),
                'documentations' => $reportService->exportDocumentationsCsv($this->filters),
                default => throw new \InvalidArgumentException("Unknown report type: {$this->type}"),
            },
            default => throw new \InvalidArgumentException("Unknown format: {$this->format}"),
        };

        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new class($filePath, $this->type, $this->format) extends \Illuminate\Notifications\Notification {
                use Queueable;

                public function __construct(
                    private readonly string $filePath,
                    private readonly string $type,
                    private readonly string $format,
                ) {}

                public function via(object $notifiable): array
                {
                    return ['database'];
                }

                public function toArray(object $notifiable): array
                {
                    return [
                        'message' => "Laporan {$this->type} telah siap dalam format {$this->format}",
                        'file_path' => $this->filePath,
                        'type' => $this->type,
                        'format' => $this->format,
                    ];
                }
            });
        }
    }
}
