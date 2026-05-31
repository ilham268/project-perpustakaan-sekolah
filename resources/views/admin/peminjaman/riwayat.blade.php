@extends('layouts.admin')

@section('title', 'Riwayat Pengembalian')
@section('page-title', 'Riwayat Pengembalian')

@section('content')

<div class="space-y-6">

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

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>

                <span>{{ session('error') }}</span>
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
                    Data Pengembalian
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Riwayat Pengembalian
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    Pantau riwayat pengembalian buku, kondisi buku, denda, dan petugas yang memproses data.
                </p>
            </div>

            <a
                href="{{ route('admin.pengembalian.create') }}"
                class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                Tambah Pengembalian
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route('admin.peminjaman.riwayat') }}"
        id="filter-form"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
    >
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
            <div class="relative lg:col-span-8">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari judul buku atau nama peminjam..."
                    autocomplete="off"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="lg:col-span-3">
                <select
                    name="kondisi"
                    id="kondisi-select"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    <option value="" {{ !request('kondisi') ? 'selected' : '' }}>Semua Kondisi</option>
                    <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <a
                    href="{{ route('admin.peminjaman.riwayat') }}"
                    title="Reset Filter"
                    class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Total Pengembalian
            </p>

            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                {{ number_format($totalPengembalian) }}
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Bermasalah
            </p>

            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                {{ number_format($totalBermasalah) }}
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Total Denda
            </p>

            <p class="mt-2 truncate text-2xl font-extrabold tracking-tight text-slate-900">
                Rp {{ number_format($totalDendaSum, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-white px-5 py-4">
            <div class="flex flex-col gap-1">
                <h2 class="text-lg font-extrabold text-slate-900">
                    Data Riwayat Pengembalian
                </h2>

                <p class="text-sm text-slate-500">
                    Daftar buku yang sudah dikembalikan beserta kondisi, denda, dan petugas.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Peminjam
                        </th>

                        <th class="w-40 border border-slate-200 px-5 py-4 text-left">
                            Nomor Identitas
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                            Tgl Tempo
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                            Tgl Kembali
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Kondisi
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                            Denda
                        </th>

                        <th class="w-44 border border-slate-200 px-5 py-4 text-left">
                            Petugas
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($returns as $return)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $returns->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[280px] truncate font-semibold text-slate-800">
                                    {{ $return->loan->bookItem->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $return->loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-slate-800">
                                    {{ $return->loan->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $return->loan->user->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ $return->loan && $return->loan->tanggal_kembali ? \Carbon\Carbon::parse($return->loan->tanggal_kembali)->format('d M Y') : '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ $return->tanggal_pengembalian ? \Carbon\Carbon::parse($return->tanggal_pengembalian)->format('d M Y') : '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($return->kondisi == 'baik')
                                    <span class="font-semibold text-green-600">
                                        Baik
                                    </span>
                                @elseif($return->kondisi == 'rusak')
                                    <span class="font-semibold text-orange-600">
                                        Rusak
                                    </span>
                                @elseif($return->kondisi == 'hilang')
                                    <span class="font-semibold text-red-600">
                                        Hilang
                                    </span>
                                @else
                                    <span class="font-semibold text-slate-500">
                                        {{ ucfirst($return->kondisi ?? '-') }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($return->denda > 0)
                                    <span class="font-bold text-red-600">
                                        Rp {{ number_format($return->denda, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                <span class="block max-w-[180px] truncate">
                                    {{ $return->loan->petugas->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($return->denda > 0)
                                    <a
                                        href="{{ route('admin.pengembalian.invoice', $return->id) }}"
                                        title="Download Invoice"
                                        class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                    >
                                        Invoice
                                    </a>
                                @else
                                    <span class="text-sm text-slate-300">
                                        -
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-history text-2xl"></i>
                                </div>

                                <p class="mt-4 text-sm font-bold text-slate-700">
                                    Belum ada riwayat pengembalian
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Data pengembalian buku akan muncul di sini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($returns->total() > 0)
        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Menampilkan
                    <span class="font-semibold text-slate-700">{{ $returns->firstItem() }}</span>&ndash;<span class="font-semibold text-slate-700">{{ $returns->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-slate-700">{{ $returns->total() }}</span>
                    data
                </p>

                <div class="flex flex-wrap items-center gap-1">
                    @if($returns->onFirstPage())
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
                            Prev
                        </span>
                    @else
                        <a
                            href="{{ $returns->previousPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >
                            Prev
                        </a>
                    @endif

                    @foreach($returns->getUrlRange(1, $returns->lastPage()) as $page => $url)
                        @if($page == $returns->currentPage())
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

                    @if($returns->hasMorePages())
                        <a
                            href="{{ $returns->nextPageUrl() }}"
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

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var kondisiSelect = document.getElementById('kondisi-select');
        var debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 400);
            });
        }

        if (kondisiSelect) {
            kondisiSelect.addEventListener('change', function () {
                form.submit();
            });
        }
    })();
</script>

@endsection