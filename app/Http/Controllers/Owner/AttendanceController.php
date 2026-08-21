<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee.user');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->paginate(20);

        $employees = Employee::with('user')->get();

        return view('owner.attendances.index', compact('attendances', 'employees'));
    }

    public function show(Attendance $attendance)
    {
        $attendance->load('employee.user', 'verifier');
        return view('owner.attendances.show', compact('attendance'));
    }

    public function verify(Request $request, Attendance $attendance)
    {
        $attendance->update([
            'is_verified' => true,
            'verified_by' => auth()->id(),
            'verified_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Absensi berhasil diverifikasi');
    }

    public function bulkVerify(Request $request)
    {
        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendances,id',
        ]);

        Attendance::whereIn('id', $request->attendance_ids)
            ->update([
                'is_verified' => true,
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now(),
            ]);

        return redirect()->back()->with('success', 'Absensi berhasil diverifikasi');
    }
}