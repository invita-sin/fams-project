<?php

namespace App\Notifications;

use App\Models\AttendanceCorrection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceCorrectionSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly AttendanceCorrection $correction,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengajuan Koreksi Absensi Baru')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Seorang karyawan telah mengajukan koreksi absensi.')
            ->line('Karyawan: ' . ($this->correction->employee->full_name ?? 'N/A'))
            ->line('Alasan: ' . $this->correction->reason)
            ->action('Lihat Pengajuan', url('/admin/attendance-corrections/' . $this->correction->id))
            ->line('Silakan review pengajuan tersebut.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'correction_id' => $this->correction->id,
            'employee_name' => $this->correction->employee?->full_name,
            'reason' => $this->correction->reason,
            'status' => $this->correction->status,
            'message' => 'Pengajuan koreksi absensi baru dari ' . ($this->correction->employee?->full_name ?? 'N/A'),
        ];
    }
}
