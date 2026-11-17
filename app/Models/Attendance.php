<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lat_masuk',
        'long_masuk',
        'lat_pulang',
        'long_pulang',
        'foto_masuk',
        'foto_pulang',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'lat_masuk' => 'float',
            'long_masuk' => 'float',
            'lat_pulang' => 'float',
            'long_pulang' => 'float',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}