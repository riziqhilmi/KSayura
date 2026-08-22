<aside id="sidebar" class="sidebar-transition bg-white shadow-md w-64 min-h-screen fixed md:relative z-20 transform -translate-x-full md:translate-x-0">
    <div class="p-4">
        <nav class="space-y-1">
            @if(auth()->user()->role === 'owner')
                <a href="{{ route('owner.dashboard') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('owner.dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-700' }}">
                    <span class="mr-3">📊</span> Dashboard
                </a>
                <a href="{{ route('owner.employees.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('owner.employees.*') ? 'bg-green-50 text-green-700' : 'text-gray-700' }}">
                    <span class="mr-3">👥</span> Karyawan
                </a>
                <a href="{{ route('owner.attendances.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('owner.attendances.*') ? 'bg-green-50 text-green-700' : 'text-gray-700' }}">
                    <span class="mr-3">📋</span> Absensi
                </a>
                <a href="{{ route('owner.leaves.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('owner.leaves.*') ? 'bg-green-50 text-green-700' : 'text-gray-700' }}">
                    <span class="mr-3">📅</span> Cuti
                </a>
                <a href="{{ route('owner.salaries.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('owner.salaries.*') ? 'bg-green-50 text-green-700' : 'text-gray-700' }}">
                    <span class="mr-3">💰</span> Gaji
                </a>
                <a href="{{ route('owner.settings.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('owner.settings.*') ? 'bg-green-50 text-green-700' : 'text-gray-700' }}">
                    <span class="mr-3">⚙️</span> Pengaturan
                </a>
            @else
                <a href="{{ route('karyawan.dashboard') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('karyawan.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                    <span class="mr-3">📊</span> Dashboard
                </a>
                <a href="{{ route('karyawan.attendances.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('karyawan.attendances.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                    <span class="mr-3">📋</span> Absensi
                </a>
                <a href="{{ route('karyawan.leaves.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('karyawan.leaves.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                    <span class="mr-3">📅</span> Cuti
                </a>
                <a href="{{ route('karyawan.salary.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('karyawan.salary.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                    <span class="mr-3">💰</span> Gaji Saya
                </a>
            @endif
        </nav>
    </div>
</aside>

<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    });
</script>