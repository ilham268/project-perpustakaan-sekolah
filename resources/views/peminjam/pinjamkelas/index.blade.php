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
                    Peminjaman Kelas
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Buku Pinjaman Kelas
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    Daftar buku yang sedang dipinjam secara kolektif oleh kelas.
                </p>
            </div>

            <a
                href="{{ route('siswa.pinjamkelas.input') }}"
                class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                Input Buku
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Data
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalPinjamKelas }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        pinjaman
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Pending
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalPending }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Disetujui
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalDisetujui }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 bg-white px-5 py-4">
            <h2 class="text-lg font-extrabold text-slate-900">
                Data Buku Pinjaman Kelas
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Riwayat pengajuan dan status peminjaman buku kelas.
            </p>
        </div>

        @if($pinjamKelas->isNotEmpty())

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                                No
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Kategori
                            </th>

                            <th class="w-40 border border-slate-200 px-5 py-4 text-left">
                                Kode Buku
                            </th>

                            <th class="w-40 border border-slate-200 px-5 py-4 text-center">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach($pinjamKelas as $index => $item)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $pinjamKelas->firstItem() + $index }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="block max-w-[320px] truncate font-semibold text-slate-800">
                                        {{ $item->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $item->kode_buku ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @if($item->status == 'pending')
                                        <span class="font-semibold text-amber-600">
                                            Pending
                                        </span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="font-semibold text-emerald-600">
                                            Disetujui
                                        </span>
                                    @elseif($item->status == 'dikembalikan')
                                        <span class="font-semibold text-blue-600">
                                            Dikembalikan
                                        </span>
                                    @else
                                        <span class="font-semibold text-slate-500">
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

        @else

            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-bold text-slate-700">
                    Belum Ada Peminjaman Kelas
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Silakan ajukan peminjaman melalui menu Input Buku.
                </p>

                <a
                    href="{{ route('siswa.pinjamkelas.input') }}"
                    class="mt-6 inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    Input Buku
                </a>
            </div>

        @endif

    </div>

</div>

@endsection