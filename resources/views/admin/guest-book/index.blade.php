@extends('layouts.admin')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')

@section('content')

<div class="space-y-5">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>

                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Buku Tamu
        </h3>

        <button
            type="button"
            onclick="openExportModal()"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
        >
            <i class="fas fa-file-export text-xs"></i>
            <span>Export Excel</span>
        </button>
    </div>

    {{-- Search & Date Filter --}}
    <form
        method="GET"
        action="{{ route('admin.guest-book.index') }}"
        id="filter-form"
        class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm"
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div class="relative w-full lg:max-w-md">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau keperluan..."
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <span class="text-sm font-semibold text-slate-600">
                    Tanggal:
                </span>

                <input
                    type="date"
                    name="start_date"
                    id="start-date"
                    value="{{ request('start_date') }}"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                >

                <span class="hidden text-sm text-slate-400 sm:inline">
                    s/d
                </span>

                <input
                    type="date"
                    name="end_date"
                    id="end-date"
                    value="{{ request('end_date') }}"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                >
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

                    <p class="mt-2 truncate text-3xl font-bold tracking-tight text-slate-900">
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
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-teal-50 transition group-hover:bg-teal-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Hari Ini
                    </p>

                    <p class="mt-2 truncate text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($todayKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        kunjungan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 ring-1 ring-teal-100">
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

                    <p class="mt-2 truncate text-3xl font-bold tracking-tight text-slate-900">
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

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
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
                        <th class="w-20 border border-slate-200 px-5 py-4 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($guestBooks as $guest)
                        <tr class="transition-colors hover:bg-slate-50">

                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $guestBooks->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
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
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                >
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-book-open text-2xl"></i>
                                </div>

                                <p class="mt-4 text-sm font-bold text-slate-700">
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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $guestBooks->firstItem() }}&ndash;{{ $guestBooks->lastItem() }} dari {{ $guestBooks->total() }} data
            </p>

            <div class="flex flex-wrap items-center gap-1">

                @if($guestBooks->onFirstPage())
                    <span class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-300">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a
                        href="{{ $guestBooks->previousPageUrl() }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                    >
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($guestBooks->getUrlRange(1, $guestBooks->lastPage()) as $page => $url)
                    @if($page == $guestBooks->currentPage())
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-sm font-semibold text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $url }}"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if($guestBooks->hasMorePages())
                    <a
                        href="{{ $guestBooks->nextPageUrl() }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                    >
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-300">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </span>
                @endif

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