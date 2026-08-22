<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <button id="sidebarToggle" class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="flex-shrink-0 flex items-center ml-2 md:ml-0">
                    <span class="text-xl font-bold text-green-600">🏪 Kantor Sayur</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                <span class="px-2 py-1 text-xs rounded-full {{ auth()->user()->role === 'owner' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ auth()->user()->role === 'owner' ? 'Owner' : 'Karyawan' }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>