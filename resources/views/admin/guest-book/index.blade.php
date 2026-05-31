@extends('layouts.admin')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')

@section('content')

<div class="space-y-6">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Data Kunjungan
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Buku Tamu
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    Pantau data pengunjung perpustakaan berdasarkan nama, keperluan, tanggal, dan jam kunjungan.
                </p>
            </div>

            <button
                type="button"
                onclick="openExportModal()"
                class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                Export Excel
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route('admin.guest-book.index') }}"
        id="filter-form"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
    >
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
            <div class="relative lg:col-span-5">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau keperluan..."
                    autocomplete="off"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="lg:col-span-3">
                <input
                    type="date"
                    name="start_date"
                    id="start-date"
                    value="{{ request('start_date') }}"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="lg:col-span-3">
                <input
                    type="date"
                    name="end_date"
                    id="end-date"
                    value="{{ request('end_date') }}"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="lg:col-span-1">
                <a
                    href="{{ route('admin.guest-book.index') }}"
                    class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Total Kunjungan --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Total Kunjungan
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($totalKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        kunjungan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Hari Ini --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Hari Ini
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($todayKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        kunjungan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Bulan Ini --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Bulan Ini
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($monthKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        kunjungan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-white px-5 py-4">
            <h2 class="text-lg font-extrabold text-slate-900">
                Data Buku Tamu
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Daftar pengunjung yang mengisi buku tamu perpustakaan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nama
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Keperluan
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                            Tanggal
                        </th>

                        <th class="w-24 border border-slate-200 px-5 py-4 text-center">
                            Jam
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($guestBooks as $guest)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $guestBooks->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-slate-800">
                                    {{ $guest->nama }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <p class="max-w-md font-medium leading-relaxed text-slate-600">
                                    {{ Str::limit($guest->keperluan, 80) }}
                                </p>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ \Carbon\Carbon::parse($guest->created_at)->translatedFormat('d M Y') }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ \Carbon\Carbon::parse($guest->created_at)->format('H:i') }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <button
                                    type="button"
                                    @click="$dispatch('open-confirm-delete', { url: '{{ route('admin.guest-book.destroy', $guest->id) }}' })"
                                    title="Hapus Data"
                                    class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-slate-200 px-6 py-16 text-center">
                                <p class="text-sm font-bold text-slate-700">
                                    Tidak ada data buku tamu
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
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
        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Menampilkan
                    <span class="font-semibold text-slate-700">{{ $guestBooks->firstItem() }}</span>&ndash;<span class="font-semibold text-slate-700">{{ $guestBooks->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-slate-700">{{ $guestBooks->total() }}</span>
                    data
                </p>

                <div class="flex flex-wrap items-center gap-1">
                    @if($guestBooks->onFirstPage())
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
                            Prev
                        </span>
                    @else
                        <a
                            href="{{ $guestBooks->previousPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >
                            Prev
                        </a>
                    @endif

                    @foreach($guestBooks->getUrlRange(1, $guestBooks->lastPage()) as $page => $url)
                        @if($page == $guestBooks->currentPage())
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

                    @if($guestBooks->hasMorePages())
                        <a
                            href="{{ $guestBooks->nextPageUrl() }}"
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