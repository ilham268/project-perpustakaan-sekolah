@extends('layouts.admin')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')

@section('content')

<div class="space-y-6">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Data&nbsp;Kunjungan
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Buku Tamu
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Pantau data pengunjung perpustakaan berdasarkan nama, kelas, jurusan, keperluan, tanggal, dan jam kunjungan.
                </p>
            </div>

            <button
                type="button"
                onclick="openExportModal()"
                class="inline-flex h-10 w-fit shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                <i class="fas fa-file-export text-xs"></i>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route('admin.guest-book.index') }}"
        id="filter-form"
        class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm"
    >
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">
            {{-- Search Input --}}
            <div class="relative sm:col-span-2 lg:col-span-3">
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau keperluan..."
                    autocomplete="off"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            {{-- Dropdown Filter Kelas --}}
            <div class="lg:col-span-2">
                <select
                    name="kelas"
                    id="kelas-select"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    <option value="">Semua Kelas</option>
                    @foreach($listKelas as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Filter Jurusan --}}
            <div class="lg:col-span-2">
                <select
                    name="jurusan"
                    id="jurusan-select"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    <option value="">Semua Jurusan</option>
                    @foreach($listJurusan as $j)
                        <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>
                            {{ $j }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Mulai --}}
            <div class="lg:col-span-2">
                <input
                    type="date"
                    name="start_date"
                    id="start-date"
                    value="{{ request('start_date') }}"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            {{-- Tanggal Selesai --}}
            <div class="lg:col-span-2">
                <input
                    type="date"
                    name="end_date"
                    id="end-date"
                    value="{{ request('end_date') }}"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            {{-- Tombol Reset --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <a
                    href="{{ route('admin.guest-book.index') }}"
                    class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Total Kunjungan --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[var(--emerald)]/30 hover:shadow-lg hover:shadow-[var(--emerald)]/10">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-[var(--emerald-tint)] transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Kunjungan
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ number_format($totalKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        kunjungan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Hari Ini --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-amber-300/50 hover:shadow-lg hover:shadow-amber-100/60">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Hari Ini
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ number_format($todayKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        kunjungan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Bulan Ini --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-sky-300/50 hover:shadow-lg hover:shadow-sky-100/60">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Bulan Ini
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ number_format($monthKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        kunjungan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="border-b border-[var(--hairline)] px-5 py-4">
            <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                Data Buku Tamu
            </h2>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Daftar pengunjung yang mengisi buku tamu perpustakaan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Nama
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Kelas
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Jurusan
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Keperluan
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Tanggal
                        </th>

                        <th class="w-24 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Jam
                        </th>

                        <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($guestBooks as $guest)
                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                {{ $guestBooks->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-[var(--text)]">
                                    {{ $guest->nama }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="font-medium text-[var(--text)]/80">
                                    {{ $guest->kelas ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="font-medium text-[var(--text)]/80">
                                    {{ $guest->jurusan ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <p class="max-w-md font-medium leading-relaxed text-[var(--text)]/80">
                                    {{ Str::limit($guest->keperluan, 80) }}
                                </p>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                {{ \Carbon\Carbon::parse($guest->created_at)->timezone(config('app.timezone'))->translatedFormat('d M Y') }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                <span class="font-mono-stat text-[var(--text)]">
                                    {{ \Carbon\Carbon::parse($guest->created_at)->timezone(config('app.timezone'))->format('H:i') }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                <button
                                    type="button"
                                    @click="$dispatch('open-confirm-delete', { url: '{{ route('admin.guest-book.destroy', $guest->id) }}' })"
                                    title="Hapus Data"
                                    class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <p class="text-sm font-semibold text-[var(--text)]">
                                    Tidak ada data buku tamu
                                </p>

                                <p class="mt-1 text-xs text-[var(--muted)]">
                                    Belum ada pengunjung yang mengisi buku tamu.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($guestBooks->total() > 0)
        <div class="rounded-2xl border border-[var(--hairline)] bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-center text-sm text-[var(--muted)] sm:text-left">
                    Menampilkan
                    <span class="font-semibold text-[var(--text)]">{{ $guestBooks->firstItem() }}</span>&ndash;<span class="font-semibold text-[var(--text)]">{{ $guestBooks->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-[var(--text)]">{{ $guestBooks->total() }}</span>
                    data
                </p>

                <div class="flex flex-wrap items-center justify-center gap-1 sm:justify-end">
                    @if($guestBooks->onFirstPage())
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-slate-300">
                            Prev
                        </span>
                    @else
                        <a
                            href="{{ $guestBooks->previousPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                        >
                            Prev
                        </a>
                    @endif

                    @foreach($guestBooks->getUrlRange(1, $guestBooks->lastPage()) as $page => $url)
                        @if($page == $guestBooks->currentPage())
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

                    @if($guestBooks->hasMorePages())
                        <a
                            href="{{ $guestBooks->nextPageUrl() }}"
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

<x-confirm-delete
    title="Hapus Data Buku Tamu?"
    message="Apakah Anda yakin ingin menghapus data buku tamu ini? Tindakan ini tidak dapat dibatalkan."
/>

<x-export-modal
    :route="route('admin.guest-book.export')"
    title="Export Laporan Buku Tamu"
    :hasStatus="false"
/>

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var kelasSelect = document.getElementById('kelas-select');
        var jurusanSelect = document.getElementById('jurusan-select');
        var startDate = document.getElementById('start-date');
        var endDate = document.getElementById('end-date');
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

        if (kelasSelect) {
            kelasSelect.addEventListener('change', function () {
                form.submit();
            });
        }

        if (jurusanSelect) {
            jurusanSelect.addEventListener('change', function () {
                form.submit();
            });
        }

        if (startDate) {
            startDate.addEventListener('change', function () {
                form.submit();
            });
        }

        if (endDate) {
            endDate.addEventListener('change', function () {
                form.submit();
            });
        }
    })();
</script>

@endsection