<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Lantera Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="icon" href="{{ asset('image/logoRounded.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }
        
        /* Smooth transitions */
        .nav-item {
            transition: all 0.2s ease;
        }
        
        /* Active menu indicator */
        .nav-active {
            position: relative;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        }
        
        .nav-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 70%;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 0 4px 4px 0;
        }
        
        /* Hover effect */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Logout button cursor */
        .logout-btn {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-white to-slate-50" x-data="{ sidebarOpen: true, userDropdownOpen: false }" x-cloak>

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-white border-r border-slate-200 shadow-lg flex flex-col transition-all duration-300 ease-in-out z-20"
        >

            <!-- Logo Section -->
            <div class="h-20 flex items-center px-5 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50/30">
                <div
                    class="flex items-center gap-2.5 w-full"
                    :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                >

                    <div class="flex items-center gap-2">
                        <img
                            x-show="sidebarOpen"
                            x-transition
                            src="{{ asset('image/smkn1cerme.png') }}"
                            alt="Logo SMKN 1 CERME"
                            class="h-7 w-auto"
                        >
                        <div
                            x-show="sidebarOpen && sidebarOpen"
                            x-transition
                            class="w-px h-6 bg-slate-200"
                        ></div>
                        <img
                            src="{{ asset('image/logoLantera.png') }}"
                            alt="Lantera Logo"
                            class="h-7 w-auto"
                        >
                    </div>

                    <div x-show="sidebarOpen" x-transition class="flex-1">
                        <h1 class="text-base font-bold bg-gradient-to-r from-emerald-600 to-emerald-500 bg-clip-text text-transparent leading-tight">
                            Lantera
                        </h1>
                        <p class="text-[9px] font-semibold text-emerald-500 uppercase tracking-wider">
                            SMKN 1 CERME
                        </p>
                    </div>

                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1.5">

                <!-- Dashboard -->
                <a
                    href="{{ route('petugas.dashboard') }}"
                    class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl group
                    {{ request()->routeIs('petugas.dashboard')
                        ? 'nav-active text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                >
                    <i class="fas fa-house w-5 text-lg {{ request()->routeIs('petugas.dashboard') ? 'text-emerald-500' : 'text-slate-400 group-hover:text-emerald-500' }}"></i>

                    <span x-show="sidebarOpen" class="font-medium text-sm">
                        Dashboard
                    </span>

                    <span x-show="!sidebarOpen" class="hidden">
                        Dashboard
                    </span>
                </a>

                <!-- Peminjaman Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['peminjaman.*']) ? 'true' : 'false' }} }">

                    <button
                        @click="open = !open"
                        class="nav-item flex items-center justify-between w-full px-3 py-2.5 rounded-xl group
                        {{ request()->routeIs(['peminjaman.*'])
                            ? 'nav-active text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                    >

                        <div class="flex items-center gap-3">
                            <i class="fas fa-book-open w-5 text-lg {{ request()->routeIs(['peminjaman.*']) ? 'text-emerald-500' : 'text-slate-400 group-hover:text-emerald-500' }}"></i>
                            <span x-show="sidebarOpen" class="font-medium text-sm">
                                Peminjaman
                            </span>
                        </div>

                        <i
                            x-show="sidebarOpen"
                            :class="open ? 'rotate-180' : ''"
                            class="fas fa-chevron-down text-xs transition-transform duration-200 text-slate-400"
                        ></i>

                    </button>

                    <div
                        x-show="open && sidebarOpen"
                        x-collapse
                        class="ml-8 mt-1 space-y-1"
                    >
                        <a
                            href="{{ route('peminjaman.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                            {{ request()->routeIs('peminjaman.index')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-list-ul w-3.5 text-xs"></i>
                            <span>Daftar Peminjaman</span>
                        </a>

                        <a
                            href="{{ route('peminjaman.riwayat') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                            {{ request()->routeIs('peminjaman.riwayat')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-history w-3.5 text-xs"></i>
                            <span>Riwayat Pengembalian</span>
                        </a>
                    </div>

                </div>

                <!-- Denda -->
                <a
                    href="{{ route('denda.index') }}"
                    class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl group
                    {{ request()->routeIs('denda.*')
                        ? 'nav-active text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                >
                    <i class="fas fa-money-bill-wave w-5 text-lg {{ request()->routeIs('denda.*') ? 'text-emerald-500' : 'text-slate-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">
                        Denda
                    </span>
                </a>

                <!-- Divider -->
                <div class="my-3 border-t border-slate-100"></div>

                <!-- Logout Button di Sidebar - Perbaikan -->
                <button 
                    type="button"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="nav-item flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50 group logout-btn"
                >
                    <i class="fas fa-sign-out-alt w-5 text-lg text-red-400 group-hover:text-red-500"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">
                        Logout
                    </span>
                </button>

            </nav>

        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Navbar -->
            <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 h-16 flex items-center px-6 shadow-sm sticky top-0 z-10">

                <div class="flex items-center justify-between w-full">

                    <div class="flex items-center gap-3">

                        <!-- Toggle Button -->
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 p-2 rounded-xl transition-all duration-200"
                        >
                            <i class="fas fa-bars text-lg"></i>
                        </button>

                        <!-- Breadcrumb / Page Title -->
                        <div class="hidden md:block">
                            <h1 class="text-lg font-semibold text-slate-800">
                                @yield('title', 'Dashboard')
                            </h1>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Selamat datang kembali, {{ Auth::user()->name }}
                            </p>
                        </div>

                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center gap-2">

                        <!-- User Dropdown -->
                        <div class="relative ml-2">

                            <button
                                @click="userDropdownOpen = !userDropdownOpen"
                                class="flex items-center gap-2 focus:outline-none hover:bg-slate-100 p-1.5 rounded-xl transition-all duration-200"
                            >

                                <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>

                                <div class="hidden lg:block text-left">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-shield-alt text-emerald-500 text-[9px]"></i>
                                        <p class="text-[10px] text-emerald-600 font-medium">
                                            {{ Auth::user()->role }}
                                        </p>
                                    </div>
                                </div>

                                <i class="fas fa-chevron-down text-slate-400 text-xs hidden lg:block"></i>

                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                x-show="userDropdownOpen"
                                @click.away="userDropdownOpen = false"
                                x-transition
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50 overflow-hidden"
                            >

                                <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wide">Signed in as</p>
                                    <p class="text-sm font-semibold text-slate-700 truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-colors duration-200">
                                    <i class="fas fa-user-circle w-4 text-emerald-400"></i>
                                    <span>Profile Saya</span>
                                </a>

                                <div class="border-t border-slate-100 my-1"></div>

                                <!-- Logout di Dropdown User - Perbaikan -->
                                <button 
                                    type="button"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200 logout-btn"
                                >
                                    <i class="fas fa-sign-out-alt w-4 text-red-400"></i>
                                    <span>Logout</span>
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gradient-to-br from-slate-50 via-white to-slate-50/50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

    <!-- Form Logout Global - Hanya SATU form untuk semua tombol logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Alpine.js with Collapse Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

</body>
</html>