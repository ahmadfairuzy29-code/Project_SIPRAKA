<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * 1. GET /api/profile
     * Mengambil data profil pengguna yang sedang login (Semua Role)
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('employee');

        return response()->json([
            'status'  => 'success',
            'message' => 'Data profil berhasil diambil.',
            'data'    => [
                'id'           => $user->id,
                'email'        => $user->email,
                'role'         => $user->role,
                'is_active'    => $user->is_active,
                'employee_id'  => $user->employee->id ?? null,
                'nip'          => $user->employee->nip ?? null,
                'nama_lengkap' => $user->employee->nama_lengkap ?? $user->email,
                'jabatan'      => $user->employee->jabatan ?? null,
                'divisi'       => $user->employee->divisi ?? null,
                'no_hp'        => $user->employee->no_hp ?? null,
                'alamat'       => $user->employee->alamat ?? null,
                'foto_profil'  => $user->employee && $user->employee->foto_profil
                    ? asset('storage/' . $user->employee->foto_profil)
                    : null,
            ],
        ], 200);
    }

    /**
     * 2. POST /api/profile/update
     * Memperbarui nama_lengkap, no_hp, alamat, dan foto_profil (Semua Role)
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'foto_profil'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'data'    => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data biodata karyawan untuk akun ini tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $dataUpdate = [
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'alamat'       => $request->alamat,
        ];

        if ($request->hasFile('foto_profil')) {
            if ($employee->foto_profil && Storage::disk('public')->exists($employee->foto_profil)) {
                Storage::disk('public')->delete($employee->foto_profil);
            }

            $path = $request->file('foto_profil')->store('profiles', 'public');
            $dataUpdate['foto_profil'] = $path;
        }

        $employee->update($dataUpdate);

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data'    => [
                'id'           => $user->id,
                'email'        => $user->email,
                'role'         => $user->role,
                'nip'          => $employee->nip,
                'nama_lengkap' => $employee->nama_lengkap,
                'jabatan'      => $employee->jabatan,
                'divisi'       => $employee->divisi,
                'no_hp'        => $employee->no_hp,
                'alamat'       => $employee->alamat,
                'foto_profil'  => $employee->foto_profil
                    ? asset('storage/' . $employee->foto_profil)
                    : null,
            ],
        ], 200);
    }
}