<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Field yang boleh diisi secara massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password', // Wajib didaftarkan di sini
    ];

    /**
     * Field yang otomatis disembunyikan saat data diubah menjadi JSON/Array (Respons API).
     */
    protected $hidden = [
        'password', // Menjaga agar hash password tidak bocor ke service lain atau frontend
    ];

    /**
     * Casts bawaan Laravel jika diperlukan di masa depan.
     */
    protected $casts = [
        'password' => 'hashed', // Laravel akan otomatis meng-hash jika menggunakan metode tertentu, namun kita akan meng-hash manual di controller agar lebih eksplisit
    ];
}
