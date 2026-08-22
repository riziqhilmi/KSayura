<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = Salary::with('employee.user');

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $salaries = $query->orderBy('period', 'desc')
            ->paginate(20);

        $employees = Employee::with('user')->get();
        $periods = Salary::distinct()->pluck('period');

        return view('owner.salaries.index', compact('salaries', 'employees', 'periods'));
    }

    public function calculate($period = null)
    {
        $period = $period ?: Carbon::now()->format('Y-m');
        list($year, $month) = explode('-', $period);
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $employees = Employee::all();

        DB::transaction(function () use ($employees, $period, $startDate, $endDate) {
            foreach ($employees as $employee) {
                // Calculate attendance stats
                $attendances = $employee->attendances()
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $totalDaysWorked = $attendances->whereIn('status', ['present', 'late'])->count();
                $totalOvertimeHours = 0; // Calculate overtime logic here
                $totalLeavesTaken = $attendances->where('status', 'leave')->count();

                // Calculate salary
                $baseSalary = $employee->salary_amount;
                if ($employee->salary_type === 'daily') {
                    $baseSalary = $employee->salary_amount * $totalDaysWorked;
                } elseif ($employee->salary_type === 'weekly') {
                    $weeksWorked = ceil($totalDaysWorked / 7);
                    $baseSalary = $employee->salary_amount * $weeksWorked;
                }

                $overtimePay = $totalOvertimeHours * ($employee->overtime_rate ?? 0);
                $bonus = 0; // Add bonus logic here
                $deductions = 0; // Add deduction logic here
                $totalSalary = $baseSalary + $overtimePay + $bonus - $deductions;

                // Create or update salary
                Salary::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'period' => $period,
                    ],
                    [
                        'base_salary' => $baseSalary,
                        'overtime_pay' => $overtimePay,
                        'bonus' => $bonus,
                        'deductions' => $deductions,
                        'total_salary' => $totalSalary,
                        'total_days_worked' => $totalDaysWorked,
                        'total_overtime_hours' => $totalOvertimeHours,
                        'total_leaves_taken' => $totalLeavesTaken,
                        'status' => 'draft',
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Perhitungan gaji berhasil dilakukan');
    }

    public function approve(Salary $salary)
    {
        $salary->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Gaji telah disetujui');
    }

    public function markAsPaid(Salary $salary)
    {
        $salary->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Gaji telah dibayarkan');
    }

    public function payslip(Salary $salary)
    {
        $salary->load('employee.user');
        return view('owner.salaries.payslip', compact('salary'));
    }
}