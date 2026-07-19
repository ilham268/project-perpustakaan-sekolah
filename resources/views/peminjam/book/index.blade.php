@extends('layouts.peminjam')

@section('title', 'Katalog Buku Referensi')
@section('page-title', 'Katalog Buku Referensi')

@section('content')

@if(session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
        <span class="text-sm font-medium">
            {{ session('success') }}
        </span>
    </div>
@endif

@if(session('error'))
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
        <span class="text-sm font-medium">
            {{ session('error') }}
        </span>
    </div>
@endif

<div
    x-data="{
        showDetail: false,
        selectedBook: {}
    }"
    class="space-y-6"
>

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-7 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-8" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="pointer-events-none absolute right-6 top-6 hidden opacity-[0.15] sm:block">
            <img
                src="{{ asset('image/logoTrans.png') }}"
                alt="Lantera"
                class="h-24 w-auto object-contain"
            >
        </div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-2xl">
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Buku&nbsp;Referensi
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[34px]">
                    Temukan Buku Referensimu
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Katalog siswa hanya menampilkan buku referensi. Buku yang belum punya kode buku tidak bisa dipinjam.
                </p>
            </div>

            <div class="w-full shrink-0 sm:w-64">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur-md">
                    <p class="catalog-eyebrow uppercase text-white/70">
                        Total Referensi
                    </p>

                    <p class="font-mono-stat mt-1 text-[32px] font-semibold leading-none text-white">
                        {{ $books->total() }}
                    </p>

                    <p class="mt-2 text-xs font-medium text-white/70">
                        buku tersedia di katalog
                    </p>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <form
            method="GET"
            action="{{ route('peminjam.list-buku') }}"
            class="relative z-10 mt-6"
        >
            <div class="flex flex-col gap-2.5 rounded-2xl border border-white/15 bg-white/10 p-2.5 backdrop-blur-md sm:flex-row sm:items-center sm:gap-2">
                <div class="relative flex-1">
                    <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul buku atau penulis..."
                        autocomplete="off"
                        class="h-12 w-full min-w-0 rounded-xl border border-white/40 bg-white pl-11 pr-4 text-sm font-medium text-[var(--text)] shadow-sm outline-none transition placeholder:text-slate-400 focus:border-white focus:ring-4 focus:ring-white/25"
                    >
                </div>

                <button
                    type="submit"
                    class="inline-flex h-12 w-full items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-[var(--gold)] px-6 text-sm font-semibold text-white shadow-sm transition hover:brightness-95 focus:outline-none focus:ring-4 focus:ring-white/25 sm:w-auto"
                >
                    <i class="fas fa-search text-xs"></i>
                    Cari
                </button>
            </div>
        </form>
    </div>

    {{-- Koleksi --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-display text-xl font-semibold tracking-tight text-[var(--forest)]">
                    Koleksi Referensi Terbaru
                </h2>

                <p class="mt-1 text-sm text-[var(--muted)]">
                    Tampilan dibuat tanpa foto buku agar sesuai dengan data yang tersedia.
                </p>
            </div>

            @if(request('search'))
                <a
                    href="{{ route('peminjam.list-buku') }}"
                    class="inline-flex h-10 w-fit items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                >
                    <i class="fas fa-rotate-left text-xs"></i>
                    Reset Filter
                </a>
            @endif
        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($books as $book)
                @php
                    $availableCount = (int) ($book->stok_tersedia ?? $book->bookItems->filter(function ($item) {
                        return $item->status === 'available' && !empty($item->kode_buku);
                    })->count());
                @endphp

                <div class="group flex h-full flex-col overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[var(--emerald)]/30 hover:shadow-lg hover:shadow-[var(--emerald)]/10">
                    <div class="flex flex-1 flex-col p-4">

                        {{-- Top --}}
                        <div class="flex items-start justify-between gap-3 border-b border-dashed border-[var(--hairline)] pb-3">
                            <div class="min-w-0">
                                <p class="catalog-eyebrow font-semibold uppercase text-[var(--emerald-deep)]">
                                    Referensi
                                </p>

                                <h3
                                    class="font-display mt-2 line-clamp-2 text-[17px] font-semibold leading-snug text-[var(--text)] transition group-hover:text-[var(--emerald-deep)]"
                                    title="{{ $book->judul }}"
                                >
                                    {{ $book->judul }}
                                </h3>
                            </div>

                            <div class="shrink-0 text-right">
                                @if($availableCount > 0)
                                    <span class="inline-flex items-center rounded-full bg-[var(--emerald-tint)] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[var(--emerald-deep)]">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-red-600">
                                        Belum&nbsp;Ada
                                    </span>
                                @endif

                                <p class="font-mono-stat mt-1.5 text-lg font-semibold leading-none text-[var(--text)]">
                                    {{ $availableCount }}
                                </p>

                                <p class="text-[10px] uppercase tracking-wide text-[var(--muted)]">
                                    stok
                                </p>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="mt-3 grid flex-1 grid-cols-2 gap-x-3 gap-y-3 text-sm">
                            <div class="col-span-2">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-[var(--muted)]">
                                    Penulis
                                </p>

                                <p class="mt-0.5 line-clamp-1 font-medium text-[var(--text)]">
                                    {{ $book->penulis ?? 'Penulis tidak tersedia' }}
                                </p>
                            </div>

                            <div class="col-span-2">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-[var(--muted)]">
                                    Penerbit
                                </p>

                                <p class="mt-0.5 line-clamp-1 font-medium text-[var(--text)]">
                                    {{ $book->penerbit ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-[var(--muted)]">
                                    Tahun
                                </p>

                                <p class="font-mono-stat mt-0.5 font-medium text-[var(--text)]">
                                    {{ $book->tahun ?? '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="mt-4 flex items-center gap-2 border-t border-[var(--hairline)] pt-3">
                            <button
                                type="button"
                                @click="selectedBook = {
                                    id: {{ $book->id }},
                                    judul: @js($book->judul),
                                    penulis: @js($book->penulis ?? '-'),
                                    penerbit: @js($book->penerbit ?? '-'),
                                    tahun: @js($book->tahun ?? '-'),
                                    kategori: @js('Referensi'),
                                    stok: {{ $availableCount }},
                                    synopsis: @js($book->synopsis ?? '-')
                                }; showDetail = true"
                                class="inline-flex h-9 flex-1 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                            >
                                Detail
                            </button>

                            @auth
                                @if($availableCount > 0)
                                    <form action="{{ route('cart.store', $book->id) }}" method="POST" class="flex-1">
                                        @csrf

                                        <button
                                            type="submit"
                                            class="inline-flex h-9 w-full items-center justify-center rounded-lg bg-[var(--emerald-deep)] px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        >
                                            Tambah
                                        </button>
                                    </form>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex h-9 flex-1 cursor-not-allowed items-center justify-center rounded-lg bg-slate-200 px-3 text-sm font-semibold text-slate-400 shadow-sm"
                                    >
                                        Tidak Tersedia
                                    </button>
                                @endif
                            @else
                                @if($availableCount > 0)
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex h-9 flex-1 items-center justify-center rounded-lg bg-[var(--emerald-deep)] px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    >
                                        Login
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex h-9 flex-1 cursor-not-allowed items-center justify-center rounded-lg bg-slate-200 px-3 text-sm font-semibold text-slate-400 shadow-sm"
                                    >
                                        Tidak Tersedia
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-[var(--hairline)] bg-white px-6 py-16 text-center shadow-sm">
                    <p class="font-display text-base font-semibold text-[var(--text)]">
                        Belum ada buku Referensi
                    </p>

                    <p class="mt-1 text-sm text-[var(--muted)]">
                        Buku Paket tidak ditampilkan di halaman siswa.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($books->hasPages())
            <div class="mt-8 rounded-2xl border border-[var(--hairline)] bg-white px-4 py-4 shadow-sm sm:px-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-center text-sm text-[var(--muted)] sm:text-left">
                        Menampilkan
                        <span class="font-semibold text-[var(--text)]">{{ $books->firstItem() }}</span>&ndash;<span class="font-semibold text-[var(--text)]">{{ $books->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-[var(--text)]">{{ $books->total() }}</span>
                        data
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-1 sm:justify-end">
                        @if($books->onFirstPage())
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-slate-300">
                                Prev
                            </span>
                        @else
                            <a
                                href="{{ $books->previousPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                            >
                                Prev
                            </a>
                        @endif

                        @foreach($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                            @if($page == $books->currentPage())
                                <span class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg bg-[var(--emerald-deep)] px-3 text-sm font-semibold text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $url }}"
                                    class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($books->hasMorePages())
                            <a
                                href="{{ $books->nextPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                            >
                                Next
                            </a>
                        @else
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-slate-300">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Keranjang tetap seperti sebelumnya --}}
    @auth
        <div class="fixed bottom-6 right-6 z-50">
            <a
                href="{{ route('cart.index') }}"
                class="relative flex h-14 w-14 items-center justify-center rounded-full text-white shadow-xl shadow-[var(--forest)]/25 transition hover:-translate-y-1"
                style="background-image: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-deep) 100%);"
            >
                <i class="fas fa-shopping-cart text-xl"></i>

                @php
                    $cartCount = \App\Models\Cart::where('user_id', Auth::id())->count();
                @endphp

                @if($cartCount > 0)
                    <span class="font-mono-stat absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white ring-2 ring-white">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
        </div>
    @endauth

    @include('peminjam.book.partials.detail-modal')

</div>

@endsection