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
    class="min-h-screen overflow-hidden bg-slate-50 text-slate-800"
    x-data="{ sidebarOpen: false, userOpen: false }"
    x-cloak
>
    <div class="flex h-screen overflow-hidden">

        {{-- Mobile Overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white transition-transform duration-300 ease-in-out lg:static"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            {{-- Logo --}}
            <div class="flex h-20 items-center justify-between border-b border-slate-100 px-6">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-emerald-50 p-2">
                        <img
                            src="{{ asset('image/smkn1cerme.png') }}"
                            alt="Logo SMKN 1 CERME"
                            class="h-7 w-7 object-contain"
                        >
                    </div>

                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">
                            Lantera
                        </h1>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                            Digital Library
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="text-slate-400 transition hover:text-slate-600 lg:hidden"
                    aria-label="Tutup menu"
                >
                    <i class="fas fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
                <div class="mb-4 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
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
                                    ? 'bg-emerald-50/70 font-medium text-emerald-700'
                                    : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'
                                }}"
                            >
                                <i class="fas fa-shopping-cart"></i>
                                <span>Keranjang Peminjaman</span>

                                @if(isset($cartCount) && $cartCount > 0)
                                    <span class="ml-auto rounded-full bg-emerald-500 px-1.5 py-0.5 text-[10px] text-white">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>

                            <a
                                href="{{ route('peminjam.loan.index') }}"
                                class="sub-link
                                {{ request()->routeIs(['peminjam.loan.index', 'loans.*'])
                                    ? 'bg-emerald-50/70 font-medium text-emerald-700'
                                    : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'
                                }}"
                            >
                                <i class="fas fa-clock-rotate-left"></i>
                                <span>Riwayat Peminjaman</span>
                            </a>

                            <a
                                href="{{ route('siswa.denda.index') }}"
                                class="sub-link
                                {{ request()->routeIs('siswa.denda.index')
                                    ? 'bg-emerald-50/70 font-medium text-emerald-700'
                                    : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'
                                }}"
                            >
                                <i class="fas fa-wallet"></i>
                                <span>Denda Saya</span>

                                @if(isset($dendaCount) && $dendaCount > 0)
                                    <span class="ml-auto rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] text-white">
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
                                    ? 'bg-emerald-50/70 font-medium text-emerald-700'
                                    : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'
                                }}"
                            >
                                <i class="fas fa-pen"></i>
                                <span>Input Buku</span>
                            </a>

                            <a
                                href="{{ route('siswa.pinjamkelas.index') }}"
                                class="sub-link
                                {{ request()->routeIs('siswa.pinjamkelas.index')
                                    ? 'bg-emerald-50/70 font-medium text-emerald-700'
                                    : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'
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
                <div class="border-t border-slate-100 p-4">
                    <div class="flex cursor-pointer items-center gap-3 rounded-xl p-3 transition-colors hover:bg-slate-50">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="truncate text-xs text-slate-500">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                    </div>
                </div>
            @endauth
        </aside>

        {{-- Content Wrapper --}}
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

            {{-- Header --}}
            <header class="sticky top-0 z-30 flex h-16 items-center border-b border-slate-200 bg-white px-4 md:px-6">
                <div class="flex w-full min-w-0 items-center justify-between gap-4">

                    <div class="flex min-w-0 items-center gap-3 md:gap-4">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-emerald-600 lg:hidden"
                            aria-label="Buka menu"
                        >
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <div class="min-w-0">
                            <h2 class="truncate text-base font-semibold text-slate-800 sm:text-lg md:text-xl">
                                @yield('title', 'Dashboard')
                            </h2>

                            @auth
                                <p class="hidden truncate text-xs text-slate-500 sm:block">
                                    Selamat datang kembali, {{ Auth::user()->name }}
                                </p>
                            @else
                                <p class="hidden truncate text-xs text-slate-500 sm:block">
                                    Sistem Informasi Perpustakaan Digital
                                </p>
                            @endauth
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">

                        @auth
                            {{-- User Dropdown --}}
                            <div class="relative">
                                <button
                                    type="button"
                                    @click="userOpen = !userOpen"
                                    class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-100 focus:outline-none"
                                >
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>

                                    <div class="hidden text-left md:block">
                                        <p class="max-w-32 truncate text-sm font-semibold leading-tight text-slate-700 lg:max-w-44">
                                            {{ Auth::user()->name }}
                                        </p>
                                        <p class="truncate text-xs text-slate-400">
                                            {{ Auth::user()->role }}
                                        </p>
                                    </div>

                                    <i class="fas fa-chevron-down hidden text-xs text-slate-400 md:block"></i>
                                </button>

                                <div
                                    x-show="userOpen"
                                    @click.away="userOpen = false"
                                    x-transition
                                    class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-slate-100 bg-white py-1 shadow-lg"
                                >
                                    <div class="border-b border-slate-100 px-4 py-3">
                                        <p class="text-xs text-slate-500">
                                            Masuk sebagai
                                        </p>
                                        <p class="truncate text-sm font-medium text-slate-700">
                                            {{ Auth::user()->email }}
                                        </p>
                                    </div>

                                    <a
                                        href="{{ route('peminjam.kartu-anggota') }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 transition hover:bg-slate-50"
                                    >
                                        <i class="fas fa-id-card w-4 text-slate-400"></i>
                                        Kartu Anggota
                                    </a>

                                    <div class="my-1 border-t border-slate-100"></div>

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="flex w-full items-center gap-3 px-4 py-2 text-sm text-red-600 transition hover:bg-red-50"
                                        >
                                            <i class="fas fa-sign-out-alt w-4 text-red-400"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            {{-- Login Button --}}
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 transition hover:bg-emerald-600 hover:shadow-md"
                            >
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                        @endauth

                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
                <div class="mx-auto max-w-7xl">
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