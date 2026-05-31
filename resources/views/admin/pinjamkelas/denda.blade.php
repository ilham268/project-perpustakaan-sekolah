@extends('layouts.admin')

@section('title', 'Denda Peminjaman Kelas')
@section('page-title', 'Denda Peminjaman Kelas')

@section('content')

@php
    $currentStatus = $pinjam->status ?? 'pending';
    $statusText = 'Pending';

    if ($currentStatus === 'disetujui') {
        $statusText = 'Disetujui';
    } elseif ($currentStatus === 'dikembalikan') {
        $statusText = 'Dikembalikan';
    } elseif ($currentStatus === 'denda') {
        $statusText = 'Denda';
    }
@endphp

<div
    class="space-y-6"
    x-data="{
        kondisi: '{{ old('kondisi', $pinjam->kondisi ?? 'baik') }}',
        dendaRusak: {{ old('denda_rusak', 10000) }},
        dendaRusakFormatted: '10.000',
        dendaHilang: {{ old('denda_hilang', 100000) }},
        dendaHilangFormatted: '100.000',

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        },

        parseNumber(value) {
            if (!value) return 0;
            if (typeof value === 'number') return value;
            return parseInt(value.toString().replace(/\D/g, '')) || 0;
        },

        init() {
            this.dendaRusakFormatted = this.formatRupiah(this.dendaRusak);
            this.dendaHilangFormatted = this.formatRupiah(this.dendaHilang);
        },

        get dendaKondisi() {
            if (this.kondisi === 'rusak') {
                return this.dendaRusak || 0;
            }

            if (this.kondisi === 'hilang') {
                return this.dendaHilang || 0;
            }

            return 0;
        },

        get totalDenda() {
            return this.dendaKondisi;
        }
    }"
