<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Lantera Petugas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,450;9..144,550;9..144,650;9..144,700&family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="icon" href="{{ asset('image/logoRounded.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #0E2620;
            --forest: #0F3D2E;
            --emerald: #147A54;
            --emerald-deep: #0C5E40;
            --emerald-tint: #E9F3EE;
            --paper: #FAF8F3;
            --sand: #F1ECE0;
            --gold: #AC8752;
            --text: #1B2420;
            --muted: #6E7770;
            --hairline: #E7E2D6;
        }

        [x-cloak] { display: none !important; }

        html, body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }

        .font-display {
            font-family: 'Fraunces', 'Georgia', serif;
            font-optical-sizing: auto;
        }

        .font-mono-stat {
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.01em;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--paper); }
        ::-webkit-scrollbar-thumb { background: #D9D2C1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--emerald); }

        /* Sidebar navigation --------------------------------------------------- */
        .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            letter-spacing: 0.01em;
            transition: background-color .18s ease, color .18s ease;
        }

        .nav-link i {
            width: 18px;
            text-align: center;
            font-size: 16px;
        }

        .nav-link.is-active::before {
            content: '';
            position: absolute;
            left: -16px;
            top: 8px;
            bottom: 8px;
            width: 3px;
            border-radius: 0 4px 4px 0;
            background: var(--gold);
        }

        .sub-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 13px;
            transition: background-color .18s ease, color .18s ease;
        }

        .sub-link i {
            width: 14px;
            text-align: center;
            font-size: 12px;
        }

        .catalog-eyebrow {
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-size: 10px;
            letter-spacing: 0.14em;
        }
    </style>
</head>

<body
    class="min-h-screen overflow-hidden text-[var(--text)]"
    style="background-color: var(--paper);"
    x-data="{ sidebarOpen: false, userDropdownOpen: false }"
    x-cloak
