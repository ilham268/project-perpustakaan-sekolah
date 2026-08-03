@extends('layouts.admin')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')
@php
    $selectedSearch = request('search');
    $selectedTahun = request('tahun_pengadaan');
    $selectedJenis = request('jenis_koleksi');

    $tahunPengadaanOptions = \App\Models\Book::whereNotNull('tahun_pengadaan')
        ->distinct()
        ->orderByDesc('tahun_pengadaan')
        ->pluck('tahun_pengadaan');

    $bookQuery = \App\Models\Book::with('bookItems')->latest();

    if ($selectedSearch) {
        $bookQuery->where(function ($query) use ($selectedSearch) {
            $query->where('judul', 'like', '%' . $selectedSearch . '%')
                ->orWhere('penulis', 'like', '%' . $selectedSearch . '%')
                ->orWhere('penerbit', 'like', '%' . $selectedSearch . '%')
                ->orWhere('nomor_klasifikasi', 'like', '%' . $selectedSearch . '%')
                ->orWhere('jenis_koleksi', 'like', '%' . $selectedSearch . '%')
                ->orWhere('sumber_buku', 'like', '%' . $selectedSearch . '%')
                ->orWhere('tahun_pengadaan', 'like', '%' . $selectedSearch . '%')
                ->orWhere('tahun', 'like', '%' . $selectedSearch . '%');
        });
    }

    if ($selectedTahun) {
        $bookQuery->where('tahun_pengadaan', $selectedTahun);
    }

    if ($selectedJenis === 'Referensi') {
        $bookQuery->where(function ($query) {
            $query->where('jenis_koleksi', 'like', '%Referensi%')
                ->orWhere('jenis_koleksi', 'like', '%Reference%')
                ->orWhere('jenis_koleksi', 'like', '%Referance%')
                ->orWhere('jenis_koleksi', 'like', '%Raferance%')
                ->orWhere('jenis_koleksi', 'like', '%Referen%');
        });
    }

    if ($selectedJenis === 'Paket') {
        $bookQuery->where('jenis_koleksi', 'like', '%Paket%');
    }

    $books = $bookQuery->get();

    $normalizeJenis = function ($value) {
        return strtoupper(trim((string) $value));
    };

    $isReferensi = function ($book) use ($normalizeJenis) {
        $jenis = $normalizeJenis($book->jenis_koleksi);

        return str_contains($jenis, 'REFERENSI')
            || str_contains($jenis, 'REFERENCE')
            || str_contains($jenis, 'REFERANCE')
            || str_contains($jenis, 'RAFERANCE')
            || str_contains($jenis, 'REFEREN');
    };

    $isPaket = function ($book) use ($normalizeJenis) {
        return str_contains($normalizeJenis($book->jenis_koleksi), 'PAKET');
    };

    $referensiBooks = $books->filter($isReferensi)->values();
    $paketBooks = $books->filter($isPaket)->values();

    $bosJudul = $books->count();
    $bosEksemplar = $books->sum(fn($book) => $book->bookItems->count());
    $kodeTerisi = $books->sum(fn($book) => $book->bookItems->filter(fn($item) => !empty($item->kode_buku))->count());

    $referensiJudul = $referensiBooks->count();
    $referensiEksemplar = $referensiBooks->sum(fn($book) => $book->bookItems->count());

    $paketJudul = $paketBooks->count();
    $paketEksemplar = $paketBooks->sum(fn($book) => $book->bookItems->count());

    $booksIndexParams = $selectedTahun ? ['tahun_pengadaan' => $selectedTahun] : [];

    // Tabel ditampilkan per halaman (bukan sekaligus semua baris) agar tidak berat.
    // Statistik di atas (bosJudul, bosEksemplar, dst) tetap dihitung dari SELURUH data hasil filter,
    // bukan cuma data yang tampil di halaman saat ini.
    $perPage = 20;
    $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

    $booksPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $books->forPage($currentPage, $perPage)->values(),
        $books->count(),
        $perPage,
        $currentPage,
        [
            'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]
    );
@endphp

