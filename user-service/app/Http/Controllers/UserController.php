<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // 1. GET ALL USERS (Melihat semua user)
    public function index()
    {
        // Karena 'password' ada di properti $hidden pada Model,
        // list user yang dikembalikan tidak akan menampilkan kolom password.
        return response()->json([
            'status' => 'success',
            'data' => User::all()
        ], 200);
    }

    // 2. GET USER BY ID (Digunakan untuk verifikasi oleh Order Service)
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    // 3. POST / CREATE NEW USER (Menambahkan user beserta password)
    public function store(Request $request)
    {
        // Aturan validasi input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6', // Validasi password minimal 6 karakter
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Simpan data ke MySQL secara sinkronus
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Meng-hash password teks biasa menjadi Bcrypt aman
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User successfully created',
            'data' => $user
        ], 201);
    }
}
