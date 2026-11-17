<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kantor');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meter')->default(50);
            $table->time('jam_masuk');
            $table->time('batas_terlambat');
            $table->time('jam_pulang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_settings');
    }
};