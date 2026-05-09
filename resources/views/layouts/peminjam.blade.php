<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Lantera</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('image/logoRounded.png') }}" type="image/png">

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
        
        .menu-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .menu-active {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #059669;
            position: relative;
        }
        
        .menu-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 0 4px 4px 0;
        }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }
        
        /* Nav link style - icon dan teks sejajar */
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

<body class="bg-gradient-to-br from-slate-50 via-white to-slate-100/30" x-data="{ sidebarOpen: window.innerWidth >= 1024, userOpen: false }" x-cloak>

    <div class="flex h-screen overflow-hidden">

        <!-- Overlay Mobile -->
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0 lg:w-72' : '-translate-x-full lg:translate-x-0 lg:w-20'"
            class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-100 shadow-xl lg:shadow-sm flex flex-col transition-all duration-300 ease-in-out lg:static"
        >

            <!-- Logo Section - Logo selalu terlihat -->
            <div class="h-20 flex items-center px-4 border-b border-slate-100">
                <div
                    class="flex items-center w-full"
                    :class="sidebarOpen ? 'justify-start gap-3' : 'justify-center'"
                >
                    <!-- Logo SMKN 1 CERME - SELALU TERLIHAT -->
                    <img
                        src="{{ asset('image/smkn1cerme.png') }}"
                        alt="Logo SMKN 1 CERME"
                        class="h-8 w-auto"
                    >
                    <!-- Teks - Hanya muncul saat sidebar terbuka -->
                    <div x-show="sidebarOpen" x-transition class="flex-1">
                        <h1 class="text-base font-bold bg-gradient-to-r from-emerald-600 to-emerald-500 bg-clip-text text-transparent leading-tight">
                            Lantera
                        </h1>
                        <p class="text-[10px] font-semibold text-emerald-500 uppercase tracking-wider">
                            Digital Library
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3">
                    MENU UTAMA
                </div>

                <!-- Daftar Buku -->
                <a
                    href="{{ route('peminjam.list-buku') }}"
                    class="nav-link menu-item
                    {{ request()->routeIs('peminjam.list-buku')
                        ? 'menu-active font-semibold'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-book-open {{ request()->routeIs('peminjam.list-buku') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Daftar Buku</span>
                </a>

                @guest
                <!-- Buku Tamu untuk Guest -->
                <a
                    href="{{ route('guest-book.create') }}"
                    class="nav-link menu-item
                    {{ request()->routeIs('guest-book.*')
                        ? 'menu-active font-semibold'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-address-book {{ request()->routeIs('guest-book.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Buku Tamu</span>
                </a>
                @endguest

                @auth
                <!-- Peminjaman Dropdown untuk User Login -->
                <div x-data="{ open: {{ request()->routeIs(['peminjam.loan.*', 'cart.*', 'loans.*']) ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        class="nav-link w-full justify-between menu-item
                        {{ request()->routeIs(['peminjam.loan.*', 'cart.*', 'loans.*'])
                            ? 'menu-active'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                        :class="!sidebarOpen && 'justify-center'"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-book-open-reader {{ request()->routeIs(['peminjam.loan.*', 'cart.*', 'loans.*']) ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                            <span x-show="sidebarOpen">Peminjaman</span>
                        </div>
                        <i x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="fas fa-chevron-down text-xs transition-transform duration-200 text-slate-400"></i>
                    </button>
                    
                    <div x-show="open && sidebarOpen" x-collapse class="ml-7 mt-1 space-y-1">
                        <a
                            href="{{ route('cart.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('cart.*')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-shopping-cart w-4"></i>
                            <span>Keranjang Peminjaman</span>
                            @if(isset($cartCount) && $cartCount > 0)
                            <span class="ml-auto text-[10px] bg-emerald-500 text-white px-1.5 py-0.5 rounded-full">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a
                            href="{{ route('peminjam.loan.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs(['peminjam.loan.index', 'loans.*'])
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-history w-4"></i>
                            <span>Riwayat Peminjaman</span>
                        </a>
                    </div>
                </div>
                @endauth

            </nav>

            <!-- Bottom Menu -->
            <div class="mt-auto border-t border-slate-100 p-3">
                <!-- Panduan -->
                <a
                    href="{{ route('peminjam.guides.index') }}"
                    class="nav-link menu-item
                    {{ request()->routeIs('peminjam.guides.*')
                        ? 'menu-active font-semibold'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-circle-question {{ request()->routeIs('peminjam.guides.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Panduan</span>
                </a>

                @auth
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="nav-link w-full text-red-600 hover:bg-red-50 menu-item" :class="!sidebarOpen && 'justify-center'">
                        <i class="fas fa-sign-out-alt text-red-400"></i>
                        <span x-show="sidebarOpen">Logout</span>
                    </button>
                </form>
                @endauth
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Navbar -->
            <header class="glass-nav border-b border-slate-200 h-16 flex items-center px-4 sm:px-6 shadow-sm sticky top-0 z-20">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 p-2 rounded-xl transition-all duration-200"
                        >
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div class="hidden md:block">
                            <h2 class="text-xl font-bold text-slate-800">
                                @yield('title', 'Dashboard')
                            </h2>
                            @auth
                            <p class="text-xs text-slate-400 mt-0.5">
                                Selamat datang kembali, {{ Auth::user()->name }}
                            </p>
                            @endauth
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        @auth
                        <div class="relative">
                            <button
                                @click="userOpen = !userOpen"
                                class="flex items-center gap-2 focus:outline-none hover:bg-slate-100 p-1.5 rounded-xl transition-all duration-200"
                            >
                                <!-- Avatar + Nama User -->
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-[10px] text-emerald-600 font-medium">
                                        {{ Auth::user()->role }}
                                    </p>
                                </div>
                                <i class="fas fa-chevron-down text-slate-400 text-xs hidden lg:block"></i>
                            </button>
                            <div
                                x-show="userOpen"
                                @click.away="userOpen = false"
                                x-transition
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50 overflow-hidden"
                            >
                                <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wide">Signed in as</p>
                                    <p class="text-sm font-semibold text-slate-700 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('peminjam.kartu-anggota') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-colors duration-200">
                                    <i class="fas fa-id-card w-4 text-emerald-400"></i>
                                    <span>Kartu Anggota</span>
                                </a>
                                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-colors duration-200">
                                    <i class="fas fa-user-edit w-4 text-emerald-400"></i>
                                    <span>Edit Profil</span>
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt w-4 text-red-400"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 rounded-xl shadow-sm transition">
                                <i class="fas fa-user-plus mr-2"></i>Register
                            </a>
                        </div>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gradient-to-br from-slate-50 via-white to-slate-100/20">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

</body>
</html>