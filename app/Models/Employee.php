<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'position',
        'join_date',
        'salary_type',
        'salary_amount',
        'overtime_rate',
    ];

    protected $casts = [
        'join_date' => 'date',
        'salary_amount' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'employee_shifts');
    }

    public function getSalaryTypeLabelAttribute()
    {
        return [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan'
        ][$this->salary_type] ?? $this->salary_type;
    }

    public function getMonthlySalaryAttribute()
    {
        if ($this->salary_type === 'monthly') {
            return $this->salary_amount;
        } elseif ($this->salary_type === 'weekly') {
            return $this->salary_amount * 4;
        } else {
            return $this->salary_amount * 30;
        }
    }
}