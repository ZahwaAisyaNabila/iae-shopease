<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotificationJob;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Notification::query()->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        return response()->json(['status' => 'success', 'data' => $query->get()]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'type'    => 'required|string',
            'message' => 'required|string',
            'recipient_channel' => 'required|string',
        ]);

        // 1. Simpan ke database dengan status awal 'pending'
        $notification = \App\Models\Notification::create([
            'user_id' => $validated['user_id'],
            'type'    => $validated['type'],
            'channel' => $validated['recipient_channel'],
            'message' => $validated['message'],
        ]);

        // 2. Dispatch Job dengan membawa data ID Notification
        \App\Jobs\SendNotificationJob::dispatch($notification->id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification has been queued.',
            'notification_id' => $notification->id
        ], 202);
    }
}
