@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Pengaturan Toko</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('owner.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Store Info -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Toko</h3>
                </div>

                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
                    <input type="text" 
                           name="store_name" 
                           id="store_name" 
                           value="{{ old('store_name', $settings->store_name ?? 'Kantor Sayur') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('store_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                    <input type="text" 
                           name="phone" 
                           id="phone" 
                           value="{{ old('phone', $settings->phone ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea name="address" 
                              id="address" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('address', $settings->address ?? '') }}</textarea>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $settings->email ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Location Settings -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Lokasi Absensi</h3>
                </div>

                <div>
                    <label for="check_in_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="number" 
                           name="check_in_latitude" 
                           id="check_in_latitude" 
                           step="any"
                           value="{{ old('check_in_latitude', $settings->check_in_latitude ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="-6.2088">
                </div>

                <div>
                    <label for="check_in_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="number" 
                           name="check_in_longitude" 
                           id="check_in_longitude" 
                           step="any"
                           value="{{ old('check_in_longitude', $settings->check_in_longitude ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="106.8456">
                </div>

                <div>
                    <label for="check_in_radius" class="block text-sm font-medium text-gray-700 mb-2">Radius Absensi (meter)</label>
                    <input type="number" 
                           name="check_in_radius" 
                           id="check_in_radius" 
                           value="{{ old('check_in_radius', $settings->check_in_radius ?? 100) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Office Hours -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Jam Kerja</h3>
                </div>

                <div>
                    <label for="office_start_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Masuk</label>
                    <input type="time" 
                           name="office_start_time" 
                           id="office_start_time" 
                           value="{{ old('office_start_time', $settings->office_start_time ?? '08:00') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label for="office_end_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Pulang</label>
                    <input type="time" 
                           name="office_end_time" 
                           id="office_end_time" 
                           value="{{ old('office_end_time', $settings->office_end_time ?? '17:00') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Overtime Settings -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Lembur</h3>
                </div>

                <div>
                    <label for="overtime_calculation" class="block text-sm font-medium text-gray-700 mb-2">Perhitungan Lembur</label>
                    <select name="overtime_calculation" 
                            id="overtime_calculation" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="hourly" {{ old('overtime_calculation', $settings->overtime_calculation ?? 'hourly') === 'hourly' ? 'selected' : '' }}>Per Jam</option>
                        <option value="daily" {{ old('overtime_calculation', $settings->overtime_calculation ?? 'hourly') === 'daily' ? 'selected' : '' }}>Per Hari</option>
                    </select>
                </div>

                <div>
                    <label for="overtime_minutes" class="block text-sm font-medium text-gray-700 mb-2">Batas Toleransi Keterlambatan (menit)</label>
                    <input type="number" 
                           name="overtime_minutes" 
                           id="overtime_minutes" 
                           value="{{ old('overtime_minutes', $settings->overtime_minutes ?? 30) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection