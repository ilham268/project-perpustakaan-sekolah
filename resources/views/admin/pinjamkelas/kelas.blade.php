@extends('layouts.admin')

@section('title', 'Kelas Pinjam')
@section('page-title', 'Kelas Pinjam')

@section('content')

@php
    $kelasList = $kelasList ?? collect();
    $jurusanList = $jurusanList ?? collect();
@endphp

<div class="space-y-6">

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-check"></i>
                </div>

                <span class="font-medium">
                    {{ session('success') }}
                </span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-red-600 ring-1 ring-red-100">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>

                <span class="font-medium">
                    {{ session('error') }}
                </span>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Data Peminjaman Kelas
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Kelas Pinjam
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Pantau peminjaman buku kelas, status persetujuan, pengembalian, dan denda dalam satu halaman.
                </p>
            </div>

            <div class="w-full lg:w-[190px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Data Pinjam
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                        {{ $pinjamKelas->total() }}
                    </p>
                </div>
            </div>
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
                    Cari data berdasarkan nama siswa, kode buku, kelas, atau jurusan.
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

                    <div class="xl:col-span-12">
                        <div class="flex justify-start xl:justify-end">
                            <a
                                href="{{ route('admin.pinjamkelas.kelas.export', request()->only(['search', 'kelas_id', 'jurusan'])) }}"
                                class="inline-flex h-10 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 sm:w-auto"
                            >
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white">
            <table class="w-full min-w-[1280px] border-collapse text-sm">
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

                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                            Status
                        </th>

                        <th class="w-44 border border-slate-200 px-5 py-4 text-center">
                            Aksi
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

                            $statusDenda = $item->status_denda ?? 'pending';
                            $dendaLunas = $item->status == 'denda' && $statusDenda == 'paid';
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

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($item->status == 'disetujui')
                                    <span class="font-semibold text-emerald-600">
                                        Disetujui
                                    </span>
                                @elseif($item->status == 'dikembalikan')
                                    <span class="font-semibold text-slate-600">
                                        Dikembalikan
                                    </span>
                                @elseif($dendaLunas)
                                    <span class="font-semibold text-emerald-600">
                                        Lunas
                                    </span>

                                    @if(isset($item->denda) && $item->denda > 0)
                                        <div class="mt-1 text-xs font-bold text-emerald-600">
                                            Rp {{ number_format($item->denda, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @elseif($item->status == 'denda')
                                    <span class="font-semibold text-red-600">
                                        Denda
                                    </span>

                                    @if(isset($item->denda) && $item->denda > 0)
                                        <div class="mt-1 text-xs font-bold text-red-600">
                                            Rp {{ number_format($item->denda, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="font-semibold text-amber-600">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($item->status == 'pending')
                                        <form action="{{ route('admin.pinjamkelas.kelas.setujui', $item->id) }}" method="POST">
                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Setujui peminjaman kelas ini?')"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                                title="Setujui"
                                            >
                                                Setujui
                                            </button>
                                        </form>

                                        <a
                                            href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                            title="Denda"
                                        >
                                            Denda
                                        </a>
                                    @elseif($item->status == 'disetujui')
                                        <a
                                            href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                            title="Denda"
                                        >
                                            Denda
                                        </a>
                                    @elseif($dendaLunas)
                                        <span class="text-xs font-semibold text-emerald-600">
                                            Lunas
                                        </span>
                                    @elseif($item->status == 'denda')
                                        <a
                                            href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-100"
                                            title="Lihat / Edit Denda"
                                        >
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">
                                            —
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-book-open text-2xl"></i>
                                </div>

                                <p class="mt-4 text-base font-bold text-slate-700">
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
        <div class="border-t border-slate-100 bg-white px-5 py-4">
            {{ $pinjamKelas->links() }}
        </div>
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