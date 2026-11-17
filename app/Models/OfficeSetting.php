<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kantor',
        'latitude',
        'longitude',
        'radius_meter',
        'jam_masuk',
        'batas_terlambat',
        'jam_pulang',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'radius_meter' => 'integer',
        ];
    }
}