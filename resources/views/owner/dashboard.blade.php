@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Karyawan</div>
            <div class="text-2xl font-bold">{{ $totalEmployees }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Hadir Hari Ini</div>
            <div class="text-2xl font-bold text-green-600">{{ $presentToday }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Terlambat</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $lateToday }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Absen</div>
            <div class="text-2xl font-bold text-red-600">{{ $absentToday }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Cuti Pending</div>
            <div class="text-2xl font-bold text-blue-600">{{ $pendingLeaves }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Gaji Bulan Ini</div>
            <div class="text-2xl font-bold text-purple-600">Rp {{ number_format($totalSalaryThisMonth, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Grafik Kehadiran Bulan Ini</h3>
        <canvas id="attendanceChart" height="200"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Attendances -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Absensi Terbaru</h3>
            <div class="space-y-3">
                @foreach($recentAttendances as $attendance)
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <div class="font-medium">{{ $attendance->employee->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $attendance->date->format('d/m/Y') }}</div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $attendance->status_color }}-100 text-{{ $attendance->status_color }}-800">
                        {{ $attendance->status_label }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Upcoming Leaves -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Permohonan Cuti Pending</h3>
            <div class="space-y-3">
                @foreach($upcomingLeaves as $leave)
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <div class="font-medium">{{ $leave->employee->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="flex space-x-2">
                        <form action="{{ route('owner.leaves.approve', $leave) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">Setujui</button>
                        </form>
                        <button onclick="showRejectModal({{ $leave->id }})" class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">Tolak</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Top 5 Karyawan Terbaik</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach($topPerformers as $index => $employee)
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl mb-2">{{ ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'][$index] }}</div>
                <div class="font-medium">{{ $employee->user->name }}</div>
                <div class="text-sm text-gray-500">{{ $employee->position }}</div>
                <div class="text-sm text-green-600">{{ $employee->total_present ?? 0 }} hari hadir</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const chartData = @json($attendanceChart);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(item => moment(item.date).format('DD/MM')),
            datasets: [
                {
                    label: 'Hadir',
                    data: chartData.map(item => item.present),
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                },
                {
                    label: 'Terlambat',
                    data: chartData.map(item => item.late),
                    backgroundColor: 'rgba(234, 179, 8, 0.5)',
                    borderColor: 'rgb(234, 179, 8)',
                    borderWidth: 1
                },
                {
                    label: 'Absen',
                    data: chartData.map(item => item.absent),
                    backgroundColor: 'rgba(239, 68, 68, 0.5)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush