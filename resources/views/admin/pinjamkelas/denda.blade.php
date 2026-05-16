@extends('layouts.admin')

@section('title', 'Denda Peminjaman Kelas')
@section('page-title', 'Denda Peminjaman Kelas')

@section('content')

@php
    $currentStatus = $pinjam->status ?? 'pending';

    $statusClass = 'bg-amber-50 text-amber-700 ring-amber-100';
    $statusIcon = 'fa-clock';
    $statusText = 'Pending';

    if ($currentStatus === 'disetujui') {
        $statusClass = 'bg-emerald-50 text-emerald-700 ring-emerald-100';
        $statusIcon = 'fa-check';
        $statusText = 'Disetujui';
    } elseif ($currentStatus === 'dikembalikan') {
        $statusClass = 'bg-slate-100 text-slate-700 ring-slate-200';
        $statusIcon = 'fa-rotate-left';
        $statusText = 'Dikembalikan';
    } elseif ($currentStatus === 'denda') {
        $statusClass = 'bg-red-50 text-red-700 ring-red-100';
        $statusIcon = 'fa-triangle-exclamation';
        $statusText = 'Denda';
    }
@endphp

<div
    class="space-y-6"
    x-data="{
        kondisi: 'baik',
        dendaRusak: 10000,
        dendaRusakFormatted: '10.000',
        dendaHilang: 100000,
        dendaHilangFormatted: '100.000',
        tanggalKembali: '{{ $pinjam->tanggal_kembali }}',

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

        get daysLate() {
            if (!this.tanggalKembali) return 0;

            const kembali = new Date(this.tanggalKembali);
            const sekarang = new Date();

            if (isNaN(kembali.getTime())) return 0;

            kembali.setHours(0, 0, 0, 0);
            sekarang.setHours(0, 0, 0, 0);

            const diff = Math.floor((sekarang - kembali) / (1000 * 60 * 60 * 24));
            return diff > 0 ? diff : 0;
        },

        get dendaKeterlambatan() {
            return this.daysLate * 10000;
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
            return this.dendaKeterlambatan + this.dendaKondisi;
        },

        get hariTerlambatText() {
            if (this.daysLate === 0) return 'Tidak terlambat';
            if (this.daysLate === 1) return 'Terlambat 1 hari';
            return 'Terlambat ' + this.daysLate + ' hari';
        }
    }"
