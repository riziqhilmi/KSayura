<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'is_active',


    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_shifts');
    }

    public function getDurationAttribute()
    {
        $start = new \DateTime($this->start_time);
        $end = new \DateTime($this->end_time);
        $interval = $start->diff($end);
        return $interval->format('%H jam %i menit');
    }
}
