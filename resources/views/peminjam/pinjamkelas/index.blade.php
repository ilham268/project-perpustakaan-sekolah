@extends('layouts.peminjam')

@section('title', 'Buku Pinjaman')
@section('page-title', 'Buku Pinjaman - Peminjaman Kelas')

@section('content')

@php
    $pinjamKelasCollection = method_exists($pinjamKelas, 'getCollection')
        ? $pinjamKelas->getCollection()
        : collect($pinjamKelas);

    $totalPinjamKelas = method_exists($pinjamKelas, 'total')
        ? $pinjamKelas->total()
        : $pinjamKelasCollection->count();

    $totalPending = $pinjamKelasCollection->where('status', 'pending')->count();
    $totalDisetujui = $pinjamKelasCollection->where('status', 'disetujui')->count();
@endphp

<div class="space-y-6">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">Peminjaman&nbsp;Kelas</p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Buku Pinjaman Kelas
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Daftar buku yang sedang dipinjam secara kolektif oleh kelas.
                </p>
            </div>

            <a href="{{ route('siswa.pinjamkelas.input') }}" class="inline-flex h-10 w-fit shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"><i class="fas fa-plus text-xs"></i> <span>Input Buku</span></a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-[var(--emerald)]/10">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-[var(--emerald-tint)] transition group-hover:bg-[var(--emerald-tint)]"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                        Total Data
                    </p>

                    <p class="font-mono-stat mt-2 text-3xl font-semibold tracking-tight text-[var(--text)]">
                        {{ $totalPinjamKelas }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        pinjaman
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                        Pending
                    </p>

                    <p class="font-mono-stat mt-2 text-3xl font-semibold tracking-tight text-[var(--text)]">
                        {{ $totalPending }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:bg-sky-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                        Disetujui
                    </p>

                    <p class="font-mono-stat mt-2 text-3xl font-semibold tracking-tight text-[var(--text)]">
                        {{ $totalDisetujui }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">

        <div class="border-b border-[var(--hairline)] px-6 py-5">
            <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                Data Buku Pinjaman Kelas
            </h2>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Riwayat pengajuan dan status peminjaman buku kelas.
            </p>
        </div>

        @if($pinjamKelas->isNotEmpty())

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-sm">
                    <thead>
                        <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                            <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                No
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Kategori
                            </th>

                            <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Kode Buku
                            </th>

                            <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach($pinjamKelas as $index => $item)
                            <tr class="transition-colors hover:bg-[var(--sand)]/30">
                                <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                    {{ $pinjamKelas->firstItem() + $index }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
                                    <span class="block max-w-[320px] truncate font-semibold text-[var(--text)]">
                                        {{ $item->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                    {{ $item->kode_buku ?? '-' }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                    @if($item->status == 'pending')
                                        <span class="font-semibold text-amber-600">
                                            Pending
                                        </span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="font-semibold text-[var(--emerald-deep)]">
                                            Disetujui
                                        </span>
                                    @elseif($item->status == 'dikembalikan')
                                        <span class="font-semibold text-sky-600">
                                            Dikembalikan
                                        </span>
                                    @else
                                        <span class="font-semibold text-[var(--text)]/70">
                                            {{ ucfirst($item->status ?? '-') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($pinjamKelas, 'total') && $pinjamKelas->total() > 0)
                <div class="border-t border-[var(--hairline)] bg-white px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-[var(--muted)]">
                            Menampilkan
                            <span class="font-semibold text-[var(--text)]">{{ $pinjamKelas->firstItem() }}</span>&ndash;<span class="font-semibold text-[var(--text)]">{{ $pinjamKelas->lastItem() }}</span>
                            dari
                            <span class="font-semibold text-[var(--text)]">{{ $pinjamKelas->total() }}</span>
                            data
                        </p>

                        <div class="flex flex-wrap items-center gap-1">
                            @if($pinjamKelas->onFirstPage())
                                <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-[var(--muted)]/50">
                                    Prev
                                </span>
                            @else
                                <a href="{{ $pinjamKelas->previousPageUrl() }}" class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60">Prev</a>
                            @endif

                            @foreach($pinjamKelas->getUrlRange(1, $pinjamKelas->lastPage()) as $page => $url)
                                @if($page == $pinjamKelas->currentPage())
                                    <span class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg bg-[var(--emerald-deep)] px-3 text-sm font-semibold text-white shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($pinjamKelas->hasMorePages())
                                <a href="{{ $pinjamKelas->nextPageUrl() }}" class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60">Next</a>
                            @else
                                <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-[var(--muted)]/50">
                                    Next
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        @else

            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                    <i class="fas fa-book text-2xl"></i>
                </div>

                <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                    Belum Ada Peminjaman Kelas
                </p>

                <p class="mt-1 text-sm text-[var(--muted)]">
                    Silakan ajukan peminjaman melalui menu Input Buku.
                </p>

                <a href="{{ route('siswa.pinjamkelas.input') }}" class="mt-6 inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"><i class="fas fa-plus text-xs"></i> <span>Input Buku</span></a>
            </div>

        @endif

    </div>

</div>

@endsection