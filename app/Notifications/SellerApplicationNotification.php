<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $status;
    public $reason;

    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        if ($this->status === 'approved') {
            $message = 'Selamat! Permohonan Anda sebagai Seller telah disetujui. Anda dapat mulai menambah produk jualan.';
            $url = route('seller.dashboard');
        } else {
            $message = 'Mohon maaf, permohonan Anda sebagai Seller ditolak. Alasan: ' . ($this->reason ?? 'Tidak memenuhi syarat pengumuman internal.');
            $url = route('home');
        }

        return [
            'type' => 'seller_application',
            'status' => $this->status,
            'message' => $message,
            'url' => $url
        ];
    }
}
