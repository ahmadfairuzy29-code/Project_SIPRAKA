<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{
    /**
     * 1. POST /api/leaves
     * Mengirim pengajuan izin/sakit/cuti beserta file_lampiran (Role: karyawan)
     * Sekaligus mengirim notifikasi otomatis ke seluruh akun Manager & Admin
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'jenis'           => 'required|in:izin,sakit,cuti',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string',
            'file_lampiran'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
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
                'message' => 'Data karyawan tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        try {
            $leaveRequest = DB::transaction(function () use ($request, $employee) {
                $filePath = null;
                if ($request->hasFile('file_lampiran')) {
                    $filePath = $request->file('file_lampiran')->store('leave_attachments', 'public');
                }

                $leave = LeaveRequest::create([
                    'employee_id'     => $employee->id,
                    'jenis'           => $request->jenis,
                    'tanggal_mulai'   => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'alasan'          => $request->alasan,
                    'file_lampiran'   => $filePath,
                    'status'          => 'menunggu',
                ]);

                // Kirim notifikasi ke seluruh Manager & Admin yang aktif
                $approvers = User::whereIn('role', ['manager', 'admin'])
                    ->where('is_active', true)
                    ->get();

                $jenisLabel = ucfirst($request->jenis);
                foreach ($approvers as $approver) {
                    Notification::create([
                        'user_id' => $approver->id,
                        'judul'   => "Pengajuan {$jenisLabel} Baru",
                        'pesan'   => "{$employee->nama_lengkap} ({$employee->nip}) mengajukan {$request->jenis} dari tanggal {$request->tanggal_mulai} s.d. {$request->tanggal_selesai}.",
                        'tipe'    => 'status_izin',
                        'is_read' => false,
                    ]);
                }

                return $leave;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Permohonan izin/cuti berhasil dikirim dan menunggu persetujuan.',
                'data'    => $leaveRequest,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengirim pengajuan: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }

    /**
     * 2. GET /api/leaves/my-requests
     * Mengambil daftar riwayat pengajuan izin milik karyawan beserta status & catatan approver
     */
    public function myIndex(Request $request): JsonResponse
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data karyawan tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $leaves = LeaveRequest::with('approver.employee')
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id'              => $item->id,
                    'jenis'           => $item->jenis,
                    'tanggal_mulai'   => $item->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $item->tanggal_selesai->format('Y-m-d'),
                    'alasan'          => $item->alasan,
                    'file_lampiran'   => $item->file_lampiran ? asset('storage/' . $item->file_lampiran) : null,
                    'status'          => $item->status,
                    'catatan_admin'   => $item->catatan_admin,
                    'diproses_oleh'   => $item->approver
                        ? ($item->approver->employee->nama_lengkap ?? $item->approver->email)
                        : null,
                    'created_at'      => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'status'  => 'success',
            'message' => 'Riwayat pengajuan izin berhasil diambil.',
            'data'    => $leaves,
        ], 200);
    }
}