<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — SLS Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('head')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-gray-900 text-white flex items-center justify-between px-6 py-3 shadow">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-purple-500 rounded flex items-center justify-center font-bold text-sm">SLS</div>
            <span class="font-semibold tracking-wide">Admin Panel</span>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('blog.index') }}" class="text-gray-300 hover:text-white">
                <i class="fas fa-external-link-alt mr-1"></i>View Site
            </a>
            <span class="text-gray-400">{{ auth()->user()->display_name ?? auth()->user()->first_name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="text-gray-300 hover:text-white">Logout</button>
            </form>
        </div>
    </header>

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside class="w-56 bg-gray-800 text-white flex flex-col py-6 shrink-0">
            <nav class="space-y-1 px-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                    <i class="fas fa-tachometer-alt w-4"></i> Dashboard
                </a>
                <a href="{{ route('admin.posts.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('admin.posts*') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                    <i class="fas fa-file-alt w-4"></i> Posts
                </a>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('admin.users*') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                    <i class="fas fa-users w-4"></i> Users
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('admin.categories*') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                    <i class="fas fa-folder w-4"></i> Categories
                </a>
                @endif
                <a href="{{ route('admin.comments.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('admin.comments*') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                    <i class="fas fa-comments w-4"></i> Comments
                    @php $pending = \App\Models\Comment::where('status','pending')->count() @endphp
                    @if($pending > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $pending }}</span>
                    @endif
                </a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-8 overflow-auto">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

@stack('scripts')
</body>
</html>
