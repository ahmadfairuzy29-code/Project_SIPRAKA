<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->decimal('lat_masuk', 10, 8)->nullable();
            $table->decimal('long_masuk', 11, 8)->nullable();
            $table->decimal('lat_pulang', 10, 8)->nullable();
            $table->decimal('long_pulang', 11, 8)->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alfa'])->default('hadir');
            $table->timestamps();

            $table->unique(['employee_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};