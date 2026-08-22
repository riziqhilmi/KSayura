<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // Monthly stats
        $monthlyAttendance = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$thisMonth, Carbon::now()])
            ->get();

        $totalPresent = $monthlyAttendance->where('status', 'present')->count();
        $totalLate = $monthlyAttendance->where('status', 'late')->count();
        $totalAbsent = $monthlyAttendance->where('status', 'absent')->count();
        $totalLeave = $monthlyAttendance->where('status', 'leave')->count();

        // Pending leaves
        $pendingLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->get();

        // Recent attendance
        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        // Leave balance
        $totalLeavesTaken = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->sum('total_days');

        $leaveQuota = 12; // Annual leave quota

        return view('karyawan.dashboard', compact(
            'employee',
            'todayAttendance',
            'totalPresent',
            'totalLate',
            'totalAbsent',
            'totalLeave',
            'pendingLeaves',
            'recentAttendances',
            'totalLeavesTaken',
            'leaveQuota'
        ));
    }
}