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
    x-data="{ sidebarOpen: false, userOpen: false }"
    x-cloak
>
    <div class="flex h-screen overflow-hidden">

        {{-- Mobile Overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            {{-- Logo --}}
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
                            Lantera
                        </h1>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">
                            Digital Library
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

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-3">
                    Menu Utama
                </div>

                {{-- Daftar Buku --}}
                <a
                    href="{{ route('peminjam.list-buku') }}"
                    class="nav-link
                    {{ request()->routeIs('peminjam.list-buku')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    }}"
                >
                    <i class="fas fa-book-open {{ request()->routeIs('peminjam.list-buku') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span>Daftar Buku</span>
                </a>

                @guest
                    {{-- Buku Tamu --}}
                    <a
                        href="{{ route('guest-book.create') }}"
                        class="nav-link
                        {{ request()->routeIs('guest-book.*')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        }}"
                    >
                        <i class="fas fa-address-book {{ request()->routeIs('guest-book.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                        <span>Buku Tamu</span>
                    </a>
                @endguest

                @auth
                    {{-- Peminjaman --}}
                    <div x-data="{ open: {{ request()->routeIs(['peminjam.loan.*', 'cart.*', 'loans.*', 'siswa.denda.index']) ? 'true' : 'false' }} }">
                        <button
                            type="button"
                            @click="open = !open"
                            class="nav-link w-full justify-between
                            {{ request()->routeIs(['peminjam.loan.*', 'cart.*', 'loans.*', 'siswa.denda.index'])
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                            }}"
                        >
                            <div class="flex items-center gap-3">
                                <i class="fas fa-book-open-reader {{ request()->routeIs(['peminjam.loan.*', 'cart.*', 'loans.*', 'siswa.denda.index']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                                <span>Peminjaman</span>
                            </div>

                            <i
                                class="fas fa-chevron-down text-xs transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"
                            ></i>
                        </button>

                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a
                                href="{{ route('cart.index') }}"
                                class="sub-link
                                {{ request()->routeIs('cart.*')
                                    ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                    : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                                }}"
                            >
                                <i class="fas fa-shopping-cart"></i>
                                <span>Keranjang Peminjaman</span>

                                @if(isset($cartCount) && $cartCount > 0)
                                    <span class="ml-auto text-[10px] bg-emerald-500 text-white px-1.5 py-0.5 rounded-full">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>

                            <a
                                href="{{ route('peminjam.loan.index') }}"
                                class="sub-link
                                {{ request()->routeIs(['peminjam.loan.index', 'loans.*'])
                                    ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                    : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                                }}"
                            >
                                <i class="fas fa-clock-rotate-left"></i>
                                <span>Riwayat Peminjaman</span>
                            </a>

                            <a
                                href="{{ route('siswa.denda.index') }}"
                                class="sub-link
                                {{ request()->routeIs('siswa.denda.index')
                                    ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                    : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                                }}"
                            >
                                <i class="fas fa-wallet"></i>
                                <span>Denda Saya</span>

                                @if(isset($dendaCount) && $dendaCount > 0)
                                    <span class="ml-auto text-[10px] bg-red-500 text-white px-1.5 py-0.5 rounded-full">
                                        {{ $dendaCount }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>

                    {{-- Peminjaman Kelas --}}
                    <div x-data="{ open: {{ request()->routeIs(['siswa.pinjamkelas.*']) ? 'true' : 'false' }} }">
                        <button
                            type="button"
                            @click="open = !open"
                            class="nav-link w-full justify-between
                            {{ request()->routeIs(['siswa.pinjamkelas.*'])
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                            }}"
                        >
                            <div class="flex items-center gap-3">
                                <i class="fas fa-chalkboard-user {{ request()->routeIs(['siswa.pinjamkelas.*']) ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                                <span>Peminjaman Kelas</span>
                            </div>

                            <i
                                class="fas fa-chevron-down text-xs transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"
                            ></i>
                        </button>

                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a
                                href="{{ route('siswa.pinjamkelas.input') }}"
                                class="sub-link
                                {{ request()->routeIs('siswa.pinjamkelas.input')
                                    ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                    : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                                }}"
                            >
                                <i class="fas fa-pen"></i>
                                <span>Input Buku</span>
                            </a>

                            <a
                                href="{{ route('siswa.pinjamkelas.index') }}"
                                class="sub-link
                                {{ request()->routeIs('siswa.pinjamkelas.index')
                                    ? 'text-emerald-700 bg-emerald-50/70 font-medium'
                                    : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'
                                }}"
                            >
                                <i class="fas fa-book"></i>
                                <span>Buku Pinjaman</span>
                            </a>
                        </div>
                    </div>
                @endauth

                {{-- Panduan --}}
                <div class="my-4 border-t border-slate-100"></div>

                <a
                    href="{{ route('peminjam.guides.index') }}"
                    class="nav-link
                    {{ request()->routeIs('peminjam.guides.*')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    }}"
                >
                    <i class="fas fa-circle-question {{ request()->routeIs('peminjam.guides.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span>Panduan</span>
                </a>
            </nav>

            {{-- User Summary --}}
            @auth
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
            @endauth
        </aside>

        {{-- Content Wrapper --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Header --}}
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

                            @auth
                                <p class="hidden sm:block text-xs text-slate-500">
                                    Selamat datang kembali, {{ Auth::user()->name }}
                                </p>
                            @else
                                <p class="hidden sm:block text-xs text-slate-500">
                                    Sistem Informasi Perpustakaan Digital
                                </p>
                            @endauth
                        </div>
                    </div>

                    <div class="flex items-center gap-2">

                        @auth
                            {{-- User Dropdown --}}
                            <div class="relative">
                                <button
                                    type="button"
                                    @click="userOpen = !userOpen"
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
                                    x-show="userOpen"
                                    @click.away="userOpen = false"
                                    x-transition
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
                                >
                                    <div class="px-4 py-3 border-b border-slate-100">
                                        <p class="text-xs text-slate-500">Signed in as</p>
                                        <p class="text-sm font-medium text-slate-700 truncate">
                                            {{ Auth::user()->email }}
                                        </p>
                                    </div>

                                    <a
                                        href="{{ route('peminjam.kartu-anggota') }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition"
                                    >
                                        <i class="fas fa-id-card w-4 text-slate-400"></i>
                                        Kartu Anggota
                                    </a>

                                    <div class="border-t border-slate-100 my-1"></div>

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
                        @else
                            {{-- Guest Buttons --}}
                            <div class="flex items-center gap-2">
                                <a
                                    href="{{ route('login') }}"
                                    class="px-4 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50 rounded-xl transition"
                                >
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login
                                </a>

                                <a
                                    href="{{ route('register') }}"
                                    class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition"
                                >
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Register
                                </a>
                            </div>
                        @endauth

                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Alpine Plugin Collapse harus sebelum Alpine core --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>