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
        
        .menu-transition {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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

<body class="bg-slate-50" x-data="{ sidebarOpen: true, dropdownOpen: false }" x-cloak>

    <div class="flex h-screen overflow-hidden">

        <aside
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-white border-r border-slate-200 shadow-sm flex flex-col transition-all duration-300 z-20"
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
                        <h1 class="text-base font-bold text-slate-800 leading-tight">
                            Lantera
                        </h1>
                        <p class="text-[10px] font-medium text-emerald-600 uppercase tracking-wide">
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
                    href="{{ route('admin.dashboard') }}"
                    class="nav-link menu-transition
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-home {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>

                <!-- Manajemen Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['users.*', 'books.*', 'categories.*']) ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        class="nav-link w-full justify-between menu-transition
                        {{ request()->routeIs(['users.*', 'books.*', 'categories.*'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                        :class="!sidebarOpen && 'justify-center'"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-layer-group {{ request()->routeIs(['users.*', 'books.*', 'categories.*']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span x-show="sidebarOpen">Manajemen</span>
                        </div>
                        <i x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('users.*')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-users w-4 text-center"></i>
                            <span>Kelola User</span>
                        </a>
                        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('categories.*')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-tag w-4 text-center"></i>
                            <span>Kategori Buku</span>
                        </a>
                        <a href="{{ route('books.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('books.*')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-book w-4 text-center"></i>
                            <span>Kelola Buku</span>
                        </a>
                    </div>
                </div>

                <!-- Peminjaman Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['admin.peminjaman.index', 'admin.peminjaman.riwayat']) ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        class="nav-link w-full justify-between menu-transition
                        {{ request()->routeIs(['admin.peminjaman.index', 'admin.peminjaman.riwayat'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                        :class="!sidebarOpen && 'justify-center'"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-book-open {{ request()->routeIs(['admin.peminjaman.index', 'admin.peminjaman.riwayat']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span x-show="sidebarOpen">Peminjaman</span>
                        </div>
                        <i x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('admin.peminjaman.index')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-list w-4 text-center"></i>
                            <span>Daftar Peminjaman</span>
                        </a>
                        <a href="{{ route('admin.peminjaman.riwayat') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('admin.peminjaman.riwayat')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-history w-4 text-center"></i>
                            <span>Riwayat Peminjaman</span>
                        </a>
                    </div>
                </div>

                <!-- Peminjaman Kelas Dropdown -->
                <div x-data="{ open: {{ request()->routeIs(['admin.pinjamkelas.*', 'admin.pinjamkelas.kategori', 'admin.pinjamkelas.buku', 'admin.pinjamkelas.kelas']) ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        class="nav-link w-full justify-between menu-transition
                        {{ request()->routeIs(['admin.pinjamkelas.*', 'admin.pinjamkelas.kategori', 'admin.pinjamkelas.buku', 'admin.pinjamkelas.kelas'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                        }}"
                        :class="!sidebarOpen && 'justify-center'"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-users {{ request()->routeIs(['admin.pinjamkelas.*', 'admin.pinjamkelas.kategori', 'admin.pinjamkelas.buku', 'admin.pinjamkelas.kelas']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span x-show="sidebarOpen">Peminjaman Kelas</span>
                        </div>
                        <i x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.pinjamkelas.kategori') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('admin.pinjamkelas.kategori')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-tag w-4 text-center"></i>
                            <span>Kategori Buku</span>
                        </a>
                        <a href="{{ route('admin.pinjamkelas.kelas') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ request()->routeIs('admin.pinjamkelas.kelas')
                                ? 'text-emerald-700 bg-emerald-50/50 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}">
                            <i class="fas fa-school w-4 text-center"></i>
                            <span>Kelas Pinjam</span>
                        </a>
                    </div>
                </div>

                <!-- Denda -->
                <a
                    href="{{ route('admin.denda.index') }}"
                    class="nav-link menu-transition
                    {{ request()->routeIs('admin.denda.*')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-money-bill-wave {{ request()->routeIs('admin.denda.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Denda</span>
                </a>

                <!-- Buku Tamu -->
                <a
                    href="{{ route('admin.guest-book.index') }}"
                    class="nav-link menu-transition
                    {{ request()->routeIs('admin.guest-book.*')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600'
                    }}"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-address-book {{ request()->routeIs('admin.guest-book.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span x-show="sidebarOpen">Buku Tamu</span>
                </a>

                <div x-show="sidebarOpen" class="my-3 border-t border-slate-100"></div>

                <!-- Pengaturan -->
                <a
                    href="#"
                    class="nav-link menu-transition text-slate-600 hover:bg-slate-50 hover:text-emerald-600"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <i class="fas fa-cog text-slate-400"></i>
                    <span x-show="sidebarOpen">Pengaturan</span>
                </a>

            </nav>

            <div class="p-3 border-t border-slate-100">
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer" :class="!sidebarOpen && 'justify-center'">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-semibold text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div x-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="bg-white border-b border-slate-200 h-16 flex items-center px-6 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-emerald-600 hover:bg-slate-100 p-2 rounded-lg transition">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">
                                @yield('title', 'Dashboard')
                            </h2>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 focus:outline-none hover:bg-slate-100 p-1.5 rounded-lg transition">
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-400">{{ Auth::user()->role }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-slate-400 text-xs hidden md:block"></i>
                            </button>
                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-xs text-slate-500">Signed in as</p>
                                    <p class="text-sm font-medium text-slate-700 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition">
                                    <i class="fas fa-user-circle w-4 text-slate-400"></i> Profile
                                </a>
                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition">
                                    <i class="fas fa-cog w-4 text-slate-400"></i> Settings
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-sign-out-alt w-4 text-red-400"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-slate-50 p-6">
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