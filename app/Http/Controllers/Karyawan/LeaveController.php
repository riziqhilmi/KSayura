<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        $leaves = Leave::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('karyawan.leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('karyawan.leaves.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['annual', 'sick', 'personal', 'other'])],
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $employee = auth()->user()->employee;
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInWeekdays($endDate) + 1;

        // Check for overlapping leaves
        $overlapping = Leave::where('employee_id', $employee->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terdapat cuti yang overlapping dengan tanggal yang dipilih');
        }

        Leave::create([
            'employee_id' => $employee->id,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.leaves.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim');
    }
}