<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Collection $lowStockProducts;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $lowStockProducts)
    {
        $this->lowStockProducts = $lowStockProducts;
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
        $mail = (new MailMessage)
            ->subject('Peringatan: Stok Produk Menipis')
            ->greeting('Halo Admin!')
            ->line('Beberapa produk di sistem saat ini memiliki stok di bawah atau sama dengan batas minimum:')
            ->line(' ');

        foreach ($this->lowStockProducts as $product) {
            $mail->line("- **{$product->name}** (SKU: {$product->sku}): Tersisa {$product->stock_qty} unit (Min: {$product->stock_min})");
        }

        return $mail
            ->line(' ')
            ->action('Lihat Produk', url('/products'))
            ->line('Mohon segera lakukan restock untuk menghindari kehabisan barang.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Stok Menipis',
            'message' => $this->lowStockProducts->count() . ' produk memiliki stok di bawah batas minimum.',
            'icon'    => 'bi-exclamation-triangle-fill',
            'color'   => 'amber',
            'url'     => '/products?low_stock=1',
            'products_count' => $this->lowStockProducts->count(),
        ];
    }
}
