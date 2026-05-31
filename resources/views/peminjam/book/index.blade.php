@extends('layouts.peminjam')

@section('title', 'Katalog Buku Referensi')
@section('page-title', 'Katalog Buku Referensi')

@section('content')

@if(session('success'))
    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
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
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-6 shadow-md shadow-emerald-100/70 md:px-7 md:py-7">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="pointer-events-none absolute right-5 top-5 opacity-20">
            <img
                src="{{ asset('image/logoTrans.png') }}"
                alt="Lantera"
                class="h-24 w-auto object-contain"
            >
        </div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Buku Referensi
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Temukan Buku Referensimu
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Katalog siswa hanya menampilkan buku Referensi. Buku yang belum punya kode buku tidak bisa dipinjam.
                </p>
            </div>

            <div class="w-full lg:w-[260px]">
                <div class="rounded-2xl bg-white/15 px-4 py-4 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Total Referensi
                    </p>

                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-white">
                        {{ $books->total() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-emerald-50">
                        buku tersedia di katalog
                    </p>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <form
            method="GET"
            action="{{ route('peminjam.list-buku') }}"
            class="relative z-10 mt-6 rounded-2xl bg-white/15 p-3 ring-1 ring-white/20 backdrop-blur-md"
        >
            <div class="flex flex-col gap-3 lg:flex-row">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul buku atau penulis..."
                    autocomplete="off"
                    class="h-11 min-w-0 flex-1 rounded-lg border border-white/40 bg-white px-4 text-sm font-medium text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-200 focus:ring-4 focus:ring-white/30"
                >

                <button
                    type="submit"
                    class="inline-flex h-11 items-center justify-center whitespace-nowrap rounded-lg bg-white px-5 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
                >
                    Cari
                </button>
            </div>
        </form>
    </div>

    {{-- Koleksi --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900">
                    Koleksi Referensi Terbaru
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Tampilan dibuat tanpa foto buku agar sesuai dengan data yang tersedia.
                </p>
            </div>

            @if(request('search'))
                <a
                    href="{{ route('peminjam.list-buku') }}"
                    class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Reset Filter
                </a>
            @endif
        </div>

        {{-- GRID 4 KOLOM --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse($books as $book)
                @php
                    $availableCount = (int) ($book->stok_tersedia ?? $book->bookItems->filter(function ($item) {
                        return $item->status === 'available' && !empty($item->kode_buku);
                    })->count());
                @endphp

                <div class="group flex min-h-[215px] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-100/60">
                    <div class="flex flex-1 flex-col p-4">

                        {{-- Top --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">
                                    Referensi
                                </p>

                                <h3
                                    class="mt-2 line-clamp-2 text-lg font-extrabold leading-snug text-slate-900 transition group-hover:text-emerald-700"
                                    title="{{ $book->judul }}"
                                >
                                    {{ $book->judul }}
                                </h3>
                            </div>

                            <div class="shrink-0 text-right">
                                @if($availableCount > 0)
                                    <p class="text-[11px] font-bold text-emerald-700">
                                        Tersedia
                                    </p>
                                @else
                                    <p class="text-[11px] font-bold text-red-700">
                                        Belum Ada Kode
                                    </p>
                                @endif

                                <p class="mt-1 text-lg font-extrabold leading-none text-slate-900">
                                    {{ $availableCount }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    stok
                                </p>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="mt-4 space-y-2 text-sm">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Penulis
                                </p>

                                <p class="mt-0.5 line-clamp-1 font-semibold text-slate-700">
                                    {{ $book->penulis ?? 'Penulis tidak tersedia' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Penerbit
                                </p>

                                <p class="mt-0.5 line-clamp-1 font-semibold text-slate-700">
                                    {{ $book->penerbit ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Tahun
                                </p>

                                <p class="mt-0.5 font-semibold text-slate-700">
                                    {{ $book->tahun ?? '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="mt-auto flex items-center justify-between gap-2 border-t border-slate-100 pt-3">
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
                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Detail
                            </button>

                            @auth
                                @if($availableCount > 0)
                                    <form action="{{ route('cart.store', $book->id) }}" method="POST">
                                        @csrf

                                        <button
                                            type="submit"
                                            class="inline-flex h-9 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                        >
                                            Tambah
                                        </button>
                                    </form>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex h-9 cursor-not-allowed items-center justify-center rounded-lg bg-slate-300 px-4 text-sm font-semibold text-white shadow-sm"
                                    >
                                        Tidak Tersedia
                                    </button>
                                @endif
                            @else
                                @if($availableCount > 0)
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex h-9 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                    >
                                        Login
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex h-9 cursor-not-allowed items-center justify-center rounded-lg bg-slate-300 px-4 text-sm font-semibold text-white shadow-sm"
                                    >
                                        Tidak Tersedia
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
                    <p class="text-base font-bold text-slate-700">
                        Belum ada buku Referensi
                    </p>

                    <p class="mt-1 text-sm text-slate-400">
                        Buku Paket tidak ditampilkan di halaman siswa.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($books->hasPages())
            <div class="mt-8 rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $books->firstItem() }}</span>&ndash;<span class="font-semibold text-slate-700">{{ $books->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $books->total() }}</span>
                        data
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        @if($books->onFirstPage())
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
                                Prev
                            </span>
                        @else
                            <a
                                href="{{ $books->previousPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                            >
                                Prev
                            </a>
                        @endif

                        @foreach($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                            @if($page == $books->currentPage())
                                <span class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-emerald-600 px-3 text-sm font-semibold text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $url }}"
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($books->hasMorePages())
                            <a
                                href="{{ $books->nextPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                            >
                                Next
                            </a>
                        @else
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
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

    @include('peminjam.book.partials.detail-modal')

</div>

@endsection