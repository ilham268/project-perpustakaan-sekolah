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
        <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--emerald-tint)] px-4 py-3 text-sm text-[var(--emerald-deep)] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
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
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Data&nbsp;Peminjaman&nbsp;Kelas
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Kelas Pinjam
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Pantau peminjaman buku kelas, status persetujuan, pengembalian, dan denda dalam satu halaman.
                </p>
            </div>

            <div class="w-full lg:w-[190px]">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                    <p class="catalog-eyebrow uppercase text-white/70">
                        Data Pinjam
                    </p>

                    <p class="font-mono-stat mt-1 text-2xl font-semibold tracking-tight text-white">
                        {{ $pinjamKelas->total() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">

        {{-- Header + Filter --}}
        <div class="border-b border-[var(--hairline)] px-5 py-5 md:px-6">
            <div class="flex flex-col gap-1">
                <h2 class="font-display text-lg font-semibold text-[var(--forest)] md:text-xl">
                    Daftar Peminjaman Kelas
                </h2>

                <p class="text-sm text-[var(--muted)]">
                    Cari data berdasarkan nama siswa, kode buku, kelas, atau jurusan.
                </p>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ url()->current() }}" id="filter-form" class="mt-5">
                <div class="grid grid-cols-1 gap-3 xl:grid-cols-12">
                    <div class="relative xl:col-span-4">
                        <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                        <input
                            type="text"
                            name="search"
                            id="search-input"
                            value="{{ request('search') }}"
                            placeholder="Cari nama siswa atau kode buku..."
                            autocomplete="off"
                            class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        >
                    </div>

                    <div class="xl:col-span-3">
                        <select
                            name="kelas_id"
                            id="kelas-select"
                            class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
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
                            class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
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
                            class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ url()->current() }}"
                            class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                        >
                            Reset
                        </a>
                    </div>

                    <div class="xl:col-span-12">
                        <div class="flex justify-start xl:justify-end">
                            <a
                                href="{{ route('admin.pinjamkelas.kelas.export', request()->only(['search', 'kelas_id', 'jurusan'])) }}"
                                class="inline-flex h-10 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)] sm:w-auto"
                            >
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1280px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Nama Siswa
                        </th>

                        <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Nomor Identitas
                        </th>

                        <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Kelas
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Jurusan
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Judul Buku
                        </th>

                        <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Kode Buku
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Status
                        </th>

                        <th class="w-44 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white" id="kelasPinjamTable">
                    @forelse($pinjamKelas as $index => $item)
                        @php
                            $kelasData = $item->user->kelas ?? '-';
                            $jurusanData = $item->user->jurusan ?? '-';

                            $statusDenda = $item->status_denda ?? 'pending';
                            $dendaLunas = $item->status == 'denda' && $statusDenda == 'paid';
                        @endphp

                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                {{ $pinjamKelas->firstItem() + $index }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-[var(--text)]">
                                    {{ $item->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $item->user->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $kelasData }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $jurusanData }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[280px] truncate font-semibold text-[var(--text)]">
                                    {{ $item->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $item->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($item->status == 'disetujui')
                                    <span class="font-semibold text-[var(--emerald-deep)]">
                                        Disetujui
                                    </span>
                                @elseif($item->status == 'dikembalikan')
                                    <span class="font-semibold text-[var(--muted)]">
                                        Dikembalikan
                                    </span>
                                @elseif($dendaLunas)
                                    <span class="font-semibold text-[var(--emerald-deep)]">
                                        Lunas
                                    </span>

                                    @if(isset($item->denda) && $item->denda > 0)
                                        <div class="font-mono-stat mt-1 text-xs font-semibold text-[var(--emerald-deep)]">
                                            Rp {{ number_format($item->denda, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @elseif($item->status == 'denda')
                                    <span class="font-semibold text-red-600">
                                        Denda
                                    </span>

                                    @if(isset($item->denda) && $item->denda > 0)
                                        <div class="font-mono-stat mt-1 text-xs font-semibold text-red-600">
                                            Rp {{ number_format($item->denda, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="font-semibold text-amber-600">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($item->status == 'pending')
                                        <form action="{{ route('admin.pinjamkelas.kelas.setujui', $item->id) }}" method="POST">
                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Setujui peminjaman kelas ini?')"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                                title="Setujui"
                                            >
                                                Setujui
                                            </button>
                                        </form>

                                        <a
                                            href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                            title="Denda"
                                        >
                                            Denda
                                        </a>
                                    @elseif($item->status == 'disetujui')
                                        <a
                                            href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                            title="Denda"
                                        >
                                            Denda
                                        </a>
                                    @elseif($dendaLunas)
                                        <span class="text-xs font-semibold text-[var(--emerald-deep)]">
                                            Lunas
                                        </span>
                                    @elseif($item->status == 'denda')
                                        <a
                                            href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-100"
                                            title="Lihat / Edit Denda"
                                        >
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-xs text-[var(--muted)]">
                                            —
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                                    <i class="fas fa-book-open text-2xl"></i>
                                </div>

                                <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                                    Belum ada data peminjaman kelas
                                </p>

                                <p class="mt-1 text-sm text-[var(--muted)]">
                                    Data akan muncul setelah siswa melakukan peminjaman kelas.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-[var(--hairline)] px-5 py-4">
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