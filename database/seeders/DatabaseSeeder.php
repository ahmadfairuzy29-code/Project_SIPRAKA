<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\OfficeSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Administrator Contoh
        $admin = User::create([
            'email' => 'admin@sipraka.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $admin->id,
            'nip' => 'ADM001',
            'nama_lengkap' => 'Rahmat Hidayat',
            'jabatan' => 'Administrator Sistem',
            'divisi' => 'Teknologi Informasi',
            'no_hp' => '081234567890',
            'alamat' => 'Kantor Pusat SIPRAKA',
        ]);

        // 2. Akun Manager Contoh
        $manager = User::create([
            'email' => 'manager@sipraka.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $manager->id,
            'nip' => 'MGR001',
            'nama_lengkap' => 'Andi Wijaya',
            'jabatan' => 'Manajer Operasional',
            'divisi' => 'Teknologi Informasi',
            'no_hp' => '081234567891',
            'alamat' => 'Jakarta Selatan',
        ]);

        // 3. Akun Karyawan (User) Contoh
        $karyawan = User::create([
            'email' => 'budi@sipraka.com',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $karyawan->id,
            'nip' => 'EMP001',
            'nama_lengkap' => 'Budi Santoso',
            'jabatan' => 'Staf Pengembang IT',
            'divisi' => 'Teknologi Informasi',
            'no_hp' => '081234567892',
            'alamat' => 'Jakarta Pusat',
        ]);

        // 4. Pengaturan Kantor Awal
        OfficeSetting::create([
            'nama_kantor' => 'Kantor Pusat SIPRAKA',
            'latitude' => -6.20000000,
            'longitude' => 106.81666600,
            'radius_meter' => 100,
            'jam_masuk' => '08:00:00',
            'batas_terlambat' => '08:15:00',
            'jam_pulang' => '17:00:00',
        ]);
    }
}