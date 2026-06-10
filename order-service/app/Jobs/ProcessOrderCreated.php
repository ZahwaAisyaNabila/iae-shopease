<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    // 1. Terima data order saat Job di-dispatch
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // 2. Logika apa yang dilakukan di background
    public function handle()
    {
        // Di sini kamu bisa menaruh logika komunikasi antar-service,
        // misalnya membungkus data ke Redis Queue khusus yang akan dibaca
        // oleh Service Product dan Notification.
    }
}
