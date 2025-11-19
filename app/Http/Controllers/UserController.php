<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $role = $request->role ?? 'user';

        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
            'role' => 'in:admin,user',
        ];

        if ($role === 'user') {
            $rules['sekolah_id'] = 'required|exists:sekolah,id';
        }

        $validatedData = $request->validate($rules);

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
            'role' => $role,
            'sekolah_id' => $role === 'user' ? $validatedData['sekolah_id'] : null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user,
            'token' => $token,
        ], 201);
    }


    // SHOW (Get detail user by ID)
public function show($id)
{
    $user = User::with('sekolah')->find($id);

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $user
    ]);
}

    // GET ALL USERS
public function index(Request $request)
{
    $users = User::with('sekolah')
        ->select('id', 'name', 'username', 'role', 'sekolah_id', 'status', 'last_login_at')
        ->get();

    return response()->json($users);
}


    // LOGIN
    // LOGIN
public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::with('sekolah')->where('username', $request->username)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Username atau password salah'], 401);
    }

    // ✅ Cek status user
    if ($user->status !== 'Aktif') {
        return response()->json(['message' => 'Akun belum aktif. Hubungi admin.'], 403);
    }

    // ✅ Simpan waktu terakhir login
    $user->update(['last_login_at' => now()]);

    // ✅ Buat token baru
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'message' => 'Login berhasil',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'sekolah' => $user->sekolah ? [
                'id' => $user->sekolah->id,
                'nama_sekolah' => $user->sekolah->nama_sekolah,
            ] : null,
            'last_login_at' => $user->last_login_at, // kirim juga ke frontend
        ],
    ]);
}

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    // GET USER LOGIN (untuk /api/me)
    public function me(Request $request)
    {
        $user = $request->user()->load('sekolah');

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    // GANTI PASSWORD
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // otomatis cek dengan new_password_confirmation
        ]);

        $user = $request->user();

        // Cek apakah password lama cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password lama tidak sesuai.',
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah.',
        ]);
    }

public function toggleStatus($id)
{
    $user = User::findOrFail($id);

    $user->status = $user->status === 'Aktif' ? 'NonAktif' : 'Aktif';
    $user->save();

    return response()->json([
        'message' => 'Status pengguna berhasil diubah.',
        'status' => $user->status,
    ]);
}

// UPDATE USER (Admin only)
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Validasi input
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users,username,'.$id,
        'sekolah_id' => 'nullable|exists:sekolah,id',
        'status' => 'required|string|in:Aktif,NonAktif',
        'role' => 'required|string|in:admin,user',
        'password' => 'nullable|string|min:6',
    ]);

    // Kalau password diisi, update; kalau tidak, biarkan
    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']);
    }

    $user->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'User berhasil diperbarui',
        'data' => $user
    ]);
}


}
