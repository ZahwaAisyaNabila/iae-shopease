<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'success', 'data' => Product::query()->latest()->get()]);
    }

    public function show(Product $product)
    {
        return response()->json(['status' => 'success', 'data' => $product]);
    }

    public function store(Request $request)
    {
        $product = Product::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]));

        return response()->json(['status' => 'success', 'data' => $product], 201);
    }
}
