@extends('layouts.peminjam')

@section('title', 'Katalog Buku')
@section('page-title', 'Katalog Buku')

@section('content')

    {{-- Flash Messages --}}
    @if(session('success'))
        <x-flash-message type="success" />
    @endif

    @if(session('error'))
        <x-flash-message type="error" message="{{ session('error') }}" />
    @endif

    <div
        x-data="{
            showDetail: false,
            selectedBook: {}
        }"
        class="space-y-6"
    >

        {{-- Hero Section --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-6 shadow-md shadow-emerald-100/70 md:px-7 md:py-7">

            {{-- Decorative Shape --}}
            <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

            {{-- Logo --}}
            <div class="pointer-events-none absolute right-5 top-5 opacity-20">
                <img
                    src="{{ asset('image/logoTrans.png') }}"
                    alt="Lantera"
                    class="h-24 w-auto object-contain"
                >
            </div>

            <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                        Temukan Buku Favoritmu
                    </h1>

                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                        Jelajahi koleksi buku dari berbagai kategori. Pinjam, baca, dan kembangkan wawasanmu.
                    </p>
                </div>

                <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">
                    <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                        <p class="text-xs font-semibold text-emerald-50">
                            Total Buku
                        </p>

                        <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                            {{ $books->total() }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                        <p class="text-xs font-semibold text-emerald-50">
                            Kategori
                        </p>

                        <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                            {{ $categories->count() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Search Bar & Filter --}}
            <form
                method="GET"
                action="{{ route('peminjam.list-buku') }}"
                class="relative z-10 mt-6 rounded-2xl bg-white/15 p-3 ring-1 ring-white/20 backdrop-blur-md"
            >
                <div class="flex flex-col gap-3 lg:flex-row">
                    <div class="relative min-w-0 flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari judul buku atau penulis..."
                            autocomplete="off"
                            class="w-full rounded-xl border border-white/40 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 shadow-sm outline-none transition focus:border-emerald-200 focus:ring-4 focus:ring-white/30"
                        >
                    </div>

                    <select
                        name="category_id"
                        class="w-full rounded-xl border border-white/40 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-emerald-200 focus:ring-4 focus:ring-white/30 lg:w-56"
                    >
                        <option value="">Semua Kategori</option>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50"
                    >
                        <i class="fas fa-search text-xs"></i>
                        <span>Cari</span>
                    </button>
                </div>
            </form>

        </div>

        {{-- Books Section --}}
        <div class="space-y-4">

            {{-- Section Header --}}
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">
                        Koleksi Terbaru
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Buku-buku yang baru saja ditambahkan.
                    </p>
                </div>

                @if(request('search') || request('category_id'))
                    <a
                        href="{{ route('peminjam.list-buku') }}"
                        class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        <i class="fas fa-rotate-left text-[10px]"></i>
                        Reset Filter
                    </a>
                @endif
            </div>

            {{-- Books Grid --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6">
                @forelse($books as $book)
                    @php
                        $availableCount = $book->availableItems()->count();
                        $bookImage = $book->foto
                            ? Storage::url($book->foto)
                            : 'https://via.placeholder.com/200x280/10b981/ffffff?text=No+Image';
                    @endphp

                    {{-- Book Card --}}
                    <div class="group overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">

                        {{-- Book Image --}}
                        <div class="relative h-44 overflow-hidden bg-slate-100 sm:h-48">
                            <img
                                src="{{ $bookImage }}"
                                alt="{{ $book->judul }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            >

                            {{-- Stock Badge --}}
                            <div class="absolute right-2 top-2">
                                @if($availableCount > 0)
                                    <span class="inline-flex items-center rounded-full bg-emerald-500 px-2 py-1 text-[10px] font-bold text-white shadow-sm">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-500 px-2 py-1 text-[10px] font-bold text-white shadow-sm">
                                        Habis
                                    </span>
                                @endif
                            </div>

                            {{-- Hover Overlay --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-slate-950/50 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <button
                                    type="button"
                                    @click="selectedBook = {
                                        foto: '{{ $bookImage }}',
                                        judul: @js($book->judul),
                                        penulis: @js($book->penulis),
                                        penerbit: @js($book->penerbit),
                                        tahun: @js($book->tahun),
                                        kategori: @js($book->category->nama_kategori ?? '-'),
                                        stok: {{ $availableCount }},
                                        id: {{ $book->id }},
                                        synopsis: @js($book->synopsis ?? '-')
                                    }; showDetail = true"
                                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-xs font-bold text-slate-800 shadow-sm transition hover:bg-emerald-500 hover:text-white"
                                >
                                    <i class="fas fa-eye text-xs"></i>
                                    Detail
                                </button>
                            </div>
                        </div>

                        {{-- Book Info --}}
                        <div class="p-3">

                            {{-- Category --}}
                            <p class="mb-2 truncate text-[11px] font-semibold text-emerald-600">
                                {{ $book->category->nama_kategori ?? '-' }}
                            </p>

                            {{-- Title --}}
                            <h3
                                class="line-clamp-2 min-h-[34px] text-sm font-extrabold leading-tight text-slate-900 transition group-hover:text-emerald-600"
                                title="{{ $book->judul }}"
                            >
                                {{ $book->judul }}
                            </h3>

                            {{-- Author --}}
                            <p class="mt-1 truncate text-xs text-slate-400">
                                {{ $book->penulis ?? 'Penulis tidak tersedia' }}
                            </p>

                            {{-- Footer --}}
                            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                                    <i class="fas fa-box text-[10px] text-emerald-500"></i>
                                    {{ $availableCount }} Stok
                                </div>

                                @auth
                                    <form action="{{ route('cart.store', $book->id) }}" method="POST">
                                        @csrf

                                        <button
                                            type="submit"
                                            title="Tambah ke Keranjang"
                                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 transition hover:bg-emerald-100"
                                        >
                                            <i class="fas fa-shopping-cart text-xs"></i>
                                        </button>
                                    </form>
                                @else
                                    <a
                                        href="{{ route('login') }}"
                                        title="Login untuk Meminjam"
                                        class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 transition hover:bg-emerald-100"
                                    >
                                        <i class="fas fa-shopping-cart text-xs"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                            <i class="fas fa-book-open text-2xl"></i>
                        </div>

                        <p class="mt-4 text-base font-bold text-slate-700">
                            Belum ada buku
                        </p>

                        <p class="mt-1 text-sm text-slate-400">
                            Belum ada buku yang tersedia saat ini.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($books->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $books->links() }}
                </div>
            @endif

        </div>

        {{-- Floating Cart Button --}}
        @auth
            <div class="fixed bottom-6 right-6 z-50">
                <a
                    href="{{ route('cart.index') }}"
                    class="relative flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl shadow-emerald-200 transition hover:-translate-y-1 hover:bg-emerald-600"
                >
                    <i class="fas fa-shopping-cart text-xl"></i>

                    @php
                        $cartCount = \App\Models\Cart::where('user_id', Auth::id())->count();
                    @endphp

                    @if($cartCount > 0)
                        <span class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white ring-2 ring-white">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </div>
        @endauth

        {{-- Modal Detail Buku --}}
        @include('peminjam.book.partials.detail-modal')

    </div>

@endsection