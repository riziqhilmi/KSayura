<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StoreSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today();

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        $storeSettings = StoreSetting::first();

        return view('karyawan.attendances.index', compact(
            'todayAttendance',
            'attendances',
            'storeSettings',
            'employee'
        ));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $employee = auth()->user()->employee;
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if already checked in
        $existing = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            return redirect()->back()->with('error', 'Anda sudah melakukan check-in hari ini');
        }

        // Get store settings
        $storeSettings = StoreSetting::first();

        // Check location (if store settings exist)
        $isInLocation = true;
        if ($storeSettings && $storeSettings->check_in_latitude) {
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $storeSettings->check_in_latitude,
                $storeSettings->check_in_longitude
            );
            $isInLocation = $distance <= ($storeSettings->check_in_radius ?? 100);
        }

        if (!$isInLocation) {
            return redirect()->back()->with('error', 'Anda harus berada di lokasi toko untuk check-in');
        }

        // Determine status
        $status = 'present';
        $checkInTime = Carbon::parse($now->format('H:i:s'));
        $officeStartTime = Carbon::parse($storeSettings->office_start_time ?? '08:00:00');
        $gracePeriod = Carbon::parse($storeSettings->office_start_time ?? '08:00:00')
            ->addMinutes($storeSettings->overtime_minutes ?? 30);

        if ($checkInTime->gt($gracePeriod)) {
            $status = 'late';
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendances', 'public');
        }

        // Create attendance
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in' => $now,
            'status' => $status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
            'check_in_photo' => $photoPath,
            'notes' => $request->notes,
            'is_verified' => false,
        ]);

        return redirect()->route('karyawan.attendances.index')
            ->with('success', 'Check-in berhasil');
    }

    public function checkOut(Request $request)
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return redirect()->back()->with('error', 'Anda belum check-in hari ini');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'Anda sudah check-out hari ini');
        }

        $now = Carbon::now();
        
        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendances', 'public');
        }

        $attendance->update([
            'check_out' => $now,
            'check_out_photo' => $photoPath,
        ]);

        return redirect()->route('karyawan.attendances.index')
            ->with('success', 'Check-out berhasil');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}