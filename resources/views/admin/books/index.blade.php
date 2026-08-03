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

    $selectedTahun = request('tahun_pengadaan');
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

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Referensi
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Kelola Buku Referensi
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Halaman ini hanya menampilkan Buku Referensi. Filter data cukup berdasarkan tahun pengadaan.
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <a
                    href="{{ route('books.import.form') }}"
                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"
                >
                    Import Excel
                </a>

                <a
                    href="{{ route('categories.index') }}"
                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl border border-white/25 bg-white/15 px-4 text-sm font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20"
                >
                    Kategori BOS
                </a>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Total Judul Referensi
            </p>

            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--text)]">
                {{ $totalJudulReferensi }}
                <span class="text-sm font-medium text-[var(--muted)]">buku</span>
            </p>
        </div>

        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Total Eksemplar
            </p>

            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--text)]">
                {{ $totalEksemplarReferensi }}
                <span class="text-sm font-medium text-[var(--muted)]">item</span>
            </p>
        </div>

        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Data Tampil
            </p>

            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--text)]">
                {{ $booksReferensi->count() }}
                <span class="text-sm font-medium text-[var(--muted)]">buku</span>
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('books.index') }}" id="filter-form">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                        Filter Tahun Data
                    </h2>

                    <p class="mt-1 text-sm text-[var(--muted)]">
                        Pilih tahun pengadaan untuk melihat Buku Referensi berdasarkan tahun import.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <select
                        name="tahun_pengadaan"
                        id="tahun-select"
                        class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)] sm:w-56"
                    >
                        <option value="">Semua Tahun</option>

                        @foreach(($tahunPengadaanOptions ?? collect()) as $tahunPengadaan)
                            <option value="{{ $tahunPengadaan }}" {{ $selectedTahun == $tahunPengadaan ? 'selected' : '' }}>
                                {{ $tahunPengadaan }}
                            </option>
                        @endforeach
                    </select>

                    <a
                        href="{{ route('books.index') }}"
                        class="inline-flex h-11 items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <form
        id="bulk-delete-form"
        action="{{ route('books.bulk-delete') }}"
        method="POST"
        class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm"
    >
        @csrf

        <div class="border-b border-[var(--hairline)] px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                        Buku Referensi
                    </h2>

                    <p class="mt-1 text-sm text-[var(--muted)]">
                        Data buku Referensi yang berada di dalam kategori BOS.
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-[var(--muted)]">
                        @if($selectedTahun)
                            <span>Tahun {{ $selectedTahun }}</span>
                            <span class="text-[var(--hairline)]">•</span>
                        @endif

                        <span>Referensi</span>
                        <span class="text-[var(--hairline)]">•</span>
                        <span>{{ $booksReferensi->count() }} buku</span>
                    </div>
                </div>

                <button
                    type="submit"
                    id="bulk-delete-button"
                    disabled
                    class="inline-flex h-10 w-full items-center justify-center whitespace-nowrap rounded-xl bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:bg-slate-100 sm:w-auto"
                >
                    Hapus Dipilih
                    <span class="font-mono-stat ml-1">
                        (<span id="selected-count">0</span>)
                    </span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1220px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-12 border border-[var(--hairline)] px-4 py-3 text-center">
                            <input
                                type="checkbox"
                                id="select-all-books"
                                class="h-4 w-4 rounded border-[var(--hairline)] text-[var(--emerald)] focus:ring-[var(--emerald)]"
                            >
                        </th>

                        <th class="w-14 border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            Buku
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Tahun Data
                        </th>

                        <th class="w-52 border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            Pengarang / Penerbit
                        </th>

                        <th class="w-24 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Terbit
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Jenis
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Sumber
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Eksemplar
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Kode
                        </th>

                        <th class="w-44 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
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

                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                <input
                                    type="checkbox"
                                    name="selected_books[]"
                                    value="{{ $book->id }}"
                                    class="book-checkbox h-4 w-4 rounded border-[var(--hairline)] text-[var(--emerald)] focus:ring-[var(--emerald)]"
                                >
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4 font-semibold text-[var(--muted)]">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4">
                                <div class="max-w-[360px] font-semibold leading-snug text-[var(--text)]">
                                    {{ $book->judul }}
                                </div>

                                <div class="mt-1 text-xs font-medium text-[var(--muted)]">
                                    No. Klasifikasi: {{ $book->nomor_klasifikasi ?? '-' }}
                                </div>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                <span class="font-semibold text-[var(--text)]/80">
                                    {{ $book->tahun_pengadaan ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4">
                                <div class="font-semibold text-[var(--text)]/80">
                                    {{ $book->penulis ?? '-' }}
                                </div>

                                <div class="mt-1 text-xs text-[var(--muted)]">
                                    {{ $book->penerbit ?? '-' }}
                                </div>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4 text-center text-[var(--muted)]">
                                {{ $book->tahun ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                <span class="font-semibold text-[var(--emerald-deep)]">
                                    {{ $book->jenis_koleksi ?? 'Referensi' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4 text-center text-[var(--muted)]">
                                {{ $book->sumber_buku ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                <span class="font-mono-stat font-semibold text-[var(--text)]/80">
                                    {{ $totalItems }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                @if($kodeTerisi == $totalItems && $totalItems > 0)
                                    <span class="font-mono-stat font-semibold text-[var(--emerald-deep)]">
                                        {{ $kodeTerisi }}/{{ $totalItems }}
                                    </span>
                                @else
                                    <span class="font-mono-stat font-semibold text-amber-600">
                                        {{ $kodeTerisi }}/{{ $totalItems }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-4">
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
                            <td colspan="11" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                                    <i class="fas fa-bookmark text-2xl"></i>
                                </div>

                                <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                                    Tidak ada data Buku Referensi
                                </p>

                                <p class="mt-1 text-sm text-[var(--muted)]">
                                    Import Excel dulu atau ubah filter tahun data.
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
        var tahunSelect = document.getElementById('tahun-select');

        if (!form || !tahunSelect) {
            return;
        }

        tahunSelect.addEventListener('change', function () {
            form.submit();
        });
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