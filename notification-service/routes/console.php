<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use App\Models\Notification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:subscribe', function () {
    $this->info('Listening for order_events on Redis.');

    Redis::subscribe(['order_events'], function (string $message) {
        $event = json_decode($message, true);

        if (($event['event'] ?? null) !== 'OrderCreated') {
            return;
        }

        Notification::create([
            'user_id' => $event['user_id'],
            'type' => 'order_created',
            'channel' => 'email',
            'message' => "Pesanan #{$event['order_id']} telah diterima dan sedang diproses.",
            'status' => 'sent',
        ]);
    });
})->purpose('Consume OrderCreated events from Redis');
