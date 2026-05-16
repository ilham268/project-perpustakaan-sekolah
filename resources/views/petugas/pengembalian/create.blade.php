@extends('layouts.petugas')

@section('title', 'Pengembalian Buku')
@section('page-title', 'Pengembalian Buku')

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
    <div class="flex flex-col gap-1">
        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Pengembalian Buku
        </h3>

        <p class="text-sm text-slate-500">
            Cari data peminjaman aktif lalu proses pengembalian buku.
        </p>
    </div>

    <div class="mx-auto max-w-5xl space-y-5">

        {{-- Search Card --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 md:px-6">
                <h3 class="text-base font-bold text-slate-900">
                    Cari Peminjaman
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Cari berdasarkan nama, nomor identitas, atau kode buku.
                </p>
            </div>

            <div class="p-5 md:p-6">

                @if($errors->any())
                    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('pengembalian.search') }}" method="POST">
                    @csrf

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ old('search', isset($loan) ? $loan->user->name : '') }}"
                                placeholder="Nama, NIM/NISN, atau kode buku..."
                                minlength="2"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                            >
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
                        >
                            <i class="fas fa-search text-xs"></i>
                            <span>Cari</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        @if(isset($loans) && $loans->count() > 1)

            {{-- Multiple Results --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4 md:px-6">
                    <h3 class="text-base font-bold text-slate-900">
                        Pilih Peminjaman
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Ditemukan {{ $loans->count() }} peminjaman aktif.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
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
                                    Tgl Pinjam
                                </th>
                                <th class="border border-slate-200 px-5 py-4 text-center">
                                    Tgl Kembali
                                </th>
                                <th class="w-24 border border-slate-200 px-5 py-4 text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($loans as $loanOption)
                                <tr class="transition-colors hover:bg-slate-50">

                                    <td class="border border-slate-200 px-5 py-4">
                                        <span class="font-semibold text-slate-800">
                                            {{ $loanOption->bookItem->book->judul }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                        {{ $loanOption->bookItem->kode_buku }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4">
                                        <span class="font-semibold text-slate-800">
                                            {{ $loanOption->user->name }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                        {{ $loanOption->user->nomor_identitas }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                        {{ \Carbon\Carbon::parse($loanOption->tanggal_pinjam)->format('d M Y') }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        @php
                                            $isLate = \Carbon\Carbon::parse($loanOption->tanggal_kembali)->isPast();
                                        @endphp

                                        <span class="text-sm font-medium {{ $isLate ? 'text-red-600' : 'text-slate-500' }}">
                                            {{ \Carbon\Carbon::parse($loanOption->tanggal_kembali)->format('d M Y') }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        <form action="{{ route('pengembalian.search') }}" method="POST">
                                            @csrf

                                            <input type="hidden" name="loan_id" value="{{ $loanOption->id }}">

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100 transition hover:bg-emerald-100"
                                            >
                                                Pilih
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

        @if(isset($loan))

            {{-- Detail & Form Pengembalian --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-5 py-4 md:px-6">
                    <h3 class="text-base font-bold text-slate-900 md:text-lg">
                        Detail Peminjaman
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Informasi peminjaman yang akan dikembalikan.
                    </p>
                </div>

                <div class="p-5 md:p-6">

                    {{-- Info Peminjaman --}}
                    <div class="mb-6 grid grid-cols-1 gap-5 rounded-2xl border border-slate-200 bg-slate-50/80 p-4 md:grid-cols-2">

                        {{-- Buku --}}
                        <div class="flex items-start gap-4">
                            @if($loan->bookItem->book->foto)
                                <img
                                    src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                    alt="{{ $loan->bookItem->book->judul }}"
                                    class="h-28 w-20 shrink-0 rounded-xl border border-slate-200 object-cover shadow-sm"
                                >
                            @else
                                <div class="flex h-28 w-20 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-slate-400 shadow-sm">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <h4 class="text-base font-bold text-slate-900">
                                    {{ $loan->bookItem->book->judul }}
                                </h4>

                                <div class="mt-3 space-y-1 text-sm">
                                    <p class="text-slate-500">
                                        Kode:
                                        <span class="font-semibold text-slate-700">
                                            {{ $loan->bookItem->kode_buku }}
                                        </span>
                                    </p>

                                    <p class="text-slate-500">
                                        Kategori:
                                        <span class="font-semibold text-slate-700">
                                            {{ $loan->bookItem->book->category->nama_kategori }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Peminjam --}}
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-1">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Peminjam
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $loan->user->name }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    {{ $loan->user->nomor_identitas }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Pinjam
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Harus Kembali
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Status
                                </p>

                                <span class="mt-1 inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                                    Disetujui
                                </span>
                            </div>
                        </div>

                    </div>

                    {{-- Form Pengembalian --}}
                    <form
                        action="{{ url('/pengembalian') }}"
                        method="POST"
                        x-data="{
                            kondisi: 'baik',

                            dendaRusak: 10000,
                            dendaRusakFormatted: '10.000',

                            dendaHilang: 100000,
                            dendaHilangFormatted: '100.000',

                            tanggalKembali: '{{ $loan->tanggal_kembali }}',

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
                                const kembali = new Date(this.tanggalKembali);
                                const sekarang = new Date();

                                kembali.setHours(0, 0, 0, 0);
                                sekarang.setHours(0, 0, 0, 0);

                                const diff = Math.floor(
                                    (sekarang - kembali) / (1000 * 60 * 60 * 24)
                                );

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
                        @csrf

                        <input type="hidden" name="loan_id" value="{{ $loan->id }}">

                        {{-- Info Denda Keterlambatan --}}
                        <div
                            class="mb-6 rounded-2xl border px-4 py-4"
                            :class="daysLate > 0
                                ? 'border-red-200 bg-red-50'
                                : 'border-green-200 bg-green-50'"
                        >
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                    :class="daysLate > 0
                                        ? 'bg-red-100 text-red-600'
                                        : 'bg-green-100 text-green-600'"
                                >
                                    <i class="fas fa-clock"></i>
                                </div>

                                <div class="flex-1">
                                    <p
                                        class="text-sm font-bold"
                                        :class="daysLate > 0 ? 'text-red-800' : 'text-green-800'"
                                    >
                                        <span x-text="hariTerlambatText"></span>
                                    </p>

                                    <p
                                        class="mt-1 text-sm"
                                        :class="daysLate > 0 ? 'text-red-700' : 'text-green-700'"
                                    >
                                        Denda keterlambatan:
                                        <strong>Rp 10.000</strong> per hari

                                        <span x-show="daysLate > 0">
                                            × <span x-text="daysLate"></span> hari =
                                            <strong x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"></strong>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Kondisi Buku --}}
                        <div class="space-y-4">
                            <div>
                                <label class="mb-3 block text-sm font-bold text-slate-700">
                                    Kondisi Buku
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

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

                                        <div class="rounded-2xl border-2 border-slate-200 bg-white p-4 transition hover:border-emerald-200 hover:bg-emerald-50 peer-checked:border-emerald-400 peer-checked:bg-emerald-50">
                                            <div class="text-center">
                                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                                    <i class="fas fa-check text-xl"></i>
                                                </div>

                                                <p class="font-bold text-slate-700">
                                                    Baik
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    Tanpa denda
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

                                        <div class="rounded-2xl border-2 border-slate-200 bg-white p-4 transition hover:border-yellow-200 hover:bg-yellow-50 peer-checked:border-yellow-400 peer-checked:bg-yellow-50">
                                            <div class="text-center">
                                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-50 text-yellow-600 ring-1 ring-yellow-100">
                                                    <i class="fas fa-tools text-xl"></i>
                                                </div>

                                                <p class="font-bold text-slate-700">
                                                    Rusak
                                                </p>

                                                <p class="mt-1 text-xs text-yellow-700">
                                                    Denda manual
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

                                        <div class="rounded-2xl border-2 border-slate-200 bg-white p-4 transition hover:border-red-200 hover:bg-red-50 peer-checked:border-red-400 peer-checked:bg-red-50">
                                            <div class="text-center">
                                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-1 ring-red-100">
                                                    <i class="fas fa-times text-xl"></i>
                                                </div>

                                                <p class="font-bold text-slate-700">
                                                    Hilang
                                                </p>

                                                <p class="mt-1 text-xs text-red-700">
                                                    Ganti rugi
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
                                class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4"
                            >
                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Nominal Denda Kerusakan
                                </label>

                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-slate-600">
                                        Rp
                                    </span>

                                    <input
                                        type="text"
                                        x-model="dendaRusakFormatted"
                                        @input="
                                            dendaRusak = parseNumber($event.target.value);
                                            dendaRusakFormatted = formatRupiah(dendaRusak);
                                        "
                                        placeholder="Contoh: 10.000"
                                        class="flex-1 rounded-xl border border-yellow-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-yellow-400 focus:ring-4 focus:ring-yellow-100"
                                    >
                                </div>

                                <div class="mt-2 flex items-center gap-2 text-xs text-yellow-700">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Default: Rp 10.000 (coretan ringan) | Rp 50.000 (sobek) | Rp 100.000 (rusak parah)</span>
                                </div>

                                <input type="hidden" name="denda_rusak" :value="dendaRusak">
                            </div>

                            {{-- Input Denda Hilang --}}
                            <div
                                x-show="kondisi === 'hilang'"
                                x-cloak
                                class="rounded-2xl border border-red-200 bg-red-50 p-4"
                            >
                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Nominal Ganti Rugi
                                </label>

                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-slate-600">
                                        Rp
                                    </span>

                                    <input
                                        type="text"
                                        x-model="dendaHilangFormatted"
                                        @input="
                                            dendaHilang = parseNumber($event.target.value);
                                            dendaHilangFormatted = formatRupiah(dendaHilang);
                                        "
                                        placeholder="Contoh: 100.000"
                                        class="flex-1 rounded-xl border border-red-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100"
                                    >
                                </div>

                                <div class="mt-2 flex items-center gap-2 text-xs text-red-700">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Default: Rp 100.000 (harga buku standar)</span>
                                </div>

                                <input type="hidden" name="denda_hilang" :value="dendaHilang">
                            </div>

                            {{-- Ringkasan Denda --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <h4 class="mb-3 text-sm font-bold text-slate-800">
                                    Ringkasan Denda
                                </h4>

                                <div class="space-y-2">
                                    <div class="flex justify-between gap-4 text-sm">
                                        <span class="text-slate-500">
                                            Denda Keterlambatan
                                        </span>

                                        <span
                                            class="font-semibold text-slate-700"
                                            x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"
                                        ></span>
                                    </div>

                                    <div
                                        class="flex justify-between gap-4 text-sm"
                                        x-show="kondisi === 'rusak' && dendaRusak > 0"
                                    >
                                        <span class="text-slate-500">
                                            Denda Kerusakan
                                        </span>

                                        <span
                                            class="font-semibold text-yellow-700"
                                            x-text="'Rp ' + formatRupiah(dendaRusak)"
                                        ></span>
                                    </div>

                                    <div
                                        class="flex justify-between gap-4 text-sm"
                                        x-show="kondisi === 'hilang' && dendaHilang > 0"
                                    >
                                        <span class="text-slate-500">
                                            Ganti Rugi Hilang
                                        </span>

                                        <span
                                            class="font-semibold text-red-700"
                                            x-text="'Rp ' + formatRupiah(dendaHilang)"
                                        ></span>
                                    </div>

                                    <div class="mt-3 border-t border-slate-200 pt-3">
                                        <div class="flex justify-between gap-4">
                                            <span class="text-sm font-extrabold text-slate-900">
                                                TOTAL DENDA
                                            </span>

                                            <span
                                                class="text-lg font-extrabold text-red-600"
                                                x-text="'Rp ' + formatRupiah(totalDenda)"
                                            ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden Inputs --}}
                            <input type="hidden" name="denda" :value="totalDenda">
                            <input type="hidden" name="days_late" :value="daysLate">
                        </div>

                        {{-- Button Action --}}
                        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                            <a
                                href="{{ route('peminjaman.riwayat') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
                            >
                                <i class="fas fa-check text-xs"></i>
                                <span>Proses Pengembalian</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        @endif

    </div>

</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

@endsection