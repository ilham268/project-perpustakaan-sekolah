@extends('layouts.admin')

@section('title', 'Buku Paket')
@section('page-title', 'Buku Paket')

@section('content')

@php
    $paketFilter = function ($q) {
        $q->where('jenis_koleksi', 'like', '%Paket%')
            ->orWhere('jenis_koleksi', 'like', '%Packet%')
            ->orWhere('jenis_koleksi', 'like', '%Pakett%')
            ->orWhere('jenis_koleksi', 'like', '%PKT%');
    };

    $tahunOptions = \App\Models\Book::query()
        ->where($paketFilter)
        ->whereNotNull('tahun_pengadaan')
        ->distinct()
        ->orderByDesc('tahun_pengadaan')
        ->pluck('tahun_pengadaan');

    $booksPaketQuery = \App\Models\Book::query()
        ->withCount([
            'bookItems as total_eksemplar',
            'bookItems as kode_terisi' => function ($q) {
                $q->whereNotNull('kode_buku')
                    ->where('kode_buku', '!=', '');
            },
            'bookItems as stok_tersedia' => function ($q) {
                $q->where('status', 'available')
                    ->whereNotNull('kode_buku')
                    ->where('kode_buku', '!=', '');
            },
        ])
        ->where($paketFilter);

    if (request('search')) {
        $search = request('search');

        $booksPaketQuery->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
                ->orWhere('penulis', 'like', "%{$search}%")
                ->orWhere('penerbit', 'like', "%{$search}%")
                ->orWhere('nomor_klasifikasi', 'like', "%{$search}%")
                ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
        });
    }

    if (request('tahun_pengadaan')) {
        $booksPaketQuery->where('tahun_pengadaan', request('tahun_pengadaan'));
    }

    $booksPaket = $booksPaketQuery
        ->orderByDesc('tahun_pengadaan')
        ->orderBy('judul')
        ->paginate(10)
        ->withQueryString();

    $totalPaket = \App\Models\Book::query()
        ->where($paketFilter)
        ->count();

    $totalEksemplar = \App\Models\BookItem::whereHas('book', $paketFilter)->count();

    $totalKodeTerisi = \App\Models\BookItem::whereHas('book', $paketFilter)
        ->whereNotNull('kode_buku')
        ->where('kode_buku', '!=', '')
        ->count();
@endphp

<div class="space-y-6">

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
                    Koleksi Buku Paket
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Buku Paket
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Daftar Buku Paket untuk peminjaman siswa. Buku Paket tidak lagi dipisah berdasarkan kelas atau jurusan.
                </p>
            </div>

            <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-3 lg:w-[520px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Total Judul
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                        {{ $totalPaket }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Total Eksemplar
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                        {{ $totalEksemplar }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Kode Terisi
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                        {{ $totalKodeTerisi }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
            <div class="flex flex-col gap-1">
                <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                    Daftar Buku Paket
                </h2>

                <p class="text-sm text-slate-500">
                    Admin input kode buku dari menu detail buku, lalu kode ditempel pada buku fisik.
                </p>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ url()->current() }}" id="filter-form" class="mt-5">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="relative md:col-span-6">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                        <input
                            type="text"
                            name="search"
                            id="search-input"
                            value="{{ request('search') }}"
                            placeholder="Cari judul, penulis, penerbit, klasifikasi..."
                            autocomplete="off"
                            class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                    </div>

                    <div class="md:col-span-3">
                        <select
                            name="tahun_pengadaan"
                            id="tahun-select"
                            class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Semua Tahun</option>

                            @foreach($tahunOptions as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_pengadaan') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:col-span-3">
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
            <table class="w-full min-w-[1100px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Tahun Data
                        </th>

                        <th class="w-48 border border-slate-200 px-5 py-4 text-left">
                            Penulis
                        </th>

                        <th class="w-48 border border-slate-200 px-5 py-4 text-left">
                            Penerbit
                        </th>

                        <th class="w-24 border border-slate-200 px-5 py-4 text-center">
                            Eksemplar
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Kode
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Tersedia
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($booksPaket as $index => $book)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $booksPaket->firstItem() + $index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <div class="max-w-[340px] font-semibold text-slate-800">
                                    {{ $book->judul }}
                                </div>

                                <div class="mt-1 text-xs text-slate-400">
                                    No. Klasifikasi: {{ $book->nomor_klasifikasi ?? '-' }}
                                </div>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-600">
                                {{ $book->tahun_pengadaan ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $book->penulis ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $book->penerbit ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <span class="font-bold text-slate-700">
                                    {{ $book->total_eksemplar }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($book->kode_terisi == $book->total_eksemplar && $book->total_eksemplar > 0)
                                    <span class="font-bold text-emerald-700">
                                        {{ $book->kode_terisi }}/{{ $book->total_eksemplar }}
                                    </span>
                                @else
                                    <span class="font-bold text-amber-700">
                                        {{ $book->kode_terisi }}/{{ $book->total_eksemplar }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <span class="font-bold text-emerald-700">
                                    {{ $book->stok_tersedia }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <a
                                    href="{{ route('books.show', $book->id) }}"
                                    class="inline-flex h-9 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                >
                                    Input Kode
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-book-open text-2xl"></i>
                                </div>

                                <p class="mt-4 text-base font-bold text-slate-700">
                                    Belum ada Buku Paket
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Import Excel BOS dulu, lalu pastikan sheet Paket sudah terbaca.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-slate-100 bg-white px-5 py-4">
            {{ $booksPaket->links() }}
        </div>
    </div>

</div>

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var tahunSelect = document.getElementById('tahun-select');
        var debounceTimer;

        if (form && searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 400);
            });
        }

        if (form && tahunSelect) {
            tahunSelect.addEventListener('change', function () {
                form.submit();
            });
        }
    })();
</script>

@endsection