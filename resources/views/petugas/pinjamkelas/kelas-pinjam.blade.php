@extends('layouts.petugas')

@section('title', 'Kelas Pinjam')
@section('page-title', 'Kelas Pinjam')

@section('content')

@php
    $kelasList = $kelasList ?? collect();
    $jurusanList = $jurusanList ?? collect();
    $kelasJurusanMap = $kelasJurusanMap ?? collect();
@endphp

<div class="space-y-6">

    {{-- Flash Message Success --}}
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

    {{-- Flash Message Error --}}
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

    {{-- Page Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 md:px-7 md:py-6 shadow-md shadow-emerald-100/60">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative">
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                Kelas Pinjam
            </h1>

            <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                Pantau data peminjaman buku kelas dalam satu halaman.
            </p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header + Filter --}}
        <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-chalkboard-user"></i>
                </div>

                <div>
                    <h2 class="text-lg md:text-xl font-bold text-slate-900">
                        Daftar Peminjaman Kelas
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Cari data berdasarkan nama siswa, nomor identitas, kode buku, kelas, atau jurusan.
                    </p>
                </div>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ url()->current() }}" class="mt-6">
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

                    {{-- Search --}}
                    <div class="xl:col-span-4">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Pencarian
                        </label>

                        <div class="relative">
                            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama siswa atau kode buku..."
                                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                        </div>
                    </div>

                    {{-- Filter Kelas --}}
                    <div class="xl:col-span-3">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Kelas
                        </label>

                        <select
                            name="kelas"
                            onchange="this.form.submit()"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Semua Kelas</option>

                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                                    {{ $kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Jurusan --}}
                    <div class="xl:col-span-3">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Jurusan
                        </label>

                        <select
                            name="jurusan"
                            onchange="this.form.submit()"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Semua Jurusan</option>

                            @foreach($jurusanList as $jurusan)
                                <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>
                                    {{ $jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="xl:col-span-2">
                        <label class="mb-2 hidden xl:block text-xs font-semibold uppercase tracking-wide text-transparent">
                            Aksi
                        </label>

                        <div class="grid grid-cols-2 gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                            >
                                <i class="fas fa-filter text-xs"></i>
                                Filter
                            </button>

                            <a
                                href="{{ url()->current() }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                            >
                                <i class="fas fa-rotate-left text-xs"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white/90">
            <table class="w-full min-w-[1080px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nama Siswa
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nomor Identitas
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kelas
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Jurusan
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>
                    </tr>
                </thead>

                <tbody id="kelasPinjamTable">
                    @forelse($pinjamKelas as $index => $item)
                        @php
                            $kelasData = $item->user->kelas ?? $item->kategori->kelas ?? null;
                            $jurusanData = $kelasData ? $kelasJurusanMap->get($kelasData, '-') : '-';
                        @endphp

                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $pinjamKelas->firstItem() + $index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $item->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $item->user->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $kelasData ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $jurusanData ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
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
        <div class="border-t border-slate-100 bg-white/80 px-5 py-4">
            {{ $pinjamKelas->links() }}
        </div>
    </div>
</div>

@endsection