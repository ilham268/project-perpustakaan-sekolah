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

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }

        .sub-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .sub-link i {
            width: 16px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body
    class="bg-slate-50 text-slate-800"
    x-data="{ sidebarOpen: false, dropdownOpen: false }"
    x-cloak
>
    <div class="flex h-screen overflow-hidden">

        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <aside
            class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 rounded-xl">
                        <img
                            src="{{ asset('image/smkn1cerme.png') }}"
                            alt="Logo SMKN 1 CERME"
                            class="w-7 h-7 object-contain"
                        >
                    </div>

                    <div>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                            SMKN 1 CERME
                        </h1>

                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">
                            Perpustakaan Digital
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="lg:hidden text-slate-400 hover:text-slate-600 transition"
                >
                    <i class="fas fa-xmark text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-3">
                    Menu Utama
                </div>

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="nav-link
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    }}"
                >
                    <i class="fas fa-house {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span>Dashboard</span>
                </a>

                <div x-data="{ open: {{ request()->routeIs(['users.*', 'books.*', 'categories.*']) ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="nav-link w-full justify-between
                        {{ request()->routeIs(['users.*', 'books.*', 'categories.*'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        }}"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-layer-group {{ request()->routeIs(['users.*', 'books.*', 'categories.*']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Manajemen</span>
                        </div>

                        <i
                            class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                        ></i>
                    </button>

                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a
                            href="{{ route('users.index') }}"
                            class="sub-link
                            {{ request()->routeIs('users.*')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-users"></i>
                            <span>Kelola User</span>
                        </a>

                        <a
                            href="{{ route('categories.index') }}"
                            class="sub-link
                            {{ request()->routeIs('categories.*')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-tag"></i>
                            <span>Kategori Buku</span>
                        </a>

                        <a
                            href="{{ route('books.index') }}"
                            class="sub-link
                            {{ request()->routeIs('books.*')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-book"></i>
                            <span>Kelola Buku</span>
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs(['admin.peminjaman.create', 'admin.peminjaman.index', 'admin.peminjaman.riwayat']) ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="nav-link w-full justify-between
                        {{ request()->routeIs(['admin.peminjaman.create', 'admin.peminjaman.index', 'admin.peminjaman.riwayat'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        }}"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-book-open {{ request()->routeIs(['admin.peminjaman.create', 'admin.peminjaman.index', 'admin.peminjaman.riwayat']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Peminjaman</span>
                        </div>

                        <i
                            class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                        ></i>
                    </button>

                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a
                            href="{{ route('admin.peminjaman.create') }}"
                            class="sub-link
                            {{ request()->routeIs('admin.peminjaman.create')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-pen-to-square"></i>
                            <span>Input Peminjaman Buku</span>
                        </a>

                        <a
                            href="{{ route('admin.peminjaman.index') }}"
                            class="sub-link
                            {{ request()->routeIs('admin.peminjaman.index')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-list-check"></i>
                            <span>Daftar Peminjaman</span>
                        </a>

                        <a
                            href="{{ route('admin.peminjaman.riwayat') }}"
                            class="sub-link
                            {{ request()->routeIs('admin.peminjaman.riwayat')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-clock-rotate-left"></i>
                            <span>Riwayat Peminjaman</span>
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs(['admin.pinjamkelas.*', 'admin.pinjamkelas.kategori', 'admin.pinjamkelas.buku', 'admin.pinjamkelas.kelas']) ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="nav-link w-full justify-between
                        {{ request()->routeIs(['admin.pinjamkelas.*', 'admin.pinjamkelas.kategori', 'admin.pinjamkelas.buku', 'admin.pinjamkelas.kelas'])
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        }}"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-people-group {{ request()->routeIs(['admin.pinjamkelas.*', 'admin.pinjamkelas.kategori', 'admin.pinjamkelas.buku', 'admin.pinjamkelas.kelas']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Peminjaman Kelas</span>
                        </div>

                        <i
                            class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                        ></i>
                    </button>

                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a
                            href="{{ route('admin.pinjamkelas.kategori') }}"
                            class="sub-link
                            {{ request()->routeIs('admin.pinjamkelas.kategori')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-tag"></i>
                            <span>Kategori Buku</span>
                        </a>

                        <a
                            href="{{ route('admin.pinjamkelas.kelas') }}"
                            class="sub-link
                            {{ request()->routeIs('admin.pinjamkelas.kelas')
                                ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                            }}"
                        >
                            <i class="fas fa-school"></i>
                            <span>Kelas Pinjam</span>
                        </a>
                    </div>
                </div>

                <a
                    href="{{ route('admin.denda.index') }}"
                    class="nav-link
                    {{ request()->routeIs('admin.denda.*')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    }}"
                >
                    <i class="fas fa-wallet {{ request()->routeIs('admin.denda.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span>Denda</span>
                </a>

                <a
                    href="{{ route('admin.guest-book.index') }}"
                    class="nav-link
                    {{ request()->routeIs('admin.guest-book.*')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    }}"
                >
                    <i class="fas fa-door-open {{ request()->routeIs('admin.guest-book.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span>Buku Tamu</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-100">
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>

                    <div class="flex-1 min-w-0">
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
            <header class="h-16 bg-white border-b border-slate-200 flex items-center px-4 md:px-6 sticky top-0 z-30">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="lg:hidden text-slate-500 hover:text-emerald-600 hover:bg-slate-100 p-2 rounded-lg transition"
                        >
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <div>
                            <h2 class="text-lg md:text-xl font-semibold text-slate-800">
                                @yield('title', 'Dashboard')
                            </h2>

                            <p class="hidden sm:block text-xs text-slate-500">
                                Sistem Administrasi Perpustakaan
                            </p>
                        </div>
                    </div>

                    <div class="relative">
                        <button
                            type="button"
                            @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center gap-2 focus:outline-none hover:bg-slate-100 p-1.5 rounded-xl transition"
                        >
                            <div class="w-9 h-9 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>

                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-slate-700 leading-tight">
                                    {{ Auth::user()->name }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    {{ Auth::user()->role }}
                                </p>
                            </div>

                            <i class="fas fa-chevron-down text-slate-400 text-xs hidden md:block"></i>
                        </button>

                        <div
                            x-show="dropdownOpen"
                            @click.away="dropdownOpen = false"
                            x-transition
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
                        >
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-xs text-slate-500">
                                    Signed in as
                                </p>

                                <p class="text-sm font-medium text-slate-700 truncate">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf

                                <button
                                    type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition"
                                >
                                    <i class="fas fa-sign-out-alt w-4 text-red-400"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>