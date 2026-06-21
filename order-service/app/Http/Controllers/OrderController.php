<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input API
        $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric'
        ]);

        // 2. Simpan Order ke Database (Status: Pending)
        $order = Order::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
            'status' => 'pending'
        ]);

        // 3. Siapkan Payload Message untuk Service Lain
        $messagePayload = json_encode([
            'event' => 'OrderCreated',
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'product_id' => $order->product_id,
            'quantity' => $order->quantity,
            'total_price' => $order->total_price,
            'timestamp' => now()->toDateTimeString()
        ]);

        // 4. Publish Message ke Redis Channel 'order_events' (Asinkron)
        // Service Product & Notification nantinya akan melakukan 'Subscribe' ke channel ini
        Redis::publish('order_events', $messagePayload);

        // 5. Kembalikan Response ke Client (Sangat cepat karena tidak menunggu proses notifikasi/stok)
        return response()->json([
            'success' => true,
            'message' => 'Order is being processed and event published',
            'data' => $order
        ], 201);
    }

    public function index(Request $request)
    {
        $query = Order::query()->latest();

        // Tanpa parameter: tampilkan semua order untuk kebutuhan dashboard/demo.
        // Dengan ?user_id=1: tampilkan riwayat milik user tertentu.
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ], 200);
    }

    public function show(Order $order)
    {
        return response()->json(['success' => true, 'data' => $order]);
    }
}
