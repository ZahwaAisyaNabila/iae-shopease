<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    // ✅ GET ALL USERS
    public function index()
    {
        try {
            $users = User::all();

            return response()->json([
                "message" => "List of users",
                "data" => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // ✅ GET USER BY ID
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "message" => "User not found"
                ], 404);
            }

            return response()->json([
                "message" => "User detail",
                "data" => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // ✅ CREATE USER (INI YANG SERING ERROR)
    public function store(Request $request)
    {
        try {
            // 🔍 Validasi
            $validated = $request->validate([
                "name" => "required|string|max:255",
                "email" => "required|email|unique:users,email",
                "phone" => "nullable|string|max:20",
                "address" => "nullable|string"
            ]);

            // 🔥 Simpan ke DB
            $user = User::create($validated);

            return response()->json([
                "message" => "User created successfully",
                "data" => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error creating user",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // 🔥 CONSUMER → ambil data order
    public function getUserOrders($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "message" => "User not found"
                ], 404);
            }

            $orderServiceUrl = env('ORDER_SERVICE_URL');

            $response = Http::get($orderServiceUrl . "/api/orders/user/" . $id);

            return response()->json([
                "message" => "User orders fetched",
                "user" => $user,
                "orders" => $response->json()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed connect to OrderService",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
