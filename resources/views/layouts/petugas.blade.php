<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Lantera Petugas</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="icon" href="{{ asset('image/logoRounded.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
        
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
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
        
        .nav-item {
            transition: all 0.2s ease;
        }
        
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
        
        .logout-btn {
            cursor: pointer;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-white to-slate-50" x-data="{ sidebarOpen: true, userDropdownOpen: false }" x-cloak>

    <div class="flex h-screen overflow-hidden">

        <aside
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-white border-r border-slate-200 shadow-lg flex flex-col transition-all duration-300 ease-in-out z-20"
        >

            <div class="h-20 flex items-center px-4 border-b border-slate-100">
                <div
                    class="flex items-center w-full"
                    :class="sidebarOpen ? 'justify-start gap-3' : 'justify-center'"
                >
                    <img
                        src="{{ asset('image/smkn1cerme.png') }}"
                        alt="Logo SMKN 1 CERME"
                        class="h-8 w-auto"
                    >
                    <div x-show="sidebarOpen" x-transition class="flex-1">
                        <h1 class="text-base font-bold text-emerald-600 leading-tight">
                            Lantera
                        </h1>
                        <p class="text-[9px] font-semibold text-emerald-500 uppercase tracking-wider">
                            SMKN 1 CERME
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3">
                    MENU UTAMA
                </div>

                <!-- Dashboard -->
                <a
                    href="{{ route('petugas.dashboard') }}"
                    class="nav-link nav-item
                    {{ request()->routeIs('petugas.dashboard')
                        ? 'nav-active text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-tachometer-alt {{ request()->routeIs('petugas.dashboard') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>

                <!-- Peminjaman Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['peminjaman.*']) ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        class="nav-link w-full justify-between nav-item
                        {{ request()->routeIs(['peminjaman.*'])
                            ? 'nav-active text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                        :class="!sidebarOpen && 'justify-center'"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-book-open {{ request()->routeIs(['peminjaman.*']) ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                            <span x-show="sidebarOpen">Peminjaman</span>
                        </div>
                        <i x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="fas fa-chevron-down text-xs transition-transform duration-200 text-slate-400"></i>
                    </button>

                    <div x-show="open && sidebarOpen" x-collapse class="ml-7 mt-1 space-y-1">
                        <a href="{{ route('peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('peminjaman.index')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-list-ul w-4"></i>
                            <span>Daftar Peminjaman</span>
                        </a>
                        <a href="{{ route('peminjaman.riwayat') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('peminjaman.riwayat')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-history w-4"></i>
                            <span>Pengembalian</span>
                        </a>
                    </div>
                </div>

                <!-- Peminjaman Kelas Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['petugas.pinjamkelas.*']) ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        class="nav-link w-full justify-between nav-item
                        {{ request()->routeIs(['petugas.pinjamkelas.*'])
                            ? 'nav-active text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                        :class="!sidebarOpen && 'justify-center'"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chalkboard-user {{ request()->routeIs(['petugas.pinjamkelas.*']) ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                            <span x-show="sidebarOpen">Peminjaman Kelas</span>
                        </div>
                        <i x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="fas fa-chevron-down text-xs transition-transform duration-200 text-slate-400"></i>
                    </button>

                    <div x-show="open && sidebarOpen" x-collapse class="ml-7 mt-1 space-y-1">
                        <a href="{{ route('petugas.pinjamkelas.kategori') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('petugas.pinjamkelas.kategori')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-tag w-4"></i>
                            <span>Kategori Buku</span>
                        </a>
                        <a href="{{ route('petugas.pinjamkelas.kelas') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('petugas.pinjamkelas.kelas')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-list w-4"></i>
                            <span>Kelas Pinjam</span>
                        </a>
                    </div>
                </div>

                <!-- Denda -->
                <a
                    href="{{ route('denda.index') }}"
                    class="nav-link nav-item
                    {{ request()->routeIs('denda.*')
                        ? 'nav-active text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-money-bill-wave {{ request()->routeIs('denda.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Denda</span>
                </a>

                <div x-show="sidebarOpen" class="my-3 border-t border-slate-100"></div>

                <button 
                    type="button"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="nav-link w-full nav-item text-red-600 hover:bg-red-50 group logout-btn"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-sign-out-alt text-red-400 group-hover:text-red-500"></i>
                    <span x-show="sidebarOpen">Logout</span>
                </button>

            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 h-16 flex items-center px-6 shadow-sm sticky top-0 z-10">
                <div class="flex items-center justify-between w-full">

                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 p-2 rounded-xl transition-all duration-200">
                            <i class="fas fa-bars text-lg"></i>
                        </button>

                        <div class="hidden md:block">
                            <h1 class="text-lg font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
                            <p class="text-xs text-slate-400 mt-0.5">Selamat datang kembali, {{ Auth::user()->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center gap-2 focus:outline-none hover:bg-slate-100 p-1.5 rounded-xl transition-all duration-200">
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-emerald-600 font-medium">{{ Auth::user()->role }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-slate-400 text-xs hidden lg:block"></i>
                            </button>

                            <div x-show="userDropdownOpen" @click.away="userDropdownOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wide">Signed in as</p>
                                    <p class="text-sm font-semibold text-slate-700 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-colors duration-200">
                                    <i class="fas fa-user-circle w-4 text-emerald-400"></i>
                                    <span>Profile Saya</span>
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200 logout-btn">
                                    <i class="fas fa-sign-out-alt w-4 text-red-400"></i>
                                    <span>Logout</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-gradient-to-br from-slate-50 via-white to-slate-50/50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>