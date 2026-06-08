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

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-6 py-6 shadow-md shadow-emerald-100/60">
        <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-24 -bottom-24 h-52 w-52 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Kategori Koleksi
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Kategori Buku
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    BOS adalah kategori utama. Buku Referensi dan Buku Paket menjadi isi dari kategori BOS.
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <a
                    href="{{ route('categories.create') }}"
                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
                >
                    <i class="fas fa-pen-to-square text-xs"></i>
                    <span>Input Manual</span>
                </a>

                <a
                    href="{{ route('books.import.form') }}"
                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-white/25 bg-white/15 px-4 text-sm font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20"
                >
                    <i class="fas fa-file-excel text-xs"></i>
                    <span>Import Excel BOS</span>
                </a>

                <a
                    href="{{ route('books.index', $booksIndexParams) }}"
                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-white/25 bg-white/15 px-4 text-sm font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20"
                >
                    <i class="fas fa-book-open text-xs"></i>
                    <span>Kelola Buku BOS</span>
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">
        <div class="bg-gradient-to-br from-emerald-50 via-white to-teal-50 p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-3xl bg-emerald-500 text-white shadow-sm">
                        <i class="fas fa-book text-xl"></i>
                    </div>

                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900">
                            BOS
                        </h2>

                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-slate-500">
                            <span class="text-emerald-700">Kategori Utama</span>
                            <span class="text-slate-300">•</span>
                            <span>Referensi + Paket</span>

                            @if($selectedTahun)
                                <span class="text-slate-300">•</span>
                                <span class="text-blue-700">Tahun {{ $selectedTahun }}</span>
                            @endif

                            @if($selectedJenis)
                                <span class="text-slate-300">•</span>
                                <span class="text-purple-700">{{ $selectedJenis }}</span>
                            @endif
                        </div>

                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-500">
                            Di halaman ini BOS menjadi induk semua data buku. Data yang tampil bisa difilter berdasarkan pencarian, tahun pengadaan, dan jenis buku.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="openBos = !openBos"
                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    <span x-text="openBos ? 'Tutup Isi BOS' : 'Buka Isi BOS'"></span>
                </button>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">
                        Total Judul BOS
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-900">
                        {{ $bosJudul }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        Data sesuai filter yang dipilih
                    </p>
                </div>

                <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">
                        Total Eksemplar
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-900">
                        {{ $bosEksemplar }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        Semua item fisik buku
                    </p>
                </div>

                <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">
                        Kode Terisi
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-900">
                        {{ $kodeTerisi }}/{{ $bosEksemplar }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        Kode buku yang sudah diinput
                    </p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                            <i class="fas fa-bookmark"></i>
                        </div>

                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">
                                Buku Referensi
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Bagian dari kategori BOS.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Judul</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $referensiJudul }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Eksemplar</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $referensiEksemplar }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                            <i class="fas fa-layer-group"></i>
                        </div>

                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">
                                Buku Paket
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Bagian dari kategori BOS.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Judul</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $paketJudul }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Eksemplar</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $paketEksemplar }}</p>
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

                <div class="border-t border-slate-100 bg-white px-5 py-4">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">
                                    Isi Kategori BOS
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Semua buku Referensi dan Paket ditampilkan menjadi satu sesuai filter yang dipilih.
                                </p>
                            </div>

                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <button
                                    type="submit"
                                    id="bulk-delete-button"
                                    disabled
                                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:bg-slate-100"
                                >
                                    Hapus Dipilih
                                    <span class="ml-1">
                                        (<span id="selected-count">0</span>)
                                    </span>
                                </button>

                                <a
                                    href="{{ route('categories.create') }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                    <span>Input Manual</span>
                                </a>

                                <a
                                    href="{{ route('books.import.form') }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                >
                                    <i class="fas fa-file-excel text-xs"></i>
                                    <span>Import Excel</span>
                                </a>

                                <a
                                    href="{{ route('books.index', $booksIndexParams) }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                                >
                                    <i class="fas fa-book-open text-xs"></i>
                                    <span>Kelola Buku</span>
                                </a>
                            </div>
                        </div>

                        <div>
                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                                <div class="relative lg:col-span-5">
                                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                                    <input
                                        type="text"
                                        name="search"
                                        id="search-input"
                                        form="filter-form"
                                        value="{{ $selectedSearch }}"
                                        placeholder="Cari judul, pengarang, penerbit, klasifikasi..."
                                        autocomplete="off"
                                        class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                    >
                                </div>

                                <div class="lg:col-span-3">
                                    <select
                                        name="tahun_pengadaan"
                                        id="tahun-select"
                                        form="filter-form"
                                        class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                        class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                    >
                                        <option value="">Semua Jenis</option>
                                        <option value="Referensi" {{ $selectedJenis === 'Referensi' ? 'selected' : '' }}>Referensi</option>
                                        <option value="Paket" {{ $selectedJenis === 'Paket' ? 'selected' : '' }}>Paket</option>
                                    </select>
                                </div>

                                <div class="lg:col-span-1">
                                    <a
                                        href="{{ route('categories.index') }}"
                                        class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
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
                            <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                                <th class="w-12 border border-slate-200 px-4 py-4 text-center">
                                    <input
                                        type="checkbox"
                                        id="select-all-books"
                                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                    >
                                </th>

                                <th class="w-16 border border-slate-200 px-5 py-4 text-left">No</th>
                                <th class="border border-slate-200 px-5 py-4 text-left">Judul Buku</th>
                                <th class="w-28 border border-slate-200 px-5 py-4 text-center">Tahun Data</th>
                                <th class="w-52 border border-slate-200 px-5 py-4 text-left">Pengarang</th>
                                <th class="w-44 border border-slate-200 px-5 py-4 text-left">Penerbit</th>
                                <th class="w-24 border border-slate-200 px-5 py-4 text-center">Terbit</th>
                                <th class="w-32 border border-slate-200 px-5 py-4 text-center">Jenis</th>
                                <th class="w-32 border border-slate-200 px-5 py-4 text-center">Sumber</th>
                                <th class="w-32 border border-slate-200 px-5 py-4 text-center">Eksemplar</th>
                                <th class="w-32 border border-slate-200 px-5 py-4 text-center">Kode</th>
                                <th class="w-52 border border-slate-200 px-5 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            @forelse($books as $book)
                                @php
                                    $totalItems = $book->bookItems->count();
                                    $kodeBukuTerisi = $book->bookItems->filter(fn($item) => !empty($item->kode_buku))->count();
                                @endphp

                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="border border-slate-200 px-4 py-4 text-center">
                                        <input
                                            type="checkbox"
                                            name="selected_books[]"
                                            value="{{ $book->id }}"
                                            class="book-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                        >
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 font-semibold text-slate-600">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4">
                                        <div class="max-w-[320px] font-bold leading-snug text-slate-800">
                                            {{ $book->judul }}
                                        </div>

                                        <div class="mt-1 text-xs font-medium text-slate-400">
                                            No. Klasifikasi: {{ $book->nomor_klasifikasi ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        <span class="font-semibold text-slate-700">
                                            {{ $book->tahun_pengadaan ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-slate-600">
                                        {{ $book->penulis ?? '-' }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-slate-600">
                                        {{ $book->penerbit ?? '-' }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center text-slate-600">
                                        {{ $book->tahun ?? '-' }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        <span class="font-semibold text-emerald-700">
                                            {{ $book->jenis_koleksi ?? 'BOS' }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center text-slate-600">
                                        {{ $book->sumber_buku ?? '-' }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        <span class="font-bold text-slate-700">
                                            {{ $totalItems }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        @if($kodeBukuTerisi == $totalItems && $totalItems > 0)
                                            <span class="font-bold text-emerald-700">
                                                {{ $kodeBukuTerisi }}/{{ $totalItems }}
                                            </span>
                                        @else
                                            <span class="font-bold text-amber-700">
                                                {{ $kodeBukuTerisi }}/{{ $totalItems }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a
                                                href="{{ route('books.show', $book->id) }}"
                                                title="Detail / Input Kode Buku"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                            >
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('books.edit', $book->id) }}"
                                                title="Edit Buku"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-100"
                                            >
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                title="Hapus Buku"
                                                @click="$dispatch('open-confirm-delete', { url: '{{ route('books.destroy', $book->id) }}' })"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="border border-slate-200 px-6 py-16 text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                            <i class="fas fa-book-open text-2xl"></i>
                                        </div>

                                        <p class="mt-4 text-base font-bold text-slate-700">
                                            Belum ada buku di kategori BOS
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Input manual, import Excel, atau ubah filter pencarian.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 400);
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