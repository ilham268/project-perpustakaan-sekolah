@extends('layouts.peminjam')

@section('title', 'Denda Saya')
@section('page-title', 'Denda Saya')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                Data Denda
            </p>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                Denda Saya
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                Pantau tagihan denda dari peminjaman buku dan peminjaman kelas.
            </p>
        </div>
    </div>

    @if($denda->total() < 1)
        <div class="rounded-3xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
            <p class="text-base font-bold text-slate-700">
                Selamat! Tidak Ada Denda
            </p>

            <p class="mt-1 text-sm text-slate-400">
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
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full {{ $semuaLunas ? 'bg-emerald-50 group-hover:bg-emerald-100' : 'bg-red-50 group-hover:bg-red-100' }} transition"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-500">
                            {{ $semuaLunas ? 'Status Denda' : 'Total Belum Lunas' }}
                        </p>

                        <p class="mt-2 truncate text-2xl font-bold tracking-tight {{ $semuaLunas ? 'text-emerald-700' : 'text-red-600' }}">
                            @if($semuaLunas)
                                Lunas
                            @else
                                Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}
                            @endif
                        </p>

                        <p class="mt-1 text-xs font-medium text-slate-400">
                            {{ $semuaLunas ? 'tidak ada tagihan' : 'segera dilunasi' }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $semuaLunas ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-red-50 text-red-600 ring-red-100' }} ring-1">
                        @if($semuaLunas)
                            <i class="fas fa-check-circle text-xl"></i>
                        @else
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Total Data --}}
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-500">
                            Total Data
                        </p>

                        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                            {{ $denda->total() }}
                        </p>

                        <p class="mt-1 text-xs font-medium text-slate-400">
                            denda
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-500">
                            Keterangan
                        </p>

                        <p class="mt-2 text-xl font-bold tracking-tight {{ $semuaLunas ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $semuaLunas ? 'Aman' : 'Perlu Dibayar' }}
                        </p>

                        <p class="mt-1 text-xs font-medium text-slate-400">
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
            class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
        >
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="relative lg:col-span-11">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        value="{{ request('search') }}"
                        placeholder="Cari judul, kategori, atau kode buku..."
                        autocomplete="off"
                        class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                </div>

                <div class="lg:col-span-1">
                    <a
                        href="{{ route('siswa.denda.index') }}"
                        class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-white px-5 py-4">
                <h2 class="text-lg font-extrabold text-slate-900">
                    Daftar Denda
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Daftar denda yang tercatat pada akun Anda.
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
                                Judul / Kategori
                            </th>

                            <th class="w-36 border border-slate-200 px-5 py-4 text-left">
                                Kode Buku
                            </th>

                            <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                                Sumber
                            </th>

                            <th class="w-32 border border-slate-200 px-5 py-4 text-center">
                                Kondisi
                            </th>

                            <th class="w-36 border border-slate-200 px-5 py-4 text-right">
                                Denda
                            </th>

                            <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach($denda as $item)
                            @php
                                $isPaid = ($item->status ?? 'pending') == 'paid';
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $denda->firstItem() + $loop->index }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="block max-w-[280px] truncate font-semibold text-slate-800">
                                        {{ $item->judul ?? '-' }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $item->kode_buku ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @if(($item->tipe ?? '') == 'kelas')
                                        <span class="font-semibold text-purple-700">
                                            Pinjam Kelas
                                        </span>
                                    @else
                                        <span class="font-semibold text-blue-700">
                                            Pinjam Buku
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @if(($item->kondisi ?? '') == 'rusak')
                                        <span class="font-semibold text-orange-600">
                                            Rusak
                                        </span>
                                    @elseif(($item->kondisi ?? '') == 'hilang')
                                        <span class="font-semibold text-red-600">
                                            Hilang
                                        </span>
                                    @elseif(($item->kondisi ?? '') == 'baik')
                                        <span class="font-semibold text-green-600">
                                            Baik
                                        </span>
                                    @else
                                        <span class="font-semibold text-slate-500">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-right">
                                    <span class="font-bold {{ $isPaid ? 'text-green-600' : 'text-red-600' }}">
                                        Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @if($isPaid)
                                        <span class="font-semibold text-green-600">
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
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $denda->firstItem() }}</span>&ndash;<span class="font-semibold text-slate-700">{{ $denda->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $denda->total() }}</span>
                        data
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        @if($denda->onFirstPage())
                            <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
                                Prev
                            </span>
                        @else
                            <a
                                href="{{ $denda->previousPageUrl() }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                            >
                                Prev
                            </a>
                        @endif

                        @foreach($denda->getUrlRange(1, $denda->lastPage()) as $page => $url)
                            @if($page == $denda->currentPage())
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

                        @if($denda->hasMorePages())
                            <a
                                href="{{ $denda->nextPageUrl() }}"
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