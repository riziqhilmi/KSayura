@extends('layouts.app')

@section('title', 'Detail Gaji')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Detail Gaji</h1>
        <a href="{{ route('karyawan.salary.index') }}" class="text-gray-600 hover:text-gray-800">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Periode</p>
                    <p class="text-lg font-semibold">{{ $salary->period_formatted }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="px-3 py-1 text-sm rounded-full bg-{{ $salary->status_color }}-100 text-{{ $salary->status_color }}-800">
                        {{ $salary->status_label }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Dibayar</p>
                    <p class="text-lg font-semibold">{{ $salary->paid_at ? $salary->paid_at->format('d/m/Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Rincian Gaji</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Gaji Pokok</span>
                    <span class="font-medium">Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Uang Lembur</span>
                    <span class="font-medium">Rp {{ number_format($salary->overtime_pay, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Bonus</span>
                    <span class="font-medium text-green-600">+ Rp {{ number_format($salary->bonus, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Potongan</span>
                    <span class="font-medium text-red-600">- Rp {{ number_format($salary->deductions, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-3 border-t-2 border-gray-300">
                    <span class="text-lg font-semibold">Total Gaji</span>
                    <span class="text-2xl font-bold text-green-600">Rp {{ number_format($salary->total_salary, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Statistik Kehadiran</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-500">Hari Kerja</p>
                    <p class="text-xl font-bold">{{ $salary->total_days_worked }} hari</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Jam Lembur</p>
                    <p class="text-xl font-bold">{{ $salary->total_overtime_hours }} jam</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Cuti</p>
                    <p class="text-xl font-bold">{{ $salary->total_leaves_taken }} hari</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Rata-rata Harian</p>
                    <p class="text-xl font-bold">
                        Rp {{ number_format($salary->total_days_worked > 0 ? $salary->total_salary / $salary->total_days_worked : 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        @if($salary->notes)
        <div class="p-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold mb-2">Catatan</h3>
            <p class="text-gray-600">{{ $salary->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection