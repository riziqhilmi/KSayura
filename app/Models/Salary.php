<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period',
        'base_salary',
        'overtime_pay',
        'bonus',
        'deductions',
        'total_salary',
        'total_days_worked',
        'total_overtime_hours',
        'total_leaves_taken',
        'notes',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusLabelAttribute()
    {
        return [
            'draft' => 'Draft',
            'approved' => 'Disetujui',
            'paid' => 'Dibayar'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'gray',
            'approved' => 'green',
            'paid' => 'blue'
        ][$this->status] ?? 'gray';
    }

    public function getPeriodFormattedAttribute()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->period);
        return $date->format('F Y');
    }
}