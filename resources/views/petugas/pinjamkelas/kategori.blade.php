@extends('layouts.petugas')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

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

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Kategori Buku
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Lihat kategori buku untuk peminjaman kelas agar data lebih tertata dan mudah ditemukan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 w-full lg:w-[360px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Total Kategori
                            </p>

                            <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                {{ $kategoris->total() }}
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas fa-layer-group text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Data Kelas
                            </p>

                            <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                {{ $kelasList->count() }}
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas fa-school text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header + Filter --}}
        <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-folder-tree"></i>
                </div>

                <div>
                    <h2 class="text-lg md:text-xl font-bold text-slate-900">
                        Daftar Kategori
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Cari dan filter kategori berdasarkan nama kategori, kelas, atau jurusan.
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
                                placeholder="Cari kategori, kelas, atau jurusan..."
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
            <option value="{{ $kelas->nama_kelas }}" {{ request('kelas') == $kelas->nama_kelas ? 'selected' : '' }}>
                {{ $kelas->nama_kelas }}
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
            <table class="w-full min-w-[980px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kategori
                        </th>

                        <th class="w-40 border border-slate-200 px-5 py-4 text-center">
                            Kelas
                        </th>

                        <th class="w-40 border border-slate-200 px-5 py-4 text-center">
                            Jurusan
                        </th>

                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Input
                        </th>

                        <th class="w-32 border border-slate-200 px-5 py-4 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody id="kategoriTable">
                    @forelse($kategoris as $index => $item)
                        @php
                            $jurusanData = $kelasJurusanMap->get($item->kelas, '-');
                        @endphp

                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $kategoris->firstItem() + $index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $item->nama_kategori }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ $item->kelas }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ $jurusanData ?: '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <a
                                    href="{{ route('petugas.pinjamkelas.create', $item->id) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-600 ring-1 ring-emerald-100 transition hover:bg-emerald-100"
                                >
                                    <i class="fas fa-arrow-right text-xs"></i>
                                    Input
                                </a>
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        onclick="alert('Edit belum tersedia untuk petugas')"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                        title="Edit"
                                    >
                                        <i class="fas fa-pen text-sm"></i>
                                    </button>

                                    <button
                                        type="button"
                                        onclick="alert('Hapus belum tersedia untuk petugas')"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                        title="Hapus"
                                    >
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-folder-open text-2xl"></i>
                                </div>

                                <p class="mt-4 text-base font-bold text-slate-700">
                                    Belum ada data kategori
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Data kategori akan muncul setelah admin menambahkan kategori buku.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-slate-100 bg-white/80 px-5 py-4">
            {{ $kategoris->links() }}
        </div>
    </div>
</div>

@endsection