>
    <div class="flex h-screen overflow-hidden">

        {{-- Mobile Overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-[var(--ink)]/40 backdrop-blur-[2px] lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-[var(--hairline)] bg-white transition-transform duration-300 ease-in-out lg:static"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            {{-- Logo --}}
            <div class="flex h-20 items-center justify-between border-b border-[var(--hairline)] px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-[var(--hairline)] bg-[var(--emerald-tint)]">
                        <img
                            src="{{ asset('image/smkn1cerme.png') }}"
                            alt="Logo SMKN 1 CERME"
                            class="h-6 w-6 object-contain"
                        >
                    </div>

                    <div class="leading-tight">
                        <h1 class="font-display text-lg font-semibold tracking-tight text-[var(--forest)]">
                            SMKN 1 CERME
                        </h1>
                        <p class="catalog-eyebrow uppercase text-[var(--gold)]">
                            Petugas&nbsp;Perpustakaan
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="text-[var(--muted)] transition hover:text-[var(--forest)] lg:hidden"
                >
                    <i class="fas fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
                <div class="catalog-eyebrow mb-4 px-3 uppercase text-[var(--muted)]">
                    Menu Utama
                </div>

                {{-- Dashboard --}}
                <a
                    href="{{ route('petugas.dashboard') }}"
                    class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'is-active' : '' }}
                    {{ request()->routeIs('petugas.dashboard')
                        ? 'bg-[var(--emerald-tint)] text-[var(--forest)]'
                        : 'text-[var(--text)]/80 hover:bg-[var(--sand)]/60 hover:text-[var(--forest)]'
                    }}"
                >
                    <i class="fas fa-gauge-high {{ request()->routeIs('petugas.dashboard') ? 'text-[var(--emerald)]' : 'text-[var(--muted)]' }}"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Peminjaman --}}
                <div x-data="{ open: {{ request()->routeIs(['peminjaman.*']) ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="nav-link w-full justify-between {{ request()->routeIs(['peminjaman.*']) ? 'is-active' : '' }}
                        {{ request()->routeIs(['peminjaman.*'])
                            ? 'bg-[var(--emerald-tint)] text-[var(--forest)]'
                            : 'text-[var(--text)]/80 hover:bg-[var(--sand)]/60 hover:text-[var(--forest)]'
                        }}"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-book-open {{ request()->routeIs(['peminjaman.*']) ? 'text-[var(--emerald)]' : 'text-[var(--muted)]' }}"></i>
                            <span>Peminjaman</span>
                        </div>

                        <i
                            class="fas fa-chevron-down text-[11px] text-[var(--muted)] transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                        ></i>
                    </button>

                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1 border-l border-[var(--hairline)] pl-3">
                        <a
                            href="{{ route('peminjaman.index') }}"
                            class="sub-link
                            {{ request()->routeIs('peminjaman.index')
                                ? 'bg-[var(--emerald-tint)] font-medium text-[var(--forest)]'
                                : 'text-[var(--muted)] hover:bg-[var(--sand)]/60 hover:text-[var(--emerald-deep)]'
                            }}"
                        >
                            <i class="fas fa-list-ul"></i>
                            <span>Daftar Peminjaman</span>
                        </a>

                        <a
                            href="{{ route('peminjaman.riwayat') }}"
                            class="sub-link
                            {{ request()->routeIs('peminjaman.riwayat')
                                ? 'bg-[var(--emerald-tint)] font-medium text-[var(--forest)]'
                                : 'text-[var(--muted)] hover:bg-[var(--sand)]/60 hover:text-[var(--emerald-deep)]'
                            }}"
                        >
                            <i class="fas fa-clock-rotate-left"></i>
                            <span>Pengembalian</span>
                        </a>
                    </div>
                </div>

                {{-- Peminjaman Kelas --}}
                <div x-data="{ open: {{ request()->routeIs(['petugas.pinjamkelas.*']) ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="nav-link w-full justify-between {{ request()->routeIs(['petugas.pinjamkelas.*']) ? 'is-active' : '' }}
                        {{ request()->routeIs(['petugas.pinjamkelas.*'])
                            ? 'bg-[var(--emerald-tint)] text-[var(--forest)]'
                            : 'text-[var(--text)]/80 hover:bg-[var(--sand)]/60 hover:text-[var(--forest)]'
                        }}"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chalkboard-user {{ request()->routeIs(['petugas.pinjamkelas.*']) ? 'text-[var(--emerald)]' : 'text-[var(--muted)]' }}"></i>
                            <span>Peminjaman Kelas</span>
                        </div>

                        <i
                            class="fas fa-chevron-down text-[11px] text-[var(--muted)] transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                        ></i>
                    </button>

                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1 border-l border-[var(--hairline)] pl-3">
                        <a
                            href="{{ route('petugas.pinjamkelas.kategori') }}"
                            class="sub-link
                            {{ request()->routeIs('petugas.pinjamkelas.kategori')
                                ? 'bg-[var(--emerald-tint)] font-medium text-[var(--forest)]'
                                : 'text-[var(--muted)] hover:bg-[var(--sand)]/60 hover:text-[var(--emerald-deep)]'
                            }}"
                        >
                            <i class="fas fa-tag"></i>
                            <span>Kategori Buku</span>
                        </a>

                        <a
                            href="{{ route('petugas.pinjamkelas.kelas') }}"
                            class="sub-link
                            {{ request()->routeIs('petugas.pinjamkelas.kelas')
                                ? 'bg-[var(--emerald-tint)] font-medium text-[var(--forest)]'
                                : 'text-[var(--muted)] hover:bg-[var(--sand)]/60 hover:text-[var(--emerald-deep)]'
                            }}"
                        >
                            <i class="fas fa-list"></i>
                            <span>Kelas Pinjam</span>
                        </a>
                    </div>
                </div>

                {{-- Denda --}}
                <a
                    href="{{ route('denda.index') }}"
                    class="nav-link {{ request()->routeIs('denda.*') ? 'is-active' : '' }}
                    {{ request()->routeIs('denda.*')
                        ? 'bg-[var(--emerald-tint)] text-[var(--forest)]'
                        : 'text-[var(--text)]/80 hover:bg-[var(--sand)]/60 hover:text-[var(--forest)]'
                    }}"
                >
                    <i class="fas fa-wallet {{ request()->routeIs('denda.*') ? 'text-[var(--emerald)]' : 'text-[var(--muted)]' }}"></i>
                    <span>Denda</span>
                </a>
            </nav>

            {{-- User Summary --}}
            <div class="border-t border-[var(--hairline)] p-4">
                <div class="flex cursor-pointer items-center gap-3 rounded-xl p-3 transition-colors hover:bg-[var(--sand)]/50">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-[var(--hairline)] bg-[var(--emerald-tint)] font-mono-stat text-[13px] font-semibold text-[var(--forest)]">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-[var(--text)]">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="truncate text-xs text-[var(--muted)]">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Content Wrapper --}}
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

            {{-- Header --}}
            <header class="sticky top-0 z-30 flex h-16 items-center border-b border-[var(--hairline)] bg-white/90 px-4 backdrop-blur md:px-6">
                <div class="flex w-full items-center justify-between">

                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="rounded-lg p-2 text-[var(--muted)] transition hover:bg-[var(--sand)]/60 hover:text-[var(--emerald-deep)] lg:hidden"
                        >
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <div>
                            <h2 class="font-display text-lg font-semibold tracking-tight text-[var(--forest)] md:text-xl">
                                @yield('title', 'Dashboard')
                            </h2>
                            <p class="hidden text-xs text-[var(--muted)] sm:block">
                                Selamat datang kembali, {{ Auth::user()->name }}
                            </p>
                        </div>
                    </div>

                    {{-- User Dropdown --}}
                    <div class="relative">
                        <button
                            type="button"
                            @click="userDropdownOpen = !userDropdownOpen"
                            class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-[var(--sand)]/60 focus:outline-none"
                        >
                            <div class="flex h-9 w-9 items-center justify-center rounded-full border border-[var(--hairline)] bg-[var(--emerald-tint)] font-mono-stat text-[13px] font-semibold text-[var(--forest)]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>

                            <div class="hidden text-left md:block">
                                <p class="text-sm font-semibold leading-tight text-[var(--text)]">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="catalog-eyebrow uppercase text-[var(--gold)]">
                                    {{ Auth::user()->role }}
                                </p>
                            </div>

                            <i class="fas fa-chevron-down hidden text-xs text-[var(--muted)] md:block"></i>
                        </button>

                        <div
                            x-show="userDropdownOpen"
                            @click.away="userDropdownOpen = false"
                            x-transition
                            class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-[var(--hairline)] bg-white py-1 shadow-xl shadow-[var(--ink)]/5"
                        >
                            <div class="border-b border-[var(--hairline)] px-4 py-3">
                                <p class="catalog-eyebrow uppercase text-[var(--muted)]">Signed in as</p>
                                <p class="truncate text-sm font-medium text-[var(--text)]">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>

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
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6" style="background-color: var(--paper);">
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