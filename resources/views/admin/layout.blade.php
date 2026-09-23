<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — SLS Blog</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Tighter heading/paragraph spacing than Tailwind Typography's own
           defaults -- kept byte-identical to layouts/app.blade.php's copy of
           this block, so the editor (here, wrapping #quill-editor in the
           same .prose classes as the published page) matches what a reader
           actually sees. If you change one, change the other. */
        .prose :where(h1, h2, h3, h4) { margin-top: 1em !important; margin-bottom: 0.4em !important; }
        .prose :where(p, ul, ol, blockquote, pre) { margin-top: 0.6em !important; margin-bottom: 0.6em !important; }
        .prose > :first-child { margin-top: 0 !important; }
        /* <main> scrolls independently of the sidebar (see .overflow-auto below), so on
           macOS/trackpads with auto-hiding overlay scrollbars it was easy not to notice
           the page could scroll at all -- writers didn't realize there was more below
           (or a way back up) until they happened to swipe. Forces a plain, always-visible
           scrollbar instead of relying on the OS's fade-in-when-scrolling default. */
        main.overflow-auto { scrollbar-width: auto; scrollbar-color: #94a3b8 #f1f5f9; }
        main.overflow-auto::-webkit-scrollbar { width: 14px; }
        main.overflow-auto::-webkit-scrollbar-track { background: #f1f5f9; }
        main.overflow-auto::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 7px; border: 3px solid #f1f5f9; }
    </style>
    @stack('head')
</head>
<!-- h-screen + overflow-hidden (was min-h-screen, which only sets a floor, not a
     ceiling): without a real height cap here, <main>'s own overflow-auto never
     actually engaged -- the whole PAGE grew taller and scrolled instead, taking
     the header, sidebar, and the post editor's toolbar along with it. That's why
     a sticky toolbar (and a visible scrollbar on the right container) didn't
     work until this line changed -- confirmed by scrolling a real page before
     and after. -->
<body class="bg-gray-100 h-screen overflow-hidden flex flex-col">

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

    <div class="flex flex-1 min-h-0">
        <!-- Sidebar -->
        <aside class="w-56 bg-gray-800 text-white flex flex-col py-6 shrink-0 overflow-y-auto">
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
        <main class="flex-1 min-h-0 p-8 overflow-auto">
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
