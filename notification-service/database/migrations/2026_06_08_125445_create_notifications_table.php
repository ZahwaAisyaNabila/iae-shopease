<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID user dari User Service
            $table->string('type');                // Contoh: 'order_created', 'payment_failed'
            $table->string('channel');             // Contoh: 'email', 'whatsapp', 'push'
            $table->text('message');               // Isi pesan notifikasi
            
            // Kolom status untuk monitoring asinkron
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable(); // Menyimpan alasan jika gagal kirim
            
            $table->timestamps();

            // Indexing untuk mempercepat pencarian riwayat berdasarkan user
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};