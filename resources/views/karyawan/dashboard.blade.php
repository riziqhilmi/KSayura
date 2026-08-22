@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
        <h1 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="mt-2">{{ $employee->position }} - {{ $employee->employee_code }}</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Hadir Bulan Ini</div>
            <div class="text-2xl font-bold text-green-600">{{ $totalPresent }} hari</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Terlambat</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $totalLate }} hari</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Cuti</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalLeave }} hari</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Sisa Cuti</div>
            <div class="text-2xl font-bold text-purple-600">{{ $leaveQuota - $totalLeavesTaken }} hari</div>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Absensi Hari Ini</h3>
        @if($todayAttendance)
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm 
                        @if($todayAttendance->status === 'present') bg-green-100 text-green-800
                        @elseif($todayAttendance->status === 'late') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $todayAttendance->status_label }}
                    </span>
                    <div class="text-sm text-gray-600">
                        Check-in: {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                        @if($todayAttendance->check_out)
                            | Check-out: {{ $todayAttendance->check_out->format('H:i') }}
                        @endif
                    </div>
                </div>
                @if(!$todayAttendance->check_out && $todayAttendance->check_in)
                    <form action="{{ route('karyawan.attendances.checkout') }}" method="POST" class="mt-2 sm:mt-0">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Check-out
                        </button>
                    </form>
                @endif
            </div>
        @else
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                <span class="text-gray-500">Belum absen hari ini</span>
                <form action="{{ route('karyawan.attendances.checkin') }}" method="POST" enctype="multipart/form-data" class="mt-2 sm:mt-0">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <button type="submit" onclick="getLocation()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        Check-in
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Recent Attendances -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">7 Hari Terakhir</h3>
        <div class="grid grid-cols-7 gap-2">
            @foreach($recentAttendances as $attendance)
            <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-xs text-gray-500">{{ $attendance->date->format('d/m') }}</div>
                <div class="mt-1 text-lg 
                    @if($attendance->status === 'present') text-green-600
                    @elseif($attendance->status === 'late') text-yellow-600
                    @elseif($attendance->status === 'leave') text-blue-600
                    @else text-red-600 @endif">
                    @if($attendance->status === 'present') ✓
                    @elseif($attendance->status === 'late') ⏰
                    @elseif($attendance->status === 'leave') 📅
                    @else ✕ @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Pending Leaves -->
    @if($pendingLeaves->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Pengajuan Cuti Pending</h3>
        <div class="space-y-2">
            @foreach($pendingLeaves as $leave)
            <div class="flex justify-between items-center border-b pb-2">
                <div>
                    <div class="font-medium">{{ $leave->type_label }}</div>
                    <div class="text-sm text-gray-500">{{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }}</div>
                </div>
                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                document.querySelector('form').submit();
            },
            function(error) {
                alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.');
                console.error(error);
            }
        );
    } else {
        alert('Browser tidak mendukung GPS');
    }
}
</script>
@endpush
@endsection