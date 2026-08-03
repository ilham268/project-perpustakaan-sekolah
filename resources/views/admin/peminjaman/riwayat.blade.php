@extends('layouts.admin')

@section('title', 'Riwayat Pengembalian')
@section('page-title', 'Riwayat Pengembalian')

@section('content')

<div class="space-y-6">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-check-circle"></i>
                </div>

                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>

                <span>{{ session('error') }}</span>
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
                    Data&nbsp;Pengembalian
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Riwayat Pengembalian
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Pantau riwayat pengembalian buku, kondisi buku, denda, dan petugas yang memproses data.
                </p>
            </div>

            <a
                href="{{ route('admin.pengembalian.create') }}"
                class="inline-flex h-10 w-fit shrink-0 items-center justify-center whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                Tambah Pengembalian
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route('admin.peminjaman.riwayat') }}"
        id="filter-form"
        class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm"
    >
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
            <div class="relative lg:col-span-8">
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari judul buku atau nama peminjam..."
                    autocomplete="off"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            <div class="lg:col-span-3">
                <select
                    name="kondisi"
                    id="kondisi-select"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    <option value="" {{ !request('kondisi') ? 'selected' : '' }}>Semua Kondisi</option>
                    <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <a
                    href="{{ route('admin.peminjaman.riwayat') }}"
                    title="Reset Filter"
                    class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Total Pengembalian
            </p>

            <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight text-[var(--text)]">
                {{ number_format($totalPengembalian) }}
            </p>
        </div>

        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Bermasalah
            </p>

            <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight text-[var(--text)]">
                {{ number_format($totalBermasalah) }}
            </p>
        </div>

        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Total Denda
            </p>

            <p class="font-mono-stat mt-2 truncate text-2xl font-semibold tracking-tight text-[var(--text)]">
                Rp {{ number_format($totalDendaSum, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="border-b border-[var(--hairline)] px-5 py-4">
            <div class="flex flex-col gap-1">
                <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                    Data Riwayat Pengembalian
                </h2>

                <p class="text-sm text-[var(--muted)]">
                    Daftar buku yang sudah dikembalikan beserta kondisi, denda, dan petugas.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Judul Buku
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Kode Buku
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Peminjam
                        </th>

                        <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Nomor Identitas
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Tgl Tempo
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Tgl Kembali
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Kondisi
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Denda
                        </th>

                        <th class="w-44 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Petugas
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($returns as $return)
                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                {{ $returns->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[280px] truncate font-semibold text-[var(--text)]">
                                    {{ $return->loan->bookItem->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $return->loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-[var(--text)]">
                                    {{ $return->loan->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $return->loan->user->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                {{ $return->loan && $return->loan->tanggal_kembali ? \Carbon\Carbon::parse($return->loan->tanggal_kembali)->format('d M Y') : '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                {{ $return->tanggal_pengembalian ? \Carbon\Carbon::parse($return->tanggal_pengembalian)->format('d M Y') : '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($return->kondisi == 'baik')
                                    <span class="font-semibold text-[var(--emerald-deep)]">
                                        Baik
                                    </span>
                                @elseif($return->kondisi == 'rusak')
                                    <span class="font-semibold text-amber-600">
                                        Rusak
                                    </span>
                                @elseif($return->kondisi == 'hilang')
                                    <span class="font-semibold text-red-600">
                                        Hilang
                                    </span>
                                @else
                                    <span class="font-semibold text-[var(--muted)]">
                                        {{ ucfirst($return->kondisi ?? '-') }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($return->denda > 0)
                                    <span class="font-mono-stat font-semibold text-red-600">
                                        Rp {{ number_format($return->denda, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[var(--muted)]">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                <span class="block max-w-[180px] truncate">
                                    {{ $return->loan->petugas->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($return->denda > 0)
                                    <a
                                        href="{{ route('admin.pengembalian.invoice', $return->id) }}"
                                        title="Download Invoice"
                                        class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    >
                                        Invoice
                                    </a>
                                @else
                                    <span class="text-sm text-[var(--hairline)]">
                                        -
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                                    <i class="fas fa-history text-2xl"></i>
                                </div>

                                <p class="font-display mt-4 text-sm font-semibold text-[var(--text)]">
                                    Belum ada riwayat pengembalian
                                </p>

                                <p class="mt-1 text-xs text-[var(--muted)]">
                                    Data pengembalian buku akan muncul di sini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($returns->total() > 0)
        <div class="rounded-2xl border border-[var(--hairline)] bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-center text-sm text-[var(--muted)] sm:text-left">
                    Menampilkan
                    <span class="font-semibold text-[var(--text)]">{{ $returns->firstItem() }}</span>&ndash;<span class="font-semibold text-[var(--text)]">{{ $returns->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-[var(--text)]">{{ $returns->total() }}</span>
                    data
                </p>

                <div class="flex flex-wrap items-center justify-center gap-1 sm:justify-end">
                    @if($returns->onFirstPage())
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-slate-300">
                            Prev
                        </span>
                    @else
                        <a
                            href="{{ $returns->previousPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                        >
                            Prev
                        </a>
                    @endif

                    @foreach($returns->getUrlRange(1, $returns->lastPage()) as $page => $url)
                        @if($page == $returns->currentPage())
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

                    @if($returns->hasMorePages())
                        <a
                            href="{{ $returns->nextPageUrl() }}"
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

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var kondisiSelect = document.getElementById('kondisi-select');
        var debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 400);
            });
        }

        if (kondisiSelect) {
            kondisiSelect.addEventListener('change', function () {
                form.submit();
            });
        }
    })();
</script>

@endsection