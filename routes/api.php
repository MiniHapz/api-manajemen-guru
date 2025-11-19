<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CabdinController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\DashboardOperatorController;
use App\Http\Controllers\GolonganPangkatController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\RiwayatKepegawaianController;
use App\Http\Controllers\JamMengajarController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\KategoriDokumenController;
use App\Http\Controllers\UsulPerubahanController;
use App\Http\Controllers\LogAuditController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\MapelJurusanController;
use Illuminate\Http\Request;


// Tes API
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Auth
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Endpoint /api/me

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    $user = $request->user()->load('sekolah');
    return response()->json([
        'status' => 'success',
        'data' => $user
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user()->load('sekolah'));
});


Route::apiResource('sekolah', SekolahController::class);
// Proteksi dengan Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    
    
    Route::get('/dashboard/operator', [DashboardOperatorController::class, 'index']);
    Route::put('/change-password', [UserController::class, 'changePassword']);

    Route::apiResource('dokumen', DokumenController::class);
    Route::get('dokumen/download/{id}', [DokumenController::class, 'download']);
    Route::get('dokumen/preview/{id}', [DokumenController::class, 'preview']);

    // Kategori Dokumen
    Route::apiResource('kategori-dokumen', KategoriDokumenController::class);

    // User Management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);

    Route::middleware(['isAdmin'])->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::put('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    });

    // ===== Master Data =====
    Route::apiResource('mapel', MapelController::class);

    // ===== Data Dinamis =====
    Route::get('guru/akan-pensiun', [GuruController::class, 'akanPensiun']);
    Route::get('guru/summary', [GuruController::class, 'summary']);

    // 🟢 Semua user login bisa lihat data guru
    Route::get('guru', [GuruController::class, 'index']);
    Route::get('guru/{nip}', [GuruController::class, 'show']);

    // Admin aja yang boleh tambah/edit/hapus guru
        Route::post('guru', [GuruController::class, 'store']);
        Route::put('guru/{nip}', [GuruController::class, 'update']);
        Route::delete('guru/{nip}', [GuruController::class, 'destroy']);
    
    Route::apiResource('dokumen', DokumenController::class);


    Route::get('/dashboard-admin', [DashboardAdminController::class, 'index']);

    // ===== Usulan Perubahan =====
});


// Tes role admin
Route::middleware(['auth:sanctum', 'isAdmin'])->get('/check-admin', function () {
    return response()->json(['message' => 'Anda adalah admin!']);
});
