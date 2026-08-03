@extends('layouts.peminjam')

@section('title', 'Denda Saya')
@section('page-title', 'Denda Saya')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10">
            <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                Data&nbsp;Denda
            </p>

            <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                Denda Saya
            </h1>

            <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                Pantau tagihan denda dari peminjaman buku dan peminjaman kelas.
            </p>
        </div>
    </div>

    @if($denda->total() < 1)
        <div class="rounded-2xl border border-[var(--hairline)] bg-white px-6 py-16 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)]">
                <i class="fas fa-circle-check text-2xl"></i>
            </div>

            <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                Selamat! Tidak Ada Denda
            </p>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Anda tidak memiliki denda yang harus dibayarkan.
            </p>
        </div>
    @else
        @php
            $totalBelumLunas = $totalDenda ?? 0;
            $semuaLunas = $totalBelumLunas <= 0;
        @endphp

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            {{-- Total Denda --}}
            <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg {{ $semuaLunas ? 'hover:shadow-[var(--emerald)]/10' : 'hover:shadow-red-100/60' }}">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full {{ $semuaLunas ? 'bg-[var(--emerald-tint)] group-hover:bg-[var(--emerald-tint)]' : 'bg-red-50 group-hover:bg-red-100' }} transition"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                            {{ $semuaLunas ? 'Status Denda' : 'Total Belum Lunas' }}
                        </p>

                        <p class="font-mono-stat mt-2 truncate text-2xl font-semibold tracking-tight {{ $semuaLunas ? 'text-[var(--emerald-deep)]' : 'text-red-600' }}">
                            @if($semuaLunas)
                                Lunas
                            @else
                                Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}
                            @endif
                        </p>

                        <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                            {{ $semuaLunas ? 'tidak ada tagihan' : 'segera dilunasi' }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $semuaLunas ? 'bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-[var(--emerald)]/15' : 'bg-red-50 text-red-600 ring-red-100' }} ring-1">
                        @if($semuaLunas)
                            <i class="fas fa-check-circle text-xl"></i>
                        @else
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Total Data --}}
            <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100/60">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:bg-sky-100"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                            Total Data
                        </p>

                        <p class="font-mono-stat mt-2 text-3xl font-semibold tracking-tight text-[var(--text)]">
                            {{ $denda->total() }}
                        </p>

                        <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                            denda
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-100/60">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                            Keterangan
                        </p>

                        <p class="mt-2 text-xl font-semibold tracking-tight {{ $semuaLunas ? 'text-[var(--emerald-deep)]' : 'text-amber-700' }}">
                            {{ $semuaLunas ? 'Aman' : 'Perlu Dibayar' }}
                        </p>

                        <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                            {{ $semuaLunas ? 'semua lunas' : 'ada tagihan' }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- Search --}}
        <form
            method="GET"
            action="{{ route('siswa.denda.index') }}"
            id="filter-form"
            class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm"
        >
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="relative lg:col-span-11">
                    <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        value="{{ request('search') }}"
                        placeholder="Cari judul, kategori, atau kode buku..."
                        autocomplete="off"
                        class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    >
                </div>

                <div class="lg:col-span-1">
                    
                        href="{{ route('siswa.denda.index') }}"
                        class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/50 hover:text-[var(--forest)]"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
            <div class="border-b border-[var(--hairline)] px-6 py-5">
                <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                    Daftar Denda
                </h2>

                <p class="mt-1 text-sm text-[var(--muted)]">
                    Daftar denda yang tercatat pada akun Anda.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] border-collapse text-sm">
                    <thead>
                        <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                            <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                No
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Judul / Kategori
                            </th>

                            <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Kode Buku
                            </th>

                            <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Sumber
                            </th>

                            <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Kondisi
                            </th>

                            <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-right font-semibold">
                                Denda
                            </th>

                            <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach($denda as $item)
                            @php
                                $isPaid = ($item->status ?? 'pending') == 'paid';
                            @endphp

                            <tr class="transition-colors hover:bg-[var(--sand)]/30">
                                <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                    {{ $denda->firstItem() + $loop->index }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
                                    <span class="block max-w-[280px] truncate font-semibold text-[var(--text)]">
                                        {{ $item->judul ?? '-' }}
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                    {{ $item->kode_buku ?? '-' }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                    @if(($item->tipe ?? '') == 'kelas')
                                        <span class="font-semibold text-purple-700">
                                            Pinjam Kelas
                                        </span>
                                    @else
                                        <span class="font-semibold text-sky-700">
                                            Pinjam Buku
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                    @if(($item->kondisi ?? '') == 'rusak')
                                        <span class="font-semibold text-orange-600">
                                            Rusak
                                        </span>
                                    @elseif(($item->kondisi ?? '') == 'hilang')
                                        <span class="font-semibold text-red-600">
                                            Hilang
                                        </span>
                                    @elseif(($item->kondisi ?? '') == 'baik')
                                        <span class="font-semibold text-[var(--emerald-deep)]">
                                            Baik
                                        </span>
                                    @else
                                        <span class="font-semibold text-[var(--text)]/70">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-right">
                                    <span class="font-bold {{ $isPaid ? 'text-[var(--emerald-deep)]' : 'text-red-600' }}">
                                        Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                    @if($isPaid)
                                        <span class="font-semibold text-[var(--emerald-deep)]">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="font-semibold text-amber-600">
                                            Belum Lunas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($denda->total() > 0)
            <div class="rounded-2xl border border-[var(--hairline)] bg-white px-5 py-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-[var(--muted)]">
                        Menampilkan
                        <span class="font-semibold text-[var(--text)]">{{ $denda->firstItem() }}</span>&ndash;<span class="font-semibold text-[var(--text)]">{{ $denda->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-[var(--text)]">{{ $denda->total() }}</span>
                        data
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        @if($denda->onFirstPage())
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-[var(--muted)]/50">
                                Prev
                            </span>
                        @else
                            
                                href="{{ $denda->previousPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                            >
                                Prev
                            </a>
                        @endif

                        @foreach($denda->getUrlRange(1, $denda->lastPage()) as $page => $url)
                            @if($page == $denda->currentPage())
                                <span class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg bg-[var(--emerald-deep)] px-3 text-sm font-semibold text-white shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                
                                    href="{{ $url }}"
                                    class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($denda->hasMorePages())
                            
                                href="{{ $denda->nextPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                            >
                                Next
                            </a>
                        @else
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-[var(--muted)]/50">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
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
    })();
</script>

@endsection