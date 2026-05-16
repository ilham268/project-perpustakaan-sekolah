@extends('layouts.admin')

@section('title', 'Riwayat Pengembalian')
@section('page-title', 'Riwayat Pengembalian')

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

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Riwayat Pengembalian
        </h3>

        <a
            href="{{ route('admin.pengembalian.create') }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Pengembalian</span>
        </a>
    </div>

    {{-- Search & Filter --}}
    <form
        method="GET"
        action="{{ route('admin.peminjaman.riwayat') }}"
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
                    placeholder="Cari judul buku atau nama peminjam..."
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <span class="text-sm font-semibold text-slate-600">
                    Kondisi:
                </span>

                <select
                    name="kondisi"
                    id="kondisi-select"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                >
                    <option value="" {{ !request('kondisi') ? 'selected' : '' }}>Semua</option>
                    <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>

        </div>
    </form>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Total Pengembalian --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Total Pengembalian
                    </p>

                    <p class="mt-2 truncate text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($totalPengembalian) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-rotate-left text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Bermasalah --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Bermasalah
                    </p>

                    <p class="mt-2 truncate text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($totalBermasalah) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-triangle-exclamation text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Denda --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-red-50 transition group-hover:bg-red-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Total Denda
                    </p>

                    <p class="mt-2 truncate text-2xl font-bold tracking-tight text-slate-900">
                        Rp {{ number_format($totalDendaSum, 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-1 ring-red-100">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Peminjam
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nomor Identitas
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tgl Tempo
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tgl Kembali
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Kondisi
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Denda
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Petugas
                        </th>
                        <th class="w-20 border border-slate-200 px-5 py-4 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($returns as $return)
                        <tr class="transition-colors hover:bg-slate-50">

                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $returns->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $return->loan->bookItem->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $return->loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
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
                                {{ $return->loan->petugas->name ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($return->denda > 0)
                                    <a
                                        href="{{ route('admin.pengembalian.invoice', $return->id) }}"
                                        title="Download Invoice"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 transition hover:bg-emerald-100"
                                    >
                                        <i class="fas fa-file-invoice text-sm"></i>
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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $returns->firstItem() }}&ndash;{{ $returns->lastItem() }} dari {{ $returns->total() }} data
            </p>

            <div class="flex flex-wrap items-center gap-1">

                @if($returns->onFirstPage())
                    <span class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-300">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a
                        href="{{ $returns->previousPageUrl() }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                    >
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($returns->getUrlRange(1, $returns->lastPage()) as $page => $url)
                    @if($page == $returns->currentPage())
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

                @if($returns->hasMorePages())
                    <a
                        href="{{ $returns->nextPageUrl() }}"
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