<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationId;

    public function __construct($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    public function handle(): void
    {
        // Cari data notifikasi di database
        $notification = Notification::find($this->notificationId);

        if (!$notification) {
            return;
        }

        try {
            // SIMULASI PROSES PENGIRIMAN (Misal ke API WhatsApp / Email)
            // if ($notification->channel == 'email') { ... kirim email ... }
            
            // Jika berhasil:
            $notification->update([
                'status' => 'sent'
            ]);

        } catch (Exception $e) {
            // Jika gagal, catat errornya agar bisa di-retry nanti
            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            // Lempar kembali exception agar Laravel Queue tahu kalau job ini gagal dan perlu dicoba lagi
            throw $e; 
        }
    }
}