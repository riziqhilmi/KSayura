<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        $salaries = Salary::where('employee_id', $employee->id)
            ->orderBy('period', 'desc')
            ->paginate(12);

        return view('karyawan.salary.index', compact('salaries'));
    }

    public function show(Salary $salary)
    {
        // Ensure the salary belongs to the logged-in employee
        if ($salary->employee_id !== auth()->user()->employee->id) {
            abort(403);
        }

        $salary->load('employee.user');
        return view('karyawan.salary.show', compact('salary'));
    }
}