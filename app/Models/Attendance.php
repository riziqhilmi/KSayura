<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'latitude',
        'longitude',
        'check_in_photo',
        'check_out_photo',
        'location_name',
        'notes',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusLabelAttribute()
    {
        return [
            'present' => 'Hadir',
            'absent' => 'Absen',
            'late' => 'Terlambat',
            'leave' => 'Cuti',
            'holiday' => 'Libur'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'leave' => 'blue',
            'holiday' => 'purple'
        ][$this->status] ?? 'gray';
    }
}