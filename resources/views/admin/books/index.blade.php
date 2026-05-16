@extends('layouts.admin')

@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')

@section('content')
    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if(session('success') || request()->query('created') == '1')
            <x-flash-message type="success" />
        @endif

        @if(session('deleted'))
            <x-flash-message type="deleted" />
        @endif

        @if(session('updated') || request()->query('updated') == '1')
            <x-flash-message type="updated" />
        @endif

        @if(session('error'))
            <x-flash-message type="error" message="{{ session('error') }}" />
        @endif

        {{-- Page Hero --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
            <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                        Kelola Buku
                    </h1>

                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                        Atur data buku, kategori, stok eksemplar, dan informasi koleksi perpustakaan dengan tampilan yang lebih rapi.
                    </p>
                </div>

                <a
                    href="{{ route('books.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm ring-1 ring-white/30 transition hover:-translate-y-0.5 hover:bg-emerald-50"
                >
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Buku</span>
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            {{-- Total Judul --}}
            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/95 p-5 shadow-sm">
                <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-emerald-50"></div>

                <div class="relative flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Judul
                        </p>

                        <p class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">
                            {{ $totalJudul }}
                            <span class="text-sm font-semibold text-slate-400">buku</span>
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                        <i class="fas fa-book text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Total Eksemplar --}}
            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/95 p-5 shadow-sm">
                <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-emerald-50"></div>

                <div class="relative flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Eksemplar
                        </p>

                        <p class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">
                            {{ $totalEksemplar }}
                            <span class="text-sm font-semibold text-slate-400">eksemplar</span>
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Kategori Aktif --}}
            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/95 p-5 shadow-sm">
                <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-emerald-50"></div>

                <div class="relative flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Kategori Aktif
                        </p>

                        <p class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">
                            {{ $totalKategori }}
                            <span class="text-sm font-semibold text-slate-400">kategori</span>
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                        <i class="fas fa-tags text-lg"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- Main Card --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white/95 shadow-sm">

            {{-- Toolbar --}}
            <div class="border-b border-slate-100 bg-white/80 p-5 md:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                            <i class="fas fa-book-open"></i>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                                Daftar Buku
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Cari buku, filter kategori, lihat stok, dan kelola data buku.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Search & Filter --}}
                <form method="GET" action="{{ route('books.index') }}" id="filter-form" class="mt-5">
                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">

                        {{-- Search --}}
                        <div class="relative lg:col-span-7">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request('search') }}"
                                placeholder="Cari judul buku, penulis, atau penerbit..."
                                autocomplete="off"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 py-3 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                        </div>

                        {{-- Category --}}
                        <div class="lg:col-span-3">
                            <select
                                name="category_id"
                                id="category-select"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                                <option value="">Semua Kategori</option>

                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Reset --}}
                        <div class="lg:col-span-2">
                            <a
                                href="{{ route('books.index') }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-200/70 hover:text-slate-800"
                            >
                                <i class="fas fa-rotate-left text-xs"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto bg-white/90">
                <table class="w-full min-w-[1180px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                                No
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Judul Buku
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Penerbit
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Kategori
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Penulis
                            </th>
                            <th class="w-24 border border-slate-200 px-5 py-4 text-center">
                                Tahun
                            </th>
                            <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                                Stok
                            </th>
                            <th class="w-32 border border-slate-200 px-5 py-4 text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($books as $index => $book)
                            <tr class="transition-colors hover:bg-slate-50">

                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $books->firstItem() + $index }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $book->judul }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $book->penerbit ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $book->category->nama_kategori ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $book->penulis ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                    {{ $book->tahun ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @php
                                        $availableCount = $book->availableItems()->count();
                                        $totalCount = $book->bookItems()->count();
                                    @endphp

                                    @if($availableCount > 0)
                                        <span class="font-semibold text-emerald-600">
                                            {{ $availableCount }}/{{ $totalCount }}
                                        </span>
                                    @else
                                        <span class="font-semibold text-red-600">
                                            Habis
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a
                                            href="{{ route('books.show', $book->id) }}"
                                            title="Detail Buku"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 transition hover:bg-emerald-100"
                                        >
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>

                                        <a
                                            href="{{ route('books.edit', $book->id) }}"
                                            title="Edit Buku"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                        >
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>

                                        <button
                                            type="button"
                                            title="Hapus Buku"
                                            @click="$dispatch('open-confirm-delete', { url: '{{ route('books.destroy', $book->id) }}' })"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="border border-slate-200 px-6 py-16 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <i class="fas fa-book text-2xl"></i>
                                    </div>

                                    <p class="mt-4 text-base font-bold text-slate-700">
                                        Tidak ada data buku
                                    </p>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Klik tombol "Tambah Buku" untuk menambahkan buku baru.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-slate-100 bg-white/80 px-5 py-4">
                <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                    <p class="text-sm text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $books->firstItem() ?? 0 }}</span>
                        &ndash;
                        <span class="font-semibold text-slate-700">{{ $books->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $books->total() }}</span>
                        data
                    </p>

                    <div class="flex items-center gap-1">
                        @if ($books->onFirstPage())
                            <span class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-xl text-slate-300">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a
                                href="{{ $books->previousPageUrl() }}"
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100"
                            >
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        @php
                            $start = max(1, $books->currentPage() - 1);
                            $end = min($books->lastPage(), $books->currentPage() + 1);
                        @endphp

                        @if($start > 1)
                            <a
                                href="{{ $books->url(1) }}"
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-sm text-slate-600 transition hover:bg-slate-100"
                            >
                                1
                            </a>

                            @if($start > 2)
                                <span class="flex h-9 w-9 items-center justify-center text-sm text-slate-400">
                                    ...
                                </span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $books->currentPage())
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-sm">
                                    {{ $i }}
                                </span>
                            @else
                                <a
                                    href="{{ $books->url($i) }}"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl text-sm text-slate-600 transition hover:bg-slate-100"
                                >
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        @if($end < $books->lastPage())
                            @if($end < $books->lastPage() - 1)
                                <span class="flex h-9 w-9 items-center justify-center text-sm text-slate-400">
                                    ...
                                </span>
                            @endif

                            <a
                                href="{{ $books->url($books->lastPage()) }}"
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-sm text-slate-600 transition hover:bg-slate-100"
                            >
                                {{ $books->lastPage() }}
                            </a>
                        @endif

                        @if ($books->hasMorePages())
                            <a
                                href="{{ $books->nextPageUrl() }}"
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100"
                            >
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-xl text-slate-300">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <x-confirm-delete />
    </div>

    <script>
        (function () {
            var form = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var categorySelect = document.getElementById('category-select');
            var debounceTimer;

            if (!form) {
                return;
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);

                    debounceTimer = setTimeout(function () {
                        form.submit();
                    }, 400);
                });
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', function () {
                    form.submit();
                });
            }
        })();
    </script>
@endsection