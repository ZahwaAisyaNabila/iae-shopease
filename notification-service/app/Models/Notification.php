<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Tentukan kolom mana saja yang boleh diisi
    protected $fillable = [
        'user_id',
        'type',
        'channel',
        'message',
        'status',
        'error_message'
    ];

    // Default nilai status jika tidak diisi
    protected $attributes = [
        'status' => 'pending',
    ];
}