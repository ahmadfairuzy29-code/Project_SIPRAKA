<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * 1. POST /api/auth/register
     * Mendaftarkan akun karyawan baru (default role: 'karyawan')
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'nip'          => 'required|string|max:50|unique:employees,nip',
            'email'        => 'required|email|max:255|unique:users,email',
            'jabatan'      => 'required|string|max:100',
            'divisi'       => 'required|string|max:100',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'password'     => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'data'    => $validator->errors(),
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($request) {
                $user = User::create([
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'role'      => 'karyawan',
                    'is_active' => true,
                ]);

                $employee = Employee::create([
                    'user_id'      => $user->id,
                    'nip'          => $request->nip,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jabatan'      => $request->jabatan,
                    'divisi'       => $request->divisi,
                    'no_hp'        => $request->no_hp,
                    'alamat'       => $request->alamat,
                ]);

                return [
                    'user'     => $user,
                    'employee' => $employee,
                ];
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi akun karyawan berhasil. Silakan login.',
                'data'    => $result,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mendaftarkan akun: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }

    /**
     * 2. POST /api/auth/login
     * Memvalidasi email & password, mengecek is_active, mengembalikan token Sanctum & role
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'data'    => null,
            ], 422);
        }

        $user = User::with('employee')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau kata sandi yang Anda masukkan salah.',
                'data'    => null,
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi Administrator.',
                'data'    => null,
            ], 403);
        }

        $token = $user->createToken('sipraka_auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'role'  => $user->role,
                'user'  => [
                    'id'           => $user->id,
                    'email'        => $user->email,
                    'role'         => $user->role,
                    'is_active'    => $user->is_active,
                    'nama_lengkap' => $user->employee->nama_lengkap ?? $user->email,
                    'nip'          => $user->employee->nip ?? null,
                    'jabatan'      => $user->employee->jabatan ?? null,
                    'divisi'       => $user->employee->divisi ?? null,
                    'foto_profil'  => $user->employee->foto_profil ?? null,
                ],
            ],
        ], 200);
    }

    /**
     * 3. POST /api/auth/logout
     * Menghapus token sesi login yang sedang aktif (Semua Role)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil. Sesi telah diakhiri.',
            'data'    => null,
        ], 200);
    }

    /**
     * 4. POST /api/auth/forgot-password
     * Membuat token riset kata sandi berdasarkan email
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar di dalam sistem.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'data'    => null,
            ], 422);
        }

        $token = strtoupper(Str::random(6));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => $token,
                'created_at' => now(),
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Kode verifikasi reset kata sandi telah dibuat.',
            'data'    => [
                'email'       => $request->email,
                'reset_token' => $token,
            ],
        ], 200);
    }

    /**
     * 5. POST /api/auth/reset-password
     * Memverifikasi token riset & menyimpan kata sandi baru
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'data'    => null,
            ], 422);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode token verifikasi tidak valid atau sudah kedaluwarsa.',
                'data'    => null,
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kata sandi berhasil direset. Silakan login dengan kata sandi baru.',
            'data'    => null,
        ], 200);
    }

    /**
     * 6. PUT /api/auth/change-password
     * Mengganti kata sandi lama menjadi kata sandi baru (Semua Role yang sedang login)
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'data'    => null,
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kata sandi lama yang Anda masukkan tidak sesuai.',
                'data'    => null,
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kata sandi berhasil diperbarui.',
            'data'    => null,
        ], 200);
    }
}