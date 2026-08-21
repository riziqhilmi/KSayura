<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        return view('owner.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('owner.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'position' => 'required|string|max:100',
            'join_date' => 'required|date',
            'salary_type' => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
            'salary_amount' => 'required|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'karyawan',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        // Create employee
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT),
            'position' => $validated['position'],
            'join_date' => $validated['join_date'],
            'salary_type' => $validated['salary_type'],
            'salary_amount' => $validated['salary_amount'],
            'overtime_rate' => $validated['overtime_rate'] ?? 0,
        ]);

        return redirect()->route('owner.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');
        return view('owner.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($employee->user_id)],
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'position' => 'required|string|max:100',
            'join_date' => 'required|date',
            'salary_type' => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
            'salary_amount' => 'required|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
        ]);

        // Update user
        $employee->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        // Update employee
        $employee->update([
            'position' => $validated['position'],
            'join_date' => $validated['join_date'],
            'salary_type' => $validated['salary_type'],
            'salary_amount' => $validated['salary_amount'],
            'overtime_rate' => $validated['overtime_rate'] ?? 0,
        ]);

        return redirect()->route('owner.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function destroy(Employee $employee)
    {
        $employee->user->delete();
        $employee->delete();

        return redirect()->route('owner.employees.index')
            ->with('success', 'Karyawan berhasil dihapus');
    }
}