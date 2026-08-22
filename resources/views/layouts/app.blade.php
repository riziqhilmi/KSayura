<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kantor Sayur - Absensi UMKM')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-transition {
            transition: all 0.3s ease;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="min-h-screen bg-gray-50">
        @auth
            @include('partials.navbar')
            <div class="flex">
                @include('partials.sidebar')
                <main class="flex-1 p-4 md:p-6 lg:p-8">
                    @yield('content')
                </main>
            </div>
        @else
            <main>
                @yield('content')
            </main>
        @endauth
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>