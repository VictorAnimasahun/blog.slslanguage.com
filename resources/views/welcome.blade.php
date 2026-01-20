<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SLS Blog') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white dark:bg-gray-900">
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold text-gray-900 dark:text-white">SLS Blog</span>
                </div>
                <div class="flex gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Login</a>
                        <a href="{{ route('register') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-8">Welcome to SLS Blog</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-12">Explore our latest posts and stories.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Blog posts will go here -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Coming Soon</h2>
                <p class="text-gray-600 dark:text-gray-400">Blog posts will appear here once published.</p>
            </div>
        </div>
    </main>
</body>
</html>