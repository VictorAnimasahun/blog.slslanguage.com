<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SLS Blog')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sls-header {
            background: linear-gradient(to right, #f3f4f6, #ffffff);
        }
        .sls-nav {
            background-color: #1e9fd8;
        }
        .section-title {
            color: #1e9fd8;
            border-bottom: 3px solid #ec4899;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="sls-header py-6">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center text-white font-bold">
                    SLS
                </div>
                <div>
                    <h1 class="text-2xl font-bold"><span class="text-purple-500">SLS</span><span class="text-blue-500">BLOG</span></h1>
                    <p class="text-gray-600 text-xs">CRAFTING BRIGHTER FUTURES</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="#" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white hover:bg-blue-500">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="#" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white hover:bg-blue-500">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sls-nav">
        <div class="max-w-6xl mx-auto px-4 flex gap-8">
            <a href="{{ route('blog.index') }}" class="text-white py-4 hover:bg-blue-600 px-3">Home</a>
            <a href="#" class="text-white py-4 hover:bg-blue-600 px-3">About</a>
            <a href="#" class="text-white py-4 hover:bg-blue-600 px-3">Contact</a>
            <a href="#" class="text-white py-4 hover:bg-blue-600 px-3">Log in</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <p>&copy; 2026 Scholarly Language Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>