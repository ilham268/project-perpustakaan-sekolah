@extends('layouts.admin')

@section('title', 'Rekap Denda')
@section('page-title', 'Rekap Denda')

@section('content')

<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('error') }}
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
                    Data Denda
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Rekap Denda
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    Atur denda keterlambatan dan pantau seluruh data denda peminjaman.
                </p>
            </div>

            <button
                type="button"
                onclick="openExportModal()"
                class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                Export Excel
            </button>
        </div>
    </div>

    {{-- Setting Denda --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                        Setting Denda
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Setting ini dipakai untuk peminjaman buku Referensi. Setelah disimpan, setting akan tampil terkunci.
                    </p>
                </div>

                <p id="setting-lock-label" class="text-sm font-bold text-slate-800">
                    Terkunci
                </p>
            </div>
        </div>

        <form id="setting-form" action="{{ route('admin.denda.setting.update') }}" method="POST" class="p-5 md:p-6">
            @csrf
            @method('PUT')

            <div class="mb-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-sm font-bold text-slate-800">
                    Setting aktif saat ini
                </p>

                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Lama Pinjam Default
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-800">
                            {{ $lamaPinjamDefault ?? 7 }} hari
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Denda Telat Per Hari
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-800">
                            Rp {{ number_format($dendaTelatPerHari ?? 10000, 0, ',', '.') }} / hari
                        </p>
                    </div>
                </div>

                <p class="mt-3 text-xs leading-relaxed text-slate-500">
                    Kalau setting ingin diganti, klik Ubah Setting terlebih dahulu. Selama terkunci, input tidak bisa diedit bebas.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Lama Pinjam Default <span class="text-red-500">*</span>
                    </label>

                    <div class="flex items-center gap-3">
                        <input
                            type="number"
                            name="lama_pinjam_default"
                            value="{{ old('lama_pinjam_default', $lamaPinjamDefault ?? 7) }}"
                            min="1"
                            required
                            disabled
                            data-setting-input
                            class="h-11 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                        >

                        <span class="shrink-0 text-sm font-semibold text-slate-500">
                            hari
                        </span>
                    </div>

                    @error('lama_pinjam_default')
                        <p class="mt-2 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-400">
                        Contoh: 7 berarti tanggal kembali otomatis 7 hari setelah tanggal pinjam.
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Denda Telat Per Hari <span class="text-red-500">*</span>
                    </label>

                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-bold text-slate-600">
                            Rp
                        </span>

                        <input
                            type="number"
                            name="denda_telat_per_hari"
                            value="{{ old('denda_telat_per_hari', $dendaTelatPerHari ?? 10000) }}"
                            min="0"
                            required
                            disabled
                            data-setting-input
                            class="h-11 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                        >

                        <span class="shrink-0 text-sm font-semibold text-slate-500">
                            / hari
                        </span>
                    </div>

                    @error('denda_telat_per_hari')
                        <p class="mt-2 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-400">
                        Contoh: 5000 berarti telat 2 hari = Rp 10.000.
                    </p>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4">
                <p class="text-sm font-bold text-blue-700">
                    Cara kerja denda
                </p>

                <p class="mt-1 text-sm leading-relaxed text-blue-600">
                    Denda dihitung saat buku dikembalikan. Sistem membandingkan tanggal pengembalian dengan tanggal kembali. Kalau lewat, denda = jumlah hari telat x denda per hari.
                </p>
            </div>

            <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    id="edit-setting-button"
                    onclick="enableSettingEdit()"
                    class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    Ubah Setting
                </button>

                <button
                    type="button"
                    id="cancel-setting-button"
                    onclick="cancelSettingEdit()"
                    class="hidden h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="save-setting-button"
                    class="hidden h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    Simpan Setting
                </button>
            </div>
        </form>
    </div>

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route('admin.denda.index') }}"
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
                    placeholder="Cari nama peminjam, judul, atau kode buku..."
                    autocomplete="off"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="lg:col-span-3">
                <select
                    name="status"
                    id="status-select"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    <option value="" {{ !request('status') || request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <a
                    href="{{ route('admin.denda.index') }}"
                    class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Total Denda --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Total Denda
                    </p>

                    <p class="mt-2 truncate text-2xl font-bold tracking-tight text-slate-900">
                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-sack-dollar text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Belum Dibayar --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Belum Dibayar
                    </p>

                    <p class="mt-2 truncate text-2xl font-bold tracking-tight text-slate-900">
                        Rp {{ number_format($totalPending, 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Sudah Dibayar --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Sudah Dibayar
                    </p>

                    <p class="mt-2 truncate text-2xl font-bold tracking-tight text-slate-900">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-money-check-dollar text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-white px-5 py-4">
            <h2 class="text-lg font-extrabold text-slate-900">
                Data Denda
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Daftar seluruh denda dari pinjam buku dan pinjam kelas.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">No</th>
                        <th class="border border-slate-200 px-5 py-4 text-left">Judul / Kategori</th>
                        <th class="border border-slate-200 px-5 py-4 text-left">Kode Buku</th>
                        <th class="border border-slate-200 px-5 py-4 text-left">Peminjam</th>
                        <th class="border border-slate-200 px-5 py-4 text-left">Nomor Identitas</th>
                        <th class="border border-slate-200 px-5 py-4 text-center">Sumber</th>
                        <th class="border border-slate-200 px-5 py-4 text-center">Kondisi</th>
                        <th class="border border-slate-200 px-5 py-4 text-center">Denda</th>
                        <th class="border border-slate-200 px-5 py-4 text-center">Status</th>
                        <th class="border border-slate-200 px-5 py-4 text-center">Tanggal</th>
                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($denda as $item)
                        @php
                            $tipe = $item->tipe ?? '';
                            $status = $item->status ?? 'pending';
                            $notaRoute = $item->invoice_route ?? null;
                            $tanggal = $item->tanggal_kembali ?? $item->tanggal_pengembalian ?? null;
                        @endphp

                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $denda->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[260px] truncate font-semibold text-slate-800">
                                    {{ $item->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $item->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-slate-800">
                                    {{ $item->nama_peminjam ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $item->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($tipe == 'kelas')
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
                                @if(($item->kondisi ?? '') == 'baik')
                                    <span class="font-semibold text-green-600">
                                        Baik
                                    </span>
                                @elseif(($item->kondisi ?? '') == 'rusak')
                                    <span class="font-semibold text-orange-600">
                                        Rusak
                                    </span>
                                @elseif(($item->kondisi ?? '') == 'hilang')
                                    <span class="font-semibold text-red-600">
                                        Hilang
                                    </span>
                                @else
                                    <span class="font-semibold text-slate-500">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <span class="font-bold text-red-600">
                                    Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($status == 'paid')
                                    <span class="font-semibold text-green-600">
                                        Lunas
                                    </span>
                                @else
                                    <span class="font-semibold text-amber-600">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ !empty($tanggal) ? \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') : '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($status == 'paid')
                                        @if(!empty($notaRoute))
                                            <a
                                                href="{{ $notaRoute }}"
                                                title="Unduh Nota Pembayaran"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                                            >
                                                Nota
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">
                                                Nota belum ada
                                            </span>
                                        @endif
                                    @else
                                        <form action="{{ route('admin.denda.paid', [$tipe, $item->id]) }}" method="POST">
                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Tandai denda ini sebagai lunas?')"
                                                title="Tandai Lunas"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                            >
                                                Tandai Lunas
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border border-slate-200 px-6 py-16 text-center">
                                <p class="text-sm font-bold text-slate-700">
                                    Tidak ada data denda
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Belum ada pengembalian atau peminjaman kelas dengan denda.
                                </p>
                            </td>
                        </tr>
                    @endforelse
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

</div>

<x-export-modal
    :route="route('admin.denda.export')"
    title="Export Laporan Denda"
/>

<script>
    (function () {
        var settingForm = document.getElementById('setting-form');
        var settingInputs = Array.prototype.slice.call(document.querySelectorAll('[data-setting-input]'));
        var editButton = document.getElementById('edit-setting-button');
        var cancelButton = document.getElementById('cancel-setting-button');
        var saveButton = document.getElementById('save-setting-button');
        var lockLabel = document.getElementById('setting-lock-label');

        window.enableSettingEdit = function () {
            settingInputs.forEach(function (input) {
                input.disabled = false;
            });

            if (lockLabel) {
                lockLabel.textContent = 'Mode Ubah';
                lockLabel.classList.remove('text-slate-800');
                lockLabel.classList.add('text-emerald-700');
            }

            if (editButton) {
                editButton.classList.add('hidden');
            }

            if (cancelButton) {
                cancelButton.classList.remove('hidden');
                cancelButton.classList.add('inline-flex');
            }

            if (saveButton) {
                saveButton.classList.remove('hidden');
                saveButton.classList.add('inline-flex');
            }

            if (settingInputs[0]) {
                settingInputs[0].focus();
            }
        };

        window.cancelSettingEdit = function () {
            if (settingForm) {
                settingForm.reset();
            }

            settingInputs.forEach(function (input) {
                input.disabled = true;
            });

            if (lockLabel) {
                lockLabel.textContent = 'Terkunci';
                lockLabel.classList.remove('text-emerald-700');
                lockLabel.classList.add('text-slate-800');
            }

            if (editButton) {
                editButton.classList.remove('hidden');
            }

            if (cancelButton) {
                cancelButton.classList.add('hidden');
                cancelButton.classList.remove('inline-flex');
            }

            if (saveButton) {
                saveButton.classList.add('hidden');
                saveButton.classList.remove('inline-flex');
            }
        };

        if (settingForm) {
            settingForm.addEventListener('submit', function () {
                settingInputs.forEach(function (input) {
                    input.disabled = false;
                });
            });
        }
    })();

    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var statusSelect = document.getElementById('status-select');
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

        if (statusSelect) {
            statusSelect.addEventListener('change', function () {
                form.submit();
            });
        }
    })();
</script>

@endsection