>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <span class="font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
            <span class="font-medium">
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
                    Peminjaman Kelas
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Denda Peminjaman Kelas
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Proses denda peminjaman buku kelas berdasarkan kondisi buku.
                </p>
            </div>

            <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Status
                    </p>

                    <p class="mt-1 truncate text-xl font-extrabold tracking-tight text-white">
                        {{ $statusText }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Total Denda
                    </p>

                    <p class="mt-1 truncate text-xl font-extrabold tracking-tight text-white" x-text="'Rp ' + formatRupiah(totalDenda)">
                        Rp 0
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Left --}}
        <div class="space-y-6 xl:col-span-2">

            {{-- Detail --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
                    <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                        Detail Peminjaman
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Data peminjaman kelas yang akan diproses dendanya.
                    </p>
                </div>

                <div class="p-5 md:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Nama Siswa
                            </p>

                            <p class="mt-1 truncate text-sm font-bold text-slate-900">
                                {{ $pinjam->user->name ?? '-' }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $pinjam->user->nomor_identitas ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Kelas
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ $pinjam->user->kelas ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Judul Buku / Kategori
                            </p>

                            <p class="mt-1 truncate text-sm font-bold text-slate-900">
                                {{ $pinjam->kategori->nama_kategori ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Kode Buku
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ $pinjam->kode_buku ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Status Saat Ini
                        </p>

                        @if($currentStatus === 'disetujui')
                            <p class="mt-1 text-sm font-bold text-emerald-700">
                                {{ $statusText }}
                            </p>
                        @elseif($currentStatus === 'dikembalikan')
                            <p class="mt-1 text-sm font-bold text-slate-700">
                                {{ $statusText }}
                            </p>
                        @elseif($currentStatus === 'denda')
                            <p class="mt-1 text-sm font-bold text-red-700">
                                {{ $statusText }}
                            </p>
                        @else
                            <p class="mt-1 text-sm font-bold text-amber-700">
                                {{ $statusText }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Form Denda --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
                    <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                        Form Proses Denda
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih kondisi buku dan sistem akan menghitung total denda berdasarkan kondisi buku.
                    </p>
                </div>

                <div class="p-5 md:p-6">
                    <form action="{{ route('admin.pinjamkelas.kelas.denda.store', $pinjam->id) }}" method="POST">
                        @csrf

                        <div>
                            <label class="mb-3 block text-sm font-bold text-slate-800">
                                Kondisi Buku <span class="text-red-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <label class="relative cursor-pointer">
                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="baik"
                                        class="peer sr-only"
                                        required
                                        x-model="kondisi"
                                    >

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-emerald-300 hover:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50">
                                        <p class="font-bold text-slate-800">
                                            Baik
                                        </p>

                                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                            Tidak ada denda.
                                        </p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="rusak"
                                        class="peer sr-only"
                                        x-model="kondisi"
                                    >

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-amber-300 hover:bg-amber-50 peer-checked:border-amber-500 peer-checked:bg-amber-50">
                                        <p class="font-bold text-slate-800">
                                            Rusak
                                        </p>

                                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                            Tambahkan denda kerusakan.
                                        </p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="hilang"
                                        class="peer sr-only"
                                        x-model="kondisi"
                                    >

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-red-300 hover:bg-red-50 peer-checked:border-red-500 peer-checked:bg-red-50">
                                        <p class="font-bold text-slate-800">
                                            Hilang
                                        </p>

                                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                            Tambahkan ganti rugi buku.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Denda Rusak --}}
                        <div
                            x-show="kondisi === 'rusak'"
                            x-cloak
                            class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4"
                        >
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Nominal Denda Kerusakan
                            </label>

                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-slate-600">
                                    Rp
                                </span>

                                <input
                                    type="text"
                                    x-model="dendaRusakFormatted"
                                    @input="
                                        dendaRusak = parseNumber($event.target.value);
                                        dendaRusakFormatted = formatRupiah(dendaRusak);
                                    "
                                    class="h-11 flex-1 rounded-lg border border-amber-200 bg-white px-4 text-sm font-semibold text-slate-700 transition focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-100"
                                    placeholder="Contoh: 10.000"
                                >
                            </div>

                            <input type="hidden" name="denda_rusak" :value="dendaRusak">
                        </div>

                        {{-- Denda Hilang --}}
                        <div
                            x-show="kondisi === 'hilang'"
                            x-cloak
                            class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4"
                        >
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Nominal Ganti Rugi
                            </label>

                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-slate-600">
                                    Rp
                                </span>

                                <input
                                    type="text"
                                    x-model="dendaHilangFormatted"
                                    @input="
                                        dendaHilang = parseNumber($event.target.value);
                                        dendaHilangFormatted = formatRupiah(dendaHilang);
                                    "
                                    class="h-11 flex-1 rounded-lg border border-red-200 bg-white px-4 text-sm font-semibold text-slate-700 transition focus:border-red-400 focus:outline-none focus:ring-4 focus:ring-red-100"
                                    placeholder="Contoh: 100.000"
                                >
                            </div>

                            <input type="hidden" name="denda_hilang" :value="dendaHilang">
                        </div>

                        <input type="hidden" name="denda" :value="totalDenda">

                        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                            <a
                                href="{{ route('admin.pinjamkelas.kelas') }}"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-red-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                            >
                                Proses Denda
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="xl:col-span-1">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm xl:sticky xl:top-6">
                <div class="border-b border-slate-100 bg-white px-5 py-5">
                    <h3 class="text-base font-bold text-slate-900">
                        Ringkasan Denda
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Total otomatis berdasarkan kondisi buku.
                    </p>
                </div>

                <div class="space-y-4 p-5">
                    <div
                        class="rounded-2xl border border-amber-200 bg-amber-50 p-4"
                        x-show="kondisi === 'rusak'"
                        x-cloak
                    >
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-amber-700">
                                Denda Kerusakan
                            </span>

                            <span class="font-bold text-amber-700" x-text="'Rp ' + formatRupiah(dendaRusak)">
                                Rp 0
                            </span>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-red-200 bg-red-50 p-4"
                        x-show="kondisi === 'hilang'"
                        x-cloak
                    >
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-red-700">
                                Ganti Rugi Hilang
                            </span>

                            <span class="font-bold text-red-700" x-text="'Rp ' + formatRupiah(dendaHilang)">
                                Rp 0
                            </span>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4"
                        x-show="kondisi === 'baik'"
                        x-cloak
                    >
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-emerald-700">
                                Kondisi Baik
                            </span>

                            <span class="font-bold text-emerald-700">
                                Rp 0
                            </span>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Total Denda
                        </p>

                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900" x-text="'Rp ' + formatRupiah(totalDenda)">
                            Rp 0
                        </p>

                        <p class="mt-2 text-xs text-slate-500">
                            Nominal ini akan dikirim saat tombol Proses Denda ditekan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

@endsection