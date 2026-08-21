<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Stats
        $totalEmployees = Employee::count();
        $presentToday = Attendance::where('date', $today)
            ->where('status', 'present')
            ->count();
        $lateToday = Attendance::where('date', $today)
            ->where('status', 'late')
            ->count();
        $absentToday = Attendance::where('date', $today)
            ->where('status', 'absent')
            ->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $totalSalaryThisMonth = Salary::where('period', Carbon::now()->format('Y-m'))
            ->where('status', 'approved')
            ->sum('total_salary');

        // Attendance chart data for current month
        $attendanceChart = Attendance::whereBetween('date', [
            $thisMonth,
            Carbon::now()
        ])
        ->select(
            DB::raw('DATE(date) as date'),
            DB::raw('COUNT(CASE WHEN status = "present" THEN 1 END) as present'),
            DB::raw('COUNT(CASE WHEN status = "late" THEN 1 END) as late'),
            DB::raw('COUNT(CASE WHEN status = "absent" THEN 1 END) as absent')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Recent attendances
        $recentAttendances = Attendance::with('employee.user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Upcoming leaves
        $upcomingLeaves = Leave::with('employee.user')
            ->where('status', 'pending')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Employee performance - top 5 by attendance
        $topPerformers = Employee::withCount(['attendances as total_present' => function($query) use ($thisMonth) {
            $query->where('status', 'present')
                ->whereBetween('date', [$thisMonth, Carbon::now()]);
        }])
        ->orderBy('total_present', 'desc')
        ->take(5)
        ->get();

        return view('owner.dashboard', compact(
            'totalEmployees',
            'presentToday',
            'lateToday',
            'absentToday',
            'pendingLeaves',
            'totalSalaryThisMonth',
            'attendanceChart',
            'recentAttendances',
            'upcomingLeaves',
            'topPerformers'
        ));
    }
}