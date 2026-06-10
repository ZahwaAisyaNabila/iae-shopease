<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class PublishProductEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $eventType;
    public $productData;

    // Menerima tipe event dan data produk
    public function __construct($eventType, $productData)
    {
        $this->eventType = $eventType;
        $this->productData = $productData;
    }

    // Eksekusi yang berjalan asinkron di background oleh Redis
    public function handle()
    {
        $payload = [
            'eventType' => $this->eventType,
            'data' => $this->productData
        ];

        // Publish ke channel 'product_events' menggunakan Redis Pub/Sub
        Redis::publish('product_events', json_encode($payload));

        logger(" [Redis Queue] Berhasil publish event {$this->eventType} secara asinkron.");
    }
}