<div class="space-y-6" x-data="{ openBos: true }">

    @if(session('success') || request()->query('created') == '1')
        <x-flash-message type="success" message="{{ session('success') ?? 'Data berhasil disimpan.' }}" />
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

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Kategori&nbsp;Koleksi
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Kategori Buku
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    BOS adalah kategori utama. Buku Referensi dan Buku Paket menjadi isi dari kategori BOS.
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <a
                    href="{{ route('categories.create') }}"
                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"
                >
                    <i class="fas fa-pen-to-square text-xs"></i>
                    <span>Input Manual</span>
                </a>

                <a
                    href="{{ route('books.import.form') }}"
                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-white/25 bg-white/15 px-4 text-sm font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20"
                >
                    <i class="fas fa-file-excel text-xs"></i>
                    <span>Import Excel BOS</span>
                </a>

                <a
                    href="{{ route('books.index', $booksIndexParams) }}"
                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-white/25 bg-white/15 px-4 text-sm font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20"
                >
                    <i class="fas fa-book-open text-xs"></i>
                    <span>Kelola Buku BOS</span>
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="p-6" style="background-image: linear-gradient(180deg, var(--emerald-tint) 0%, white 60%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-deep)] text-white shadow-sm">
                        <i class="fas fa-book text-xl"></i>
                    </div>

                    <div>
                        <h2 class="font-display text-2xl font-semibold text-[var(--forest)]">
                            BOS
                        </h2>

                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-[var(--muted)]">
                            <span class="text-[var(--emerald-deep)]">Kategori Utama</span>
                            <span class="text-[var(--hairline)]">•</span>
                            <span>Referensi + Paket</span>

                            @if($selectedTahun)
                                <span class="text-[var(--hairline)]">•</span>
                                <span class="text-sky-700">Tahun {{ $selectedTahun }}</span>
                            @endif

                            @if($selectedJenis)
                                <span class="text-[var(--hairline)]">•</span>
                                <span class="text-[var(--gold)]">{{ $selectedJenis }}</span>
                            @endif
                        </div>

                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-[var(--muted)]">
                            Di halaman ini BOS menjadi induk semua data buku. Data yang tampil bisa difilter berdasarkan pencarian, tahun pengadaan, dan jenis buku.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="openBos = !openBos"
                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    <span x-text="openBos ? 'Tutup Isi BOS' : 'Buka Isi BOS'"></span>
                </button>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Judul BOS
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold text-[var(--text)]">
                        {{ $bosJudul }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        Data sesuai filter yang dipilih
                    </p>
                </div>

                <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Eksemplar
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold text-[var(--text)]">
                        {{ $bosEksemplar }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        Semua item fisik buku
                    </p>
                </div>

                <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Kode Terisi
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold text-[var(--text)]">
                        {{ $kodeTerisi }}/{{ $bosEksemplar }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        Kode buku yang sudah diinput
                    </p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                            <i class="fas fa-bookmark"></i>
                        </div>

                        <div>
                            <h3 class="font-display text-lg font-semibold text-[var(--forest)]">
                                Buku Referensi
                            </h3>

                            <p class="mt-1 text-sm text-[var(--muted)]">
                                Bagian dari kategori BOS.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-[var(--paper)] p-4">
                            <p class="text-xs font-semibold uppercase text-[var(--muted)]">Judul</p>
                            <p class="font-mono-stat mt-1 text-2xl font-semibold text-[var(--text)]">{{ $referensiJudul }}</p>
                        </div>

                        <div class="rounded-xl bg-[var(--paper)] p-4">
                            <p class="text-xs font-semibold uppercase text-[var(--muted)]">Eksemplar</p>
                            <p class="font-mono-stat mt-1 text-2xl font-semibold text-[var(--text)]">{{ $referensiEksemplar }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <i class="fas fa-layer-group"></i>
                        </div>

                        <div>
                            <h3 class="font-display text-lg font-semibold text-[var(--forest)]">
                                Buku Paket
                            </h3>

                            <p class="mt-1 text-sm text-[var(--muted)]">
                                Bagian dari kategori BOS.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-[var(--paper)] p-4">
                            <p class="text-xs font-semibold uppercase text-[var(--muted)]">Judul</p>
                            <p class="font-mono-stat mt-1 text-2xl font-semibold text-[var(--text)]">{{ $paketJudul }}</p>
                        </div>

                        <div class="rounded-xl bg-[var(--paper)] p-4">
                            <p class="text-xs font-semibold uppercase text-[var(--muted)]">Eksemplar</p>
                            <p class="font-mono-stat mt-1 text-2xl font-semibold text-[var(--text)]">{{ $paketEksemplar }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openBos" x-transition.opacity.duration.150ms x-cloak>
            <form
                id="bulk-delete-form"
                action="{{ route('books.bulk-delete') }}"
                method="POST"
            >
                @csrf

                <div class="border-t border-[var(--hairline)] bg-white px-5 py-4">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h3 class="font-display text-lg font-semibold text-[var(--forest)]">
                                    Isi Kategori BOS
                                </h3>

                                <p class="mt-1 text-sm text-[var(--muted)]">
                                    Semua buku Referensi dan Paket ditampilkan menjadi satu sesuai filter yang dipilih.
                                </p>
                            </div>

                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <button
                                    type="submit"
                                    id="bulk-delete-button"
                                    disabled
                                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:bg-slate-100"
                                >
                                    Hapus Dipilih
                                    <span class="font-mono-stat ml-1">
                                        (<span id="selected-count">0</span>)
                                    </span>
                                </button>

                                <a
                                    href="{{ route('categories.create') }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-sky-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                    <span>Input Manual</span>
                                </a>

                                <a
                                    href="{{ route('books.import.form') }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                >
                                    <i class="fas fa-file-excel text-xs"></i>
                                    <span>Import Excel</span>
                                </a>

                                <a
                                    href="{{ route('books.index', $booksIndexParams) }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                                >
                                    <i class="fas fa-book-open text-xs"></i>
                                    <span>Kelola Buku</span>
                                </a>
                            </div>
                        </div>

                        <div>
                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                                <div class="relative lg:col-span-5">
                                    <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                                    <input
                                        type="text"
                                        name="search"
                                        id="search-input"
                                        form="filter-form"
                                        value="{{ $selectedSearch }}"
                                        placeholder="Cari judul, pengarang, penerbit, klasifikasi..."
                                        autocomplete="off"
                                        class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    >
                                </div>

                                <div class="lg:col-span-3">
                                    <select
                                        name="tahun_pengadaan"
                                        id="tahun-select"
                                        form="filter-form"
                                        class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    >
                                        <option value="">Semua Tahun</option>

                                        @foreach($tahunPengadaanOptions as $tahunPengadaan)
                                            <option value="{{ $tahunPengadaan }}" {{ $selectedTahun == $tahunPengadaan ? 'selected' : '' }}>
                                                {{ $tahunPengadaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="lg:col-span-3">
                                    <select
                                        name="jenis_koleksi"
                                        id="jenis-select"
                                        form="filter-form"
                                        class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    >
                                        <option value="">Semua Jenis</option>
                                        <option value="Referensi" {{ $selectedJenis === 'Referensi' ? 'selected' : '' }}>Referensi</option>
                                        <option value="Paket" {{ $selectedJenis === 'Paket' ? 'selected' : '' }}>Paket</option>
                                    </select>
                                </div>

                                <div class="lg:col-span-1">
                                    <a
                                        href="{{ route('categories.index') }}"
                                        class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                                    >
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1380px] border-collapse text-sm">
                        <thead>
                            <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                                <th class="w-12 border border-[var(--hairline)] px-4 py-4 text-center">
                                    <input
                                        type="checkbox"
                                        id="select-all-books"
                                        class="h-4 w-4 rounded border-[var(--hairline)] text-[var(--emerald)] focus:ring-[var(--emerald)]"
                                    >
                                </th>

                                <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">No</th>
                                <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">Judul Buku</th>
                                <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Tahun Data</th>
                                <th class="w-52 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">Pengarang</th>
                                <th class="w-44 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">Penerbit</th>
                                <th class="w-24 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Terbit</th>
                                <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Jenis</th>
                                <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Sumber</th>
                                <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Eksemplar</th>
                                <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Kode</th>
                                <th class="w-52 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            @forelse($booksPaginated as $book)
                                @php
                                    $totalItems = $book->bookItems->count();
                                    $kodeBukuTerisi = $book->bookItems->filter(fn($item) => !empty($item->kode_buku))->count();
                                @endphp

                                <tr class="transition-colors hover:bg-[var(--sand)]/30">
                                    <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                        <input
                                            type="checkbox"
                                            name="selected_books[]"
                                            value="{{ $book->id }}"
                                            class="book-checkbox h-4 w-4 rounded border-[var(--hairline)] text-[var(--emerald)] focus:ring-[var(--emerald)]"
                                        >
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 font-semibold text-[var(--muted)]">
                                        {{ $booksPaginated->firstItem() + $loop->index }}
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4">
                                        <div class="max-w-[320px] font-semibold leading-snug text-[var(--text)]">
                                            {{ $book->judul }}
                                        </div>

                                        <div class="mt-1 text-xs font-medium text-[var(--muted)]">
                                            No. Klasifikasi: {{ $book->nomor_klasifikasi ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                        <span class="font-semibold text-[var(--text)]/80">
                                            {{ $book->tahun_pengadaan ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                        {{ $book->penulis ?? '-' }}
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                        {{ $book->penerbit ?? '-' }}
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                        {{ $book->tahun ?? '-' }}
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                        <span class="font-semibold text-[var(--emerald-deep)]">
                                            {{ $book->jenis_koleksi ?? 'BOS' }}
                                        </span>
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                        {{ $book->sumber_buku ?? '-' }}
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                        <span class="font-mono-stat font-semibold text-[var(--text)]/80">
                                            {{ $totalItems }}
                                        </span>
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                        @if($kodeBukuTerisi == $totalItems && $totalItems > 0)
                                            <span class="font-mono-stat font-semibold text-[var(--emerald-deep)]">
                                                {{ $kodeBukuTerisi }}/{{ $totalItems }}
                                            </span>
                                        @else
                                            <span class="font-mono-stat font-semibold text-amber-600">
                                                {{ $kodeBukuTerisi }}/{{ $totalItems }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="border border-[var(--hairline)] px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a
                                                href="{{ route('books.show', $book->id) }}"
                                                title="Detail / Input Kode Buku"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                            >
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('books.edit', $book->id) }}"
                                                title="Edit Buku"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-100"
                                            >
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                title="Hapus Buku"
                                                @click="$dispatch('open-confirm-delete', { url: '{{ route('books.destroy', $book->id) }}' })"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                                            <i class="fas fa-book-open text-2xl"></i>
                                        </div>

                                        <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                                            Belum ada buku di kategori BOS
                                        </p>

                                        <p class="mt-1 text-sm text-[var(--muted)]">
                                            Input manual, import Excel, atau ubah filter pencarian.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($booksPaginated->hasPages())
                    <div class="border-t border-[var(--hairline)] px-5 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-center text-sm text-[var(--muted)] sm:text-left">
                                Menampilkan
                                <span class="font-semibold text-[var(--text)]">{{ $booksPaginated->firstItem() }}</span>&ndash;<span class="font-semibold text-[var(--text)]">{{ $booksPaginated->lastItem() }}</span>
                                dari
                                <span class="font-semibold text-[var(--text)]">{{ $booksPaginated->total() }}</span>
                                data
                            </p>

                            <div class="flex flex-wrap items-center justify-center gap-1 sm:justify-end">
                                @if($booksPaginated->onFirstPage())
                                    <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-slate-300">
                                        Prev
                                    </span>
                                @else
                                    <a
                                        href="{{ $booksPaginated->previousPageUrl() }}"
                                        class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                                    >
                                        Prev
                                    </a>
                                @endif

                                @foreach($booksPaginated->getUrlRange(1, $booksPaginated->lastPage()) as $page => $url)
                                    @if($page == $booksPaginated->currentPage())
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

                                @if($booksPaginated->hasMorePages())
                                    <a
                                        href="{{ $booksPaginated->nextPageUrl() }}"
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
            </form>
        </div>
    </div>

    <form method="GET" action="{{ route('categories.index') }}" id="filter-form"></form>

    <x-confirm-delete />
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var tahunSelect = document.getElementById('tahun-select');
        var jenisSelect = document.getElementById('jenis-select');
        var debounceTimer;

        if (!form) {
            return;
        }

        if (searchInput) {
            // Kembalikan fokus + taruh cursor di akhir teks setelah reload,
            // supaya user bisa lanjut ngetik tanpa kerasa "kepotong"/reset.
            var currentValue = searchInput.value;
            searchInput.focus();
            searchInput.setSelectionRange(currentValue.length, currentValue.length);

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 700); // delay diperpanjang biar tidak submit di tengah-tengah ngetik
            });

            // Kalau user menekan Enter, langsung submit tanpa menunggu debounce
            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    clearTimeout(debounceTimer);
                    form.submit();
                }
            });
        }

        if (tahunSelect) {
            tahunSelect.addEventListener('change', function () {
                form.submit();
            });
        }

        if (jenisSelect) {
            jenisSelect.addEventListener('change', function () {
                form.submit();
            });
        }
    })();

    (function () {
        var selectAll = document.getElementById('select-all-books');
        var checkboxes = Array.prototype.slice.call(document.querySelectorAll('.book-checkbox'));
        var bulkForm = document.getElementById('bulk-delete-form');
        var bulkButton = document.getElementById('bulk-delete-button');
        var selectedCount = document.getElementById('selected-count');

        function updateBulkButton() {
            var checked = checkboxes.filter(function (checkbox) {
                return checkbox.checked;
            });

            if (selectedCount) {
                selectedCount.textContent = checked.length;
            }

            if (bulkButton) {
                bulkButton.disabled = checked.length === 0;
            }

            if (selectAll) {
                selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
                selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });

                updateBulkButton();
            });
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateBulkButton);
        });

        if (bulkForm) {
            bulkForm.addEventListener('submit', function (event) {
                var checked = checkboxes.filter(function (checkbox) {
                    return checkbox.checked;
                });

                if (checked.length === 0) {
                    event.preventDefault();
                    alert('Pilih minimal satu buku yang ingin dihapus.');
                    return;
                }

                if (!confirm('Hapus ' + checked.length + ' buku yang dipilih? Semua kode buku di dalamnya juga akan terhapus.')) {
                    event.preventDefault();
                }
            });
        }

        updateBulkButton();
    })();
</script>

@endsection