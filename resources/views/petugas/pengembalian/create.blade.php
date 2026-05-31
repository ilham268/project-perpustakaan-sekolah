@extends('layouts.petugas')

@section('title', 'Pengembalian Buku')
@section('page-title', 'Pengembalian Buku')

@section('content')

@php
    try {
        $settingDenda = null;

        if (\Illuminate\Support\Facades\Schema::hasTable('library_settings')) {
            $settingDenda = \Illuminate\Support\Facades\DB::table('library_settings')
                ->where('key', 'denda_telat_per_hari')
                ->value('value');
        }

        $dendaTelatPerHariValue = is_numeric($settingDenda) ? (int) $settingDenda : 10000;

        if ($dendaTelatPerHariValue < 0) {
            $dendaTelatPerHariValue = 10000;
        }
    } catch (\Throwable $e) {
        $dendaTelatPerHariValue = 10000;
    }
@endphp

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

        <div class="relative">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                Data Pengembalian
            </p>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                Pengembalian Buku
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                Pilih peminjaman aktif lalu proses pengembalian buku sesuai kondisi dan keterlambatan.
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-6xl space-y-6">

        {{-- Search --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
                <h2 class="text-lg font-extrabold text-slate-900">
                    Cari Peminjaman
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Cari berdasarkan nama, nomor identitas, judul buku, atau kode buku.
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

                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                        <div class="relative lg:col-span-8">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ old('search', isset($loan) ? $loan->user->name : '') }}"
                                placeholder="Nama, NIM/NISN, judul, atau kode buku..."
                                minlength="2"
                                required
                                class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                        </div>

                        <div class="lg:col-span-2">
                            <button
                                type="submit"
                                class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                                Cari
                            </button>
                        </div>

                        <div class="lg:col-span-2">
                            <a
                                href="{{ route('pengembalian.index') }}"
                                class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Daftar Peminjaman Aktif --}}
        @if(isset($loans) && $loans->count() > 0 && !isset($loan))
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-5 py-4 md:px-6">
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Daftar Peminjaman Aktif
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Ada {{ $loans->count() }} buku yang sedang dipinjam dan siap diproses pengembaliannya.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1080px] border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                                <th class="w-14 border border-slate-200 px-5 py-4 text-left">
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
                                    Tgl Pinjam
                                </th>

                                <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                                    Tgl Kembali
                                </th>

                                <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            @foreach($loans as $loanOption)
                                @php
                                    $tanggalKembaliOption = $loanOption->tanggal_kembali
                                        ? \Carbon\Carbon::parse($loanOption->tanggal_kembali)->startOfDay()
                                        : null;

                                    $isLate = $tanggalKembaliOption
                                        ? $tanggalKembaliOption->lt(now()->startOfDay())
                                        : false;
                                @endphp

                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4">
                                        <span class="block max-w-[280px] truncate font-semibold text-slate-800">
                                            {{ $loanOption->bookItem->book->judul ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                        {{ $loanOption->bookItem->kode_buku ?? '-' }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4">
                                        <span class="block max-w-[220px] truncate font-semibold text-slate-800">
                                            {{ $loanOption->user->name ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                        {{ $loanOption->user->nomor_identitas ?? '-' }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                        {{ $loanOption->tanggal_pinjam ? \Carbon\Carbon::parse($loanOption->tanggal_pinjam)->format('d M Y') : '-' }}
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        <span class="{{ $isLate ? 'font-semibold text-red-600' : 'text-slate-500' }}">
                                            {{ $loanOption->tanggal_kembali ? \Carbon\Carbon::parse($loanOption->tanggal_kembali)->format('d M Y') : '-' }}
                                        </span>

                                        @if($isLate)
                                            <p class="mt-1 text-xs font-semibold text-red-500">
                                                Terlambat
                                            </p>
                                        @endif
                                    </td>

                                    <td class="border border-slate-200 px-5 py-4 text-center">
                                        <form action="{{ route('pengembalian.search') }}" method="POST">
                                            @csrf

                                            <input type="hidden" name="loan_id" value="{{ $loanOption->id }}">

                                            <button
                                                type="submit"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
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

        @if(isset($loans) && $loans->count() === 0 && !isset($loan))
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="px-6 py-12 text-center">
                    <p class="text-sm font-bold text-slate-700">
                        Tidak ada peminjaman aktif
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Belum ada buku dengan status disetujui yang perlu dikembalikan.
                    </p>
                </div>
            </div>
        @endif

        @if(isset($loan))
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-5 py-4 md:px-6">
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Detail Peminjaman
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi peminjaman yang akan dikembalikan.
                    </p>
                </div>

                <div class="p-5 md:p-6">
                    <div class="mb-6 grid grid-cols-1 gap-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2">
                        <div class="flex items-start gap-4">
                            @if($loan->bookItem && $loan->bookItem->book && $loan->bookItem->book->foto)
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
                                    {{ $loan->bookItem->book->judul ?? '-' }}
                                </h4>

                                <p class="mt-3 text-sm text-slate-500">
                                    Kode:
                                    <span class="font-semibold text-slate-700">
                                        {{ $loan->bookItem->kode_buku ?? '-' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-1">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Peminjam
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $loan->user->name ?? '-' }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    {{ $loan->user->nomor_identitas ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Pinjam
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $loan->tanggal_pinjam ? \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') : '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Harus Kembali
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $loan->tanggal_kembali ? \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') : '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Status
                                </p>

                                <p class="mt-1 text-sm font-bold text-emerald-700">
                                    Disetujui
                                </p>
                            </div>
                        </div>
                    </div>

                    <form
                        action="{{ route('pengembalian.store') }}"
                        method="POST"
                        x-data="{
                            kondisi: 'baik',
                            dendaPerHari: {{ $dendaTelatPerHariValue }},
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

                                const diff = Math.floor((sekarang - kembali) / (1000 * 60 * 60 * 24));

                                return diff > 0 ? diff : 0;
                            },

                            get dendaKeterlambatan() {
                                return this.daysLate * this.dendaPerHari;
                            },

                            get dendaKondisi() {
                                if (this.kondisi === 'rusak') return this.dendaRusak || 0;
                                if (this.kondisi === 'hilang') return this.dendaHilang || 0;
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

                        <div
                            class="mb-6 rounded-2xl border px-4 py-4"
                            :class="daysLate > 0 ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50'"
                        >
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
                                <strong x-text="'Rp ' + formatRupiah(dendaPerHari)"></strong>
                                per hari

                                <span x-show="daysLate > 0">
                                    × <span x-text="daysLate"></span> hari =
                                    <strong x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"></strong>
                                </span>
                            </p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-3 block text-sm font-bold text-slate-700">
                                    Kondisi Buku <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="kondisi" value="baik" class="peer sr-only" required x-model="kondisi">

                                        <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-emerald-300 hover:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50">
                                            <p class="font-bold text-slate-700">
                                                Baik
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Tanpa denda
                                            </p>
                                        </div>
                                    </label>

                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="kondisi" value="rusak" class="peer sr-only" x-model="kondisi">

                                        <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-yellow-300 hover:bg-yellow-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                            <p class="font-bold text-slate-700">
                                                Rusak
                                            </p>

                                            <p class="mt-1 text-xs text-yellow-700">
                                                Denda manual
                                            </p>
                                        </div>
                                    </label>

                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="kondisi" value="hilang" class="peer sr-only" x-model="kondisi">

                                        <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-red-300 hover:bg-red-50 peer-checked:border-red-500 peer-checked:bg-red-50">
                                            <p class="font-bold text-slate-700">
                                                Hilang
                                            </p>

                                            <p class="mt-1 text-xs text-red-700">
                                                Ganti rugi
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div x-show="kondisi === 'rusak'" x-cloak class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4">
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
                                        class="h-11 flex-1 rounded-lg border border-yellow-200 bg-white px-3 text-sm font-medium text-slate-700 transition focus:border-yellow-400 focus:outline-none focus:ring-4 focus:ring-yellow-100"
                                    >
                                </div>

                                <input type="hidden" name="denda_rusak" :value="dendaRusak">
                            </div>

                            <div x-show="kondisi === 'hilang'" x-cloak class="rounded-2xl border border-red-200 bg-red-50 p-4">
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
                                        class="h-11 flex-1 rounded-lg border border-red-200 bg-white px-3 text-sm font-medium text-slate-700 transition focus:border-red-400 focus:outline-none focus:ring-4 focus:ring-red-100"
                                    >
                                </div>

                                <input type="hidden" name="denda_hilang" :value="dendaHilang">
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <h4 class="mb-3 text-sm font-bold text-slate-800">
                                    Ringkasan Denda
                                </h4>

                                <div class="space-y-2">
                                    <div class="flex justify-between gap-4 text-sm">
                                        <span class="text-slate-500">
                                            Denda Keterlambatan
                                        </span>

                                        <span class="font-semibold text-slate-700" x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"></span>
                                    </div>

                                    <div class="flex justify-between gap-4 text-sm" x-show="kondisi === 'rusak' && dendaRusak > 0">
                                        <span class="text-slate-500">
                                            Denda Kerusakan
                                        </span>

                                        <span class="font-semibold text-yellow-700" x-text="'Rp ' + formatRupiah(dendaRusak)"></span>
                                    </div>

                                    <div class="flex justify-between gap-4 text-sm" x-show="kondisi === 'hilang' && dendaHilang > 0">
                                        <span class="text-slate-500">
                                            Ganti Rugi Hilang
                                        </span>

                                        <span class="font-semibold text-red-700" x-text="'Rp ' + formatRupiah(dendaHilang)"></span>
                                    </div>

                                    <div class="mt-3 border-t border-slate-200 pt-3">
                                        <div class="flex justify-between gap-4">
                                            <span class="text-sm font-extrabold text-slate-900">
                                                Total Denda
                                            </span>

                                            <span class="text-lg font-extrabold text-red-600" x-text="'Rp ' + formatRupiah(totalDenda)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="denda" :value="totalDenda">
                            <input type="hidden" name="days_late" :value="daysLate">
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                            <a
                                href="{{ route('pengembalian.index') }}"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                                Proses Pengembalian
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