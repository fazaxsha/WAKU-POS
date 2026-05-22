<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportExportedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $reportName;
    public string $filename;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reportName, string $filename)
    {
        $this->reportName = $reportName;
        $this->filename = $filename;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('storage/' . $this->filename);

        return (new MailMessage)
                    ->subject("{$this->reportName} Selesai Diekspor")
                    ->greeting('Halo!')
                    ->line("Proses ekspor {$this->reportName} Anda telah selesai.")
                    ->action('Unduh Laporan', $url)
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'      => $this->reportName . ' Siap Diunduh',
            'message'    => "Ekspor {$this->reportName} selesai. Klik untuk mengunduh.",
            'icon'       => 'bi-file-earmark-arrow-down-fill',
            'color'      => 'teal',
            'url'        => url('storage/' . $this->filename),
            'reportName' => $this->reportName,
            'filename'   => $this->filename,
        ];
    }
}
