<?php

namespace App\Notifications;

use App\Models\AttendanceCorrection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceCorrectionRejected extends Notification
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
            ->subject('Koreksi Absensi Ditolak')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pengajuan koreksi absensi Anda telah ditolak.')
            ->line('Alasan pengajuan: ' . $this->correction->reason)
            ->line('Alasan penolakan: ' . $this->correction->rejection_reason)
            ->line('Ditolak oleh: ' . ($this->correction->reviewer->name ?? 'N/A'))
            ->action('Lihat Detail', url('/admin/attendance-corrections/' . $this->correction->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'correction_id' => $this->correction->id,
            'status' => 'rejected',
            'rejection_reason' => $this->correction->rejection_reason,
            'message' => 'Koreksi absensi Anda ditolak: ' . ($this->correction->rejection_reason ?? ''),
        ];
    }
}
