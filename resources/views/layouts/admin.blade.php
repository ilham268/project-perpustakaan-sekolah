<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Lantera Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('image/logoRounded.png') }}" type="image/png">

    <style>
        [x-cloak] {
            display: none !important;
        }
        
        /* Custom scrollbar yang bersih */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }
        
        /* Smooth transitions */
        .menu-transition {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Card hover effect */
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-slate-50" x-data="{ sidebarOpen: true }" x-cloak>

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar Putih Bersih -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-white border-r border-slate-200 shadow-sm flex flex-col transition-all duration-300 z-20"
        >

            <!-- Logo Section -->
            <div class="h-20 flex items-center px-5 border-b border-slate-100">
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
                            x-show="sidebarOpen"
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
                        <h1 class="text-lg font-bold text-slate-800 leading-tight">
                            Lantera
                        </h1>
                        <p class="text-[10px] font-medium text-emerald-600 uppercase tracking-wide">
                            SMKN 1 CERME
                        </p>
                    </div>

                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

                <!-- Dashboard -->
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg menu-transition group
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                >
                    <i class="fas fa-home w-5 text-lg"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">
                        Dashboard
                    </span>
                </a>

                <!-- Manajemen Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['users.*', 'books.*', 'categories.*']) ? 'true' : 'false' }} }">

                    <button
                        @click="open = !open"
                        class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg menu-transition group
                        {{ request()->routeIs(['users.*', 'books.*', 'categories.*'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                    >

                        <div class="flex items-center gap-3">
                            <i class="fas fa-layer-group w-5 text-lg"></i>
                            <span x-show="sidebarOpen" class="font-medium text-sm">
                                Manajemen
                            </span>
                        </div>

                        <i
                            x-show="sidebarOpen"
                            :class="open ? 'rotate-180' : ''"
                            class="fas fa-chevron-down text-xs transition-transform duration-200"
                        ></i>

                    </button>

                    <div
                        x-show="open && sidebarOpen"
                        x-collapse
                        class="ml-7 mt-1 space-y-0.5"
                    >
                        <a
                            href="{{ route('users.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm menu-transition
                            {{ request()->routeIs('users.*')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-users w-4 text-xs"></i>
                            <span>Kelola User</span>
                        </a>

                        <a
                            href="{{ route('categories.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm menu-transition
                            {{ request()->routeIs('categories.*')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-tag w-4 text-xs"></i>
                            <span>Kategori Buku</span>
                        </a>

                        <a
                            href="{{ route('books.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm menu-transition
                            {{ request()->routeIs('books.*')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-book w-4 text-xs"></i>
                            <span>Kelola Buku</span>
                        </a>
                    </div>

                </div>

                <!-- Peminjaman Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['admin.peminjaman.*']) ? 'true' : 'false' }} }">

                    <button
                        @click="open = !open"
                        class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg menu-transition group
                        {{ request()->routeIs(['admin.peminjaman.*'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                    >

                        <div class="flex items-center gap-3">
                            <i class="fas fa-book-open w-5 text-lg"></i>
                            <span x-show="sidebarOpen" class="font-medium text-sm">
                                Peminjaman
                            </span>
                        </div>

                        <i
                            x-show="sidebarOpen"
                            :class="open ? 'rotate-180' : ''"
                            class="fas fa-chevron-down text-xs transition-transform duration-200"
                        ></i>

                    </button>

                    <div
                        x-show="open && sidebarOpen"
                        x-collapse
                        class="ml-7 mt-1 space-y-0.5"
                    >
                        <a
                            href="{{ route('admin.peminjaman.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm menu-transition
                            {{ request()->routeIs('admin.peminjaman.index')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-list w-4 text-xs"></i>
                            <span>Daftar Peminjaman</span>
                        </a>

                        <a
                            href="{{ route('admin.peminjaman.riwayat') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm menu-transition
                            {{ request()->routeIs('admin.peminjaman.riwayat')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-history w-4 text-xs"></i>
                            <span>Riwayat</span>
                        </a>
                    </div>

                </div>

                <!-- Denda -->
                <a
                    href="{{ route('admin.denda.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg menu-transition group
                    {{ request()->routeIs('admin.denda.*')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                >
                    <i class="fas fa-money-bill-wave w-5 text-lg"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">
                        Denda
                    </span>
                </a>

                <!-- Buku Tamu -->
                <a
                    href="{{ route('admin.guest-book.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg menu-transition group
                    {{ request()->routeIs('admin.guest-book.*')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                >
                    <i class="fas fa-address-book w-5 text-lg"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">
                        Buku Tamu
                    </span>
                </a>

                <!-- Divider -->
                <div x-show="sidebarOpen" class="my-4 border-t border-slate-100"></div>

                <!-- Pengaturan -->
                <a
                    href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg menu-transition group text-slate-600 hover:bg-slate-50 hover:text-emerald-600"
                >
                    <i class="fas fa-cog w-5 text-lg"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">
                        Pengaturan
                    </span>
                </a>

            </nav>

            <!-- Sidebar Footer -->
            <div x-show="sidebarOpen" class="p-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 text-center">
                    © 2024 Lantera
                </p>
            </div>

        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Navbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center px-6 sticky top-0 z-10 shadow-sm">

                <div class="flex items-center justify-between w-full">

                    <div class="flex items-center gap-4">
                        <!-- Toggle Sidebar Button -->
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="text-slate-500 hover:text-emerald-600 hover:bg-slate-100 p-2 rounded-lg transition-all duration-200"
                        >
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <!-- Page Title -->
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">
                                @yield('title', 'Dashboard')
                            </h2>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center gap-3">

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ dropdownOpen: false }">

                            <button
                                @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center gap-2 focus:outline-none hover:bg-slate-100 p-1.5 rounded-lg transition"
                            >

                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>

                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-slate-700">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ Auth::user()->role }}
                                    </p>
                                </div>

                                <i class="fas fa-chevron-down text-slate-400 text-xs hidden md:block"></i>

                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                x-show="dropdownOpen"
                                @click.away="dropdownOpen = false"
                                x-transition
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
                            >

                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-xs text-slate-500">Signed in as</p>
                                    <p class="text-sm font-medium text-slate-700 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="border-t border-slate-100 my-1"></div>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-sign-out-alt w-4 text-red-400"></i>
                                        Logout
                                    </button>
                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-6">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

</body>
</html>