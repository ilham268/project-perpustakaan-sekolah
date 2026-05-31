@extends('layouts.admin')

@section('title', 'Kelola Buku Referensi')
@section('page-title', 'Kelola Buku Referensi')

@section('content')
@php
    $booksReferensi = $booksReferensi ?? collect();

    $totalJudulReferensi = $booksReferensi->count();

    $totalEksemplarReferensi = $booksReferensi->sum(function ($book) {
        return $book->bookItems->count();
    });
@endphp

<div class="space-y-6">

    @if(session('success') || request()->query('created') == '1')
        <x-flash-message type="success" message="{{ session('success') }}" />
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

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                </div>

                <span>{{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-6 py-6 shadow-md shadow-emerald-100/60">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Referensi
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Kelola Buku Referensi
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Halaman ini hanya menampilkan Buku Referensi. Buku Paket dikelola melalui menu peminjaman Buku Paket.
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <a
                    href="{{ route('books.import.form') }}"
                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
                >
                    Import Excel
                </a>

                <a
                    href="{{ route('categories.index') }}"
                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-white/25 bg-white/15 px-4 text-sm font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20"
                >
                    Kategori BOS
                </a>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Total Judul Referensi
            </p>

            <p class="mt-2 text-2xl font-extrabold text-slate-900">
                {{ $totalJudulReferensi }}
                <span class="text-sm font-semibold text-slate-400">buku</span>
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Total Eksemplar
            </p>

            <p class="mt-2 text-2xl font-extrabold text-slate-900">
                {{ $totalEksemplarReferensi }}
                <span class="text-sm font-semibold text-slate-400">item</span>
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Data Tampil
            </p>

            <p class="mt-2 text-2xl font-extrabold text-slate-900">
                {{ $booksReferensi->count() }}
                <span class="text-sm font-semibold text-slate-400">buku</span>
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('books.index') }}" id="filter-form">
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-10">
                <div class="relative lg:col-span-4">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        value="{{ request('search') }}"
                        placeholder="Cari judul, pengarang, penerbit, klasifikasi..."
                        autocomplete="off"
                        class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                </div>

                <div class="lg:col-span-2">
                    <select
                        name="tahun_pengadaan"
                        id="tahun-select"
                        class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                        <option value="">Semua Tahun</option>

                        @foreach(($tahunPengadaanOptions ?? collect()) as $tahunPengadaan)
                            <option value="{{ $tahunPengadaan }}" {{ request('tahun_pengadaan') == $tahunPengadaan ? 'selected' : '' }}>
                                {{ $tahunPengadaan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select
                        name="sumber_buku"
                        id="sumber-select"
                        class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                        <option value="">Semua Sumber</option>

                        @foreach(($sumberOptions ?? collect()) as $sumber)
                            <option value="{{ $sumber }}" {{ request('sumber_buku') == $sumber ? 'selected' : '' }}>
                                {{ $sumber }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <a
                        href="{{ route('books.index') }}"
                        class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <form
        id="bulk-delete-form"
        action="{{ route('books.bulk-delete') }}"
        method="POST"
        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
    >
        @csrf

        <div class="border-b border-slate-100 bg-white px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Buku Referensi
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Data buku Referensi yang berada di dalam kategori BOS.
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-slate-500">
                        @if(request('tahun_pengadaan'))
                            <span>Tahun {{ request('tahun_pengadaan') }}</span>
                            <span class="text-slate-300">•</span>
                        @endif

                        <span>Referensi</span>
                        <span class="text-slate-300">•</span>
                        <span>{{ $booksReferensi->count() }} buku</span>
                    </div>
                </div>

                <button
                    type="submit"
                    id="bulk-delete-button"
                    disabled
                    class="inline-flex h-10 w-full items-center justify-center whitespace-nowrap rounded-lg bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:bg-slate-100 sm:w-auto"
                >
                    Hapus Dipilih
                    <span class="ml-1">
                        (<span id="selected-count">0</span>)
                    </span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1220px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-12 border border-slate-200 px-4 py-3 text-center">
                            <input
                                type="checkbox"
                                id="select-all-books"
                                class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                            >
                        </th>

                        <th class="w-14 border border-slate-200 px-4 py-3 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-left">
                            Buku
                        </th>

                        <th class="w-28 border border-slate-200 px-4 py-3 text-center">
                            Tahun Data
                        </th>

                        <th class="w-52 border border-slate-200 px-4 py-3 text-left">
                            Pengarang / Penerbit
                        </th>

                        <th class="w-24 border border-slate-200 px-4 py-3 text-center">
                            Terbit
                        </th>

                        <th class="w-28 border border-slate-200 px-4 py-3 text-center">
                            Jenis
                        </th>

                        <th class="w-28 border border-slate-200 px-4 py-3 text-center">
                            Sumber
                        </th>

                        <th class="w-28 border border-slate-200 px-4 py-3 text-center">
                            Eksemplar
                        </th>

                        <th class="w-28 border border-slate-200 px-4 py-3 text-center">
                            Kode
                        </th>

                        <th class="w-44 border border-slate-200 px-4 py-3 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($booksReferensi as $book)
                        @php
                            $totalItems = $book->bookItems->count();
                            $kodeTerisi = $book->bookItems->filter(fn($item) => !empty($item->kode_buku))->count();
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

                            <td class="border border-slate-200 px-4 py-4 font-semibold text-slate-600">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border border-slate-200 px-4 py-4">
                                <div class="max-w-[360px] font-bold leading-snug text-slate-800">
                                    {{ $book->judul }}
                                </div>

                                <div class="mt-1 text-xs font-medium text-slate-400">
                                    No. Klasifikasi: {{ $book->nomor_klasifikasi ?? '-' }}
                                </div>
                            </td>

                            <td class="border border-slate-200 px-4 py-4 text-center">
                                <span class="font-semibold text-slate-700">
                                    {{ $book->tahun_pengadaan ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-4 py-4">
                                <div class="font-semibold text-slate-700">
                                    {{ $book->penulis ?? '-' }}
                                </div>

                                <div class="mt-1 text-xs text-slate-400">
                                    {{ $book->penerbit ?? '-' }}
                                </div>
                            </td>

                            <td class="border border-slate-200 px-4 py-4 text-center text-slate-600">
                                {{ $book->tahun ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-4 py-4 text-center">
                                <span class="font-semibold text-emerald-700">
                                    {{ $book->jenis_koleksi ?? 'Referensi' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-4 py-4 text-center text-slate-600">
                                {{ $book->sumber_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-4 py-4 text-center">
                                <span class="font-bold text-slate-700">
                                    {{ $totalItems }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-4 py-4 text-center">
                                @if($kodeTerisi == $totalItems && $totalItems > 0)
                                    <span class="font-bold text-emerald-700">
                                        {{ $kodeTerisi }}/{{ $totalItems }}
                                    </span>
                                @else
                                    <span class="font-bold text-amber-700">
                                        {{ $kodeTerisi }}/{{ $totalItems }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-4 py-4">
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
                            <td colspan="11" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-bookmark text-2xl"></i>
                                </div>

                                <p class="mt-4 text-base font-bold text-slate-700">
                                    Tidak ada data Buku Referensi
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Import Excel dulu atau ubah filter pencarian.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <x-confirm-delete />
</div>

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var tahunSelect = document.getElementById('tahun-select');
        var sumberSelect = document.getElementById('sumber-select');
        var debounceTimer;

        if (form) {
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

            if (sumberSelect) {
                sumberSelect.addEventListener('change', function () {
                    form.submit();
                });
            }
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