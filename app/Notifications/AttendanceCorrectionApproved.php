<?php

namespace App\Notifications;

use App\Models\AttendanceCorrection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceCorrectionApproved extends Notification
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
            ->subject('Koreksi Absensi Disetujui')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pengajuan koreksi absensi Anda telah disetujui.')
            ->line('Alasan: ' . $this->correction->reason)
            ->line('Disetujui oleh: ' . ($this->correction->reviewer->name ?? 'N/A'))
            ->action('Lihat Detail', url('/admin/attendance-corrections/' . $this->correction->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'correction_id' => $this->correction->id,
            'status' => 'approved',
            'message' => 'Koreksi absensi Anda telah disetujui',
        ];
    }
}
