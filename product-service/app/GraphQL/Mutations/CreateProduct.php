<?php

namespace App\GraphQL\Mutations;

use App\Models\Product;
use App\Jobs\PublishProductEvent;

final class CreateProduct
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // 1. Simpan ke Database (Pastikan kamu sudah buat migration & model Product ya)
        // Untuk contoh cepat, kita gunakan model Eloquent:
        $product = Product::create([
            'name' => $args['name'],
            'price' => $args['price'],
            'stock' => $args['stock'],
        ]);

        // 2. ASINKRON: Kirim ke Redis Queue menggunakan Job yang kita buat tadi
        // dispatch() membuat proses ini langsung selesai tanpa menunggu Redis selesai publish
        PublishProductEvent::dispatch('PRODUCT_CREATED', $product->toArray());

        // 3. Kembalikan respon GraphQL dengan cepat ke client
        return $product;
    }
}
