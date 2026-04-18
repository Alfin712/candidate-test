<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CLT Toolbox Manager')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        @include('manager.partials.navbar')

        @if (session('status'))
            <div class="bg-emerald-50 border-b border-emerald-200 text-emerald-800 px-4 py-2 text-sm">
                <div class="max-w-7xl mx-auto">{{ session('status') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-b border-red-200 text-red-800 px-4 py-2 text-sm">
                <div class="max-w-7xl mx-auto">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-gray-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-xs text-gray-500 flex justify-between">
                <span>&copy; {{ date('Y') }} CLT Toolbox Manager</span>
                <span>v1.0</span>
            </div>
        </footer>
    </div>
</body>
</html>
