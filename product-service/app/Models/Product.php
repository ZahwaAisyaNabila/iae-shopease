<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Daftarkan kolom agar bisa disimpan via GraphQL Mutation
    protected $fillable = [
        'name',
        'price',
        'stock',
    ];
}