>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-check"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-red-600 ring-1 ring-red-100">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <span class="font-medium">{{ session('error') }}</span>
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
                    Denda Peminjaman Kelas
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Proses pengembalian buku kelas, cek keterlambatan, kondisi buku, dan hitung total denda secara otomatis.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 w-full lg:w-[360px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Status
                            </p>
                            <p class="mt-1 text-lg font-extrabold tracking-tight text-white truncate">
                                {{ $statusText }}
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas {{ $statusIcon }} text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Total Denda
                            </p>
                            <p class="mt-1 text-xl font-extrabold tracking-tight text-white" x-text="'Rp ' + formatRupiah(totalDenda)">
                                Rp 0
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas fa-wallet text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Left: Detail + Form --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Detail Peminjaman --}}
            <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                            <i class="fas fa-clipboard-list text-lg"></i>
                        </div>

                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-slate-900">
                                Detail Peminjaman
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Data peminjaman kelas yang akan diproses dendanya.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Siswa --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-user-graduate"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Nama Siswa
                                    </p>
                                    <p class="mt-1 truncate text-sm font-bold text-slate-900">
                                        {{ $pinjam->user->name ?? '-' }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $pinjam->user->nomor_identitas ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Kelas --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-school"></i>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Kelas
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">
                                        {{ $pinjam->user->kelas ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-book-open"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Judul Buku / Kategori
                                    </p>
                                    <p class="mt-1 truncate text-sm font-bold text-slate-900">
                                        {{ $pinjam->kategori->nama_kategori ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Kode Buku --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-barcode"></i>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Kode Buku
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">
                                        {{ $pinjam->kode_buku ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Tanggal Pinjam --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Tanggal Pinjam
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">
                                        {{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Tanggal Kembali --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-calendar-check"></i>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Tanggal Harus Kembali
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">
                                        {{ $pinjam->tanggal_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_kembali)->translatedFormat('d M Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }} text-[10px]"></i>
                            Status: {{ $statusText }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Form Denda --}}
            <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-1 ring-red-100">
                            <i class="fas fa-money-bill-wave text-lg"></i>
                        </div>

                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-slate-900">
                                Form Proses Denda
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Pilih kondisi buku dan sistem akan menghitung total denda otomatis.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6">
                    <form action="{{ route('admin.pinjamkelas.kelas.denda.store', $pinjam->id) }}" method="POST">
                        @csrf

                        {{-- Keterlambatan --}}
                        <div
                            class="mb-6 rounded-2xl border p-4"
                            :class="daysLate > 0
                                ? 'border-red-100 bg-red-50'
                                : 'border-emerald-100 bg-emerald-50'"
                        >
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white ring-1"
                                    :class="daysLate > 0
                                        ? 'text-red-600 ring-red-100'
                                        : 'text-emerald-600 ring-emerald-100'"
                                >
                                    <i class="fas fa-clock"></i>
                                </div>

                                <div class="flex-1">
                                    <p
                                        class="text-sm font-bold"
                                        :class="daysLate > 0 ? 'text-red-800' : 'text-emerald-800'"
                                    >
                                        <span x-text="hariTerlambatText"></span>
                                    </p>

                                    <p
                                        class="mt-1 text-sm leading-relaxed"
                                        :class="daysLate > 0 ? 'text-red-700' : 'text-emerald-700'"
                                    >
                                        Denda keterlambatan:
                                        <strong>Rp 10.000</strong> per hari.

                                        <span x-show="daysLate > 0">
                                            Total keterlambatan:
                                            <strong x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"></strong>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Kondisi Buku --}}
                        <div>
                            <label class="mb-3 block text-sm font-bold text-slate-800">
                                Kondisi Buku <span class="text-red-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                {{-- Baik --}}
                                <label class="relative cursor-pointer">
                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="baik"
                                        class="peer sr-only"
                                        required
                                        x-model="kondisi"
                                    >

                                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-emerald-200 hover:bg-emerald-50/40 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-sm">
                                        <div class="text-center">
                                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                                <i class="fas fa-check text-lg"></i>
                                            </div>

                                            <p class="font-bold text-slate-800">Baik</p>
                                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                                Hanya denda keterlambatan.
                                            </p>
                                        </div>
                                    </div>
                                </label>

                                {{-- Rusak --}}
                                <label class="relative cursor-pointer">
                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="rusak"
                                        class="peer sr-only"
                                        x-model="kondisi"
                                    >

                                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-amber-200 hover:bg-amber-50/40 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-sm">
                                        <div class="text-center">
                                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                                                <i class="fas fa-screwdriver-wrench text-lg"></i>
                                            </div>

                                            <p class="font-bold text-slate-800">Rusak</p>
                                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                                Tambahkan denda kerusakan.
                                            </p>
                                        </div>
                                    </div>
                                </label>

                                {{-- Hilang --}}
                                <label class="relative cursor-pointer">
                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="hilang"
                                        class="peer sr-only"
                                        x-model="kondisi"
                                    >

                                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-red-200 hover:bg-red-50/40 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-sm">
                                        <div class="text-center">
                                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-1 ring-red-100">
                                                <i class="fas fa-xmark text-lg"></i>
                                            </div>

                                            <p class="font-bold text-slate-800">Hilang</p>
                                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                                Tambahkan ganti rugi buku.
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Input Denda Rusak --}}
                        <div
                            x-show="kondisi === 'rusak'"
                            x-cloak
                            class="mt-5 rounded-2xl border border-amber-100 bg-amber-50 p-4"
                        >
                            <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-screwdriver-wrench text-xs text-amber-500"></i>
                                Nominal Denda Kerusakan
                            </label>

                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white px-3 py-3 text-sm font-bold text-slate-600 ring-1 ring-amber-100">
                                    Rp
                                </span>

                                <input
                                    type="text"
                                    x-model="dendaRusakFormatted"
                                    @input="
                                        dendaRusak = parseNumber($event.target.value);
                                        dendaRusakFormatted = formatRupiah(dendaRusak);
                                    "
                                    class="flex-1 rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-100"
                                    placeholder="Contoh: 10.000"
                                >
                            </div>

                            <input type="hidden" name="denda_rusak" :value="dendaRusak">
                        </div>

                        {{-- Input Denda Hilang --}}
                        <div
                            x-show="kondisi === 'hilang'"
                            x-cloak
                            class="mt-5 rounded-2xl border border-red-100 bg-red-50 p-4"
                        >
                            <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-circle-exclamation text-xs text-red-500"></i>
                                Nominal Ganti Rugi
                            </label>

                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white px-3 py-3 text-sm font-bold text-slate-600 ring-1 ring-red-100">
                                    Rp
                                </span>

                                <input
                                    type="text"
                                    x-model="dendaHilangFormatted"
                                    @input="
                                        dendaHilang = parseNumber($event.target.value);
                                        dendaHilangFormatted = formatRupiah(dendaHilang);
                                    "
                                    class="flex-1 rounded-2xl border border-red-100 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition focus:border-red-400 focus:outline-none focus:ring-4 focus:ring-red-100"
                                    placeholder="Contoh: 100.000"
                                >
                            </div>

                            <input type="hidden" name="denda_hilang" :value="dendaHilang">
                        </div>

                        <input type="hidden" name="denda" :value="totalDenda">

                        {{-- Actions --}}
                        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                            <a
                                href="{{ route('admin.pinjamkelas.kelas') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-100/80 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-200/70 hover:text-slate-800"
                            >
                                <i class="fas fa-arrow-left text-xs"></i>
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-red-100 transition hover:-translate-y-0.5 hover:bg-red-700"
                            >
                                <i class="fas fa-money-bill-wave text-xs"></i>
                                Proses Denda
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Ringkasan Denda --}}
        <div class="xl:col-span-1">
            <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden xl:sticky xl:top-6">
                <div class="p-5 border-b border-slate-100 bg-white/80">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-1 ring-red-100">
                            <i class="fas fa-receipt"></i>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                Ringkasan Denda
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Total otomatis berdasarkan keterlambatan dan kondisi buku.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-slate-500">Keterlambatan</span>
                            <span class="font-bold text-slate-800" x-text="'Rp ' + formatRupiah(dendaKeterlambatan)">
                                Rp 0
                            </span>
                        </div>

                        <p class="mt-1 text-xs text-slate-400" x-text="hariTerlambatText">
                            Tidak terlambat
                        </p>
                    </div>

                    <div
                        class="rounded-2xl bg-amber-50 p-4 ring-1 ring-amber-100"
                        x-show="kondisi === 'rusak'"
                        x-cloak
                    >
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-amber-700">Denda Kerusakan</span>
                            <span class="font-bold text-amber-700" x-text="'Rp ' + formatRupiah(dendaRusak)">
                                Rp 0
                            </span>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl bg-red-50 p-4 ring-1 ring-red-100"
                        x-show="kondisi === 'hilang'"
                        x-cloak
                    >
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-red-700">Ganti Rugi Hilang</span>
                            <span class="font-bold text-red-700" x-text="'Rp ' + formatRupiah(dendaHilang)">
                                Rp 0
                            </span>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-gradient-to-br from-red-600 to-rose-500 p-5 text-white shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-50">
                            Total Denda
                        </p>

                        <p class="mt-2 text-3xl font-extrabold tracking-tight" x-text="'Rp ' + formatRupiah(totalDenda)">
                            Rp 0
                        </p>

                        <p class="mt-2 text-xs text-red-50">
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