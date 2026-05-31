@extends('layouts.petugas')

@section('title', 'Kelas Pinjam')
@section('page-title', 'Kelas Pinjam')

@section('content')

@php
    $kelasList = $kelasList ?? collect();
    $jurusanList = $jurusanList ?? collect();
@endphp

<div class="space-y-6">

    {{-- Flash Message Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <span class="font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif

    {{-- Flash Message Error --}}
    @if(session('error'))
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
            <span class="font-medium">
                {{ session('error') }}
            </span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                Data Peminjaman Kelas
            </p>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                Kelas Pinjam
            </h1>

            <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                Pantau data peminjaman buku kelas dalam satu halaman.
            </p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        {{-- Header + Filter --}}
        <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
            <div class="flex flex-col gap-1">
                <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                    Daftar Peminjaman Kelas
                </h2>

                <p class="text-sm text-slate-500">
                    Cari data berdasarkan nama siswa, nomor identitas, kode buku, kelas, atau jurusan.
                </p>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ url()->current() }}" id="filter-form" class="mt-5">
                <div class="grid grid-cols-1 gap-3 xl:grid-cols-12">
                    <div class="relative xl:col-span-4">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                        <input
                            type="text"
                            name="search"
                            id="search-input"
                            value="{{ request('search') }}"
                            placeholder="Cari nama siswa atau kode buku..."
                            autocomplete="off"
                            class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                    </div>

                    <div class="xl:col-span-3">
                        <select
                            name="kelas_id"
                            id="kelas-select"
                            class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Semua Kelas</option>

                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->jurusan }} {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="xl:col-span-3">
                        <select
                            name="jurusan"
                            id="jurusan-select"
                            class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Semua Jurusan</option>

                            @foreach($jurusanList as $jurusan)
                                <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>
                                    {{ $jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 xl:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ url()->current() }}"
                            class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                        >
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white">
            <table class="w-full min-w-[1080px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nama Siswa
                        </th>

                        <th class="w-40 border border-slate-200 px-5 py-4 text-left">
                            Nomor Identitas
                        </th>

                        <th class="w-32 border border-slate-200 px-5 py-4 text-left">
                            Kelas
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-left">
                            Jurusan
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>

                        <th class="w-32 border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white" id="kelasPinjamTable">
                    @forelse($pinjamKelas as $index => $item)
                        @php
                            $kelasData = $item->user->kelas
                                ?? optional($item->kategori)->kelas
                                ?? '-';

                            $jurusanData = $item->user->jurusan
                                ?? optional(optional($item->kategori)->kelasData)->jurusan
                                ?? '-';
                        @endphp

                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $pinjamKelas->firstItem() + $index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-slate-800">
                                    {{ $item->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $item->user->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $kelasData }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $jurusanData }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[280px] truncate font-semibold text-slate-800">
                                    {{ $item->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $item->kode_buku ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-slate-200 px-6 py-16 text-center">
                                <p class="text-sm font-bold text-slate-700">
                                    Belum ada data peminjaman kelas
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Data akan muncul setelah siswa melakukan peminjaman kelas.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pinjamKelas->total() > 0)
            <div class="border-t border-slate-100 bg-white px-5 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $pinjamKelas->firstItem() }}</span>&ndash;<span class="font-semibold text-slate-700">{{ $pinjamKelas->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $pinjamKelas->total() }}</span>
                        data
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        @if($pinjamKelas->onFirstPage())
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
                                Prev
                            </span>
                        @else
                            <a
                                href="{{ $pinjamKelas->previousPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                            >
                                Prev
                            </a>
                        @endif

                        @foreach($pinjamKelas->getUrlRange(1, $pinjamKelas->lastPage()) as $page => $url)
                            @if($page == $pinjamKelas->currentPage())
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

                        @if($pinjamKelas->hasMorePages())
                            <a
                                href="{{ $pinjamKelas->nextPageUrl() }}"
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
</div>

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var kelasSelect = document.getElementById('kelas-select');
        var jurusanSelect = document.getElementById('jurusan-select');
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
    })();
</script>

@endsection