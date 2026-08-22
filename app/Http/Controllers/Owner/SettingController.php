<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::first();
        return view('owner.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'check_in_latitude' => 'nullable|numeric',
            'check_in_longitude' => 'nullable|numeric',
            'check_in_radius' => 'nullable|integer|min:10',
            'office_start_time' => 'nullable|date_format:H:i',
            'office_end_time' => 'nullable|date_format:H:i',
            'overtime_calculation' => 'nullable|in:hourly,daily',
            'overtime_minutes' => 'nullable|integer|min:0',
        ]);

        $settings = StoreSetting::first();
        if ($settings) {
            $settings->update($validated);
        } else {
            StoreSetting::create($validated);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui');
    }
}