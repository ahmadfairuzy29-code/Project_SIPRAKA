<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BLOK ORANG 3 (BACKEND & DB 1) - AUTH, PROFIL, IZIN, & NOTIFIKASI
|--------------------------------------------------------------------------
| Dilarang menghapus atau mengubah rute di blok ini tanpa koordinasi Orang 3.
*/

// Rute Autentikasi Publik
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Rute Terproteksi Sanctum (Semua Role: Admin, Manager, Karyawan)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword']);

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);

    // Notifikasi Pengguna
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markRead']);

    // Pengajuan Izin / Sakit / Cuti (Khusus Karyawan)
    Route::middleware('role:karyawan')->group(function () {
        Route::post('/leaves', [LeaveRequestController::class, 'store']);
        Route::get('/leaves/my-requests', [LeaveRequestController::class, 'myIndex']);
    });
});

/*
|--------------------------------------------------------------------------
| BATAS BLOK ORANG 3
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| BLOK ORANG 4 (BACKEND & DB 2) - ABSENSI GPS/SELFIE & PANEL ADMIN/MANAGER
|--------------------------------------------------------------------------
| Silakan Orang 4 menuliskan rute /attendance/* dan /admin/* di bawah baris ini.
*/