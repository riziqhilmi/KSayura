<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('employee.user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('owner.leaves.index', compact('leaves'));
    }

    public function approve(Leave $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        // Create attendance for leave days
        $start = Carbon::parse($leave->start_date);
        $end = Carbon::parse($leave->end_date);
        
        for ($date = $start; $date <= $end; $date->addDay()) {
            $leave->employee->attendances()->updateOrCreate(
                ['date' => $date->format('Y-m-d')],
                ['status' => 'leave', 'is_verified' => true]
            );
        }

        return redirect()->back()->with('success', 'Cuti berhasil disetujui');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Cuti ditolak');
    }
}