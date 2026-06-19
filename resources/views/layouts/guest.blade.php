<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SLS Blog'))</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-100 to-white py-5 border-b">
        <div class="max-w-6xl mx-auto px-4 flex items-center gap-3">
            <a href="{{ route('blog.index') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">SLS</div>
                <div>
                    <h1 class="text-xl font-bold leading-none">
                        <span class="text-purple-500">SLS</span><span class="text-blue-500">BLOG</span>
                    </h1>
                    <p class="text-gray-500 text-xs">CRAFTING BRIGHTER FUTURES</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Auth card -->
    <div class="flex items-center justify-center min-h-[calc(100vh-80px)] py-10 px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-purple-500 to-blue-500"></div>
                <div class="p-8">
                    {{ $slot }}
                </div>
            </div>
            <p class="text-center text-xs text-gray-400 mt-4">
                &copy; {{ date('Y') }} Scholarly Language Services
            </p>
        </div>
    </div>

</body>
</html>
