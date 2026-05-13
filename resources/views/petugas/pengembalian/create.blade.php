@extends('layouts.petugas')

@section('title', 'Pengembalian Buku')
@section('page-title', 'Pengembalian Buku')

@section('content')

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-times-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-2xl font-bold text-gray-900">Pengembalian Buku</h3>
    </div>

    <div class="max-w-4xl mx-auto">

        <!-- Search Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Cari Peminjaman</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    Cari berdasarkan nama, nomor identitas, atau kode buku
                </p>
            </div>

            <div class="p-6">

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('pengembalian.search') }}" method="POST">
                    @csrf

                    <div class="flex gap-3">

                        <div class="relative flex-1">
                            <i class="fas fa-search text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ old('search', isset($loan) ? $loan->user->name : '') }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                                placeholder="Nama, NIM/NISN, atau kode buku..."
                                minlength="2"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-full text-sm font-medium flex items-center gap-2 transition-colors"
                        >
                            <i class="fas fa-search"></i>
                            <span>Cari</span>
                        </button>

                    </div>

                </form>
            </div>
        </div>

        @if(isset($loans) && $loans->count() > 1)

        <!-- Multiple Results -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-5">

            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">
                    Pilih Peminjaman
                </h3>

                <p class="text-sm text-gray-500 mt-0.5">
                    Ditemukan {{ $loans->count() }} peminjaman aktif.
                </p>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="bg-cyan-500 text-white text-sm">
                            <th class="px-5 py-3 text-left font-semibold">Judul Buku</th>
                            <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                            <th class="px-5 py-3 text-center font-semibold">Tgl Pinjam</th>
                            <th class="px-5 py-3 text-center font-semibold">Tgl Kembali</th>
                            <th class="px-5 py-3 text-center font-semibold w-20">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @foreach($loans as $loanOption)

                        <tr class="hover:bg-gray-50">

                            <td class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $loanOption->bookItem->book->judul }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    {{ $loanOption->bookItem->kode_buku }}
                                </p>
                            </td>

                            <td class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $loanOption->user->name }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    {{ $loanOption->user->nomor_identitas }}
                                </p>
                            </td>

                            <td class="px-5 py-3 text-center text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($loanOption->tanggal_pinjam)->format('d M Y') }}
                            </td>

                            <td class="px-5 py-3 text-center">
                                @php
                                    $isLate = \Carbon\Carbon::parse($loanOption->tanggal_kembali)->isPast();
                                @endphp

                                <span class="text-sm {{ $isLate ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ \Carbon\Carbon::parse($loanOption->tanggal_kembali)->format('d M Y') }}
                                </span>
                            </td>

                            <td class="px-5 py-3 text-center">

                                <form action="{{ route('pengembalian.search') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="loan_id" value="{{ $loanOption->id }}">

                                    <button
                                        type="submit"
                                        class="px-3 py-1.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg text-xs font-medium transition-colors"
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

        <!-- Detail & Form Pengembalian -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">
                    Detail Peminjaman
                </h3>

                <p class="text-sm text-gray-600 mt-1">
                    Informasi peminjaman yang akan dikembalikan
                </p>
            </div>

            <div class="p-6">

                <!-- Info Peminjaman -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">

                    <div>

                        <div class="flex items-start gap-4">

                            @if($loan->bookItem->book->foto)

                                <img
                                    src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                    alt="{{ $loan->bookItem->book->judul }}"
                                    class="w-20 h-28 object-cover rounded shadow-sm border border-gray-200"
                                >

                            @else

                                <div class="w-20 h-28 bg-gray-200 rounded shadow-sm border border-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-3xl text-gray-400"></i>
                                </div>

                            @endif

                            <div class="flex-1">

                                <h4 class="font-semibold text-gray-900 mb-1">
                                    {{ $loan->bookItem->book->judul }}
                                </h4>

                                <p class="text-sm text-gray-600">
                                    Kode: {{ $loan->bookItem->kode_buku }}
                                </p>

                                <p class="text-sm text-gray-600">
                                    Kategori: {{ $loan->bookItem->book->category->nama_kategori }}
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="space-y-2">

                        <div>
                            <p class="text-sm text-gray-600">Peminjam</p>

                            <p class="font-semibold text-gray-900">
                                {{ $loan->user->name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ $loan->user->nomor_identitas }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Tanggal Pinjam</p>

                            <p class="font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Tanggal Harus Kembali</p>

                            <p class="font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Status</p>

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                Disetujui
                            </span>
                        </div>

                    </div>

                </div>

                <!-- FORM - INI YANG DIUBAH -->
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

                            kembali.setHours(0,0,0,0);
                            sekarang.setHours(0,0,0,0);

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

                    <!-- INFO DENDA KETERLAMBATAN -->
                    <div
                        class="mb-6 p-4 rounded-lg"
                        :class="daysLate > 0
                            ? 'bg-red-50 border-l-4 border-red-500'
                            : 'bg-green-50 border-l-4 border-green-500'"
                    >

                        <div class="flex items-start">

                            <i
                                class="fas fa-clock mt-0.5 mr-3"
                                :class="daysLate > 0 ? 'text-red-600' : 'text-green-600'"
                            ></i>

                            <div class="flex-1">

                                <p
                                    class="text-sm font-medium"
                                    :class="daysLate > 0 ? 'text-red-800' : 'text-green-800'"
                                >
                                    <span x-text="hariTerlambatText"></span>
                                </p>

                                <p
                                    class="text-sm mt-1"
                                    :class="daysLate > 0 ? 'text-red-700' : 'text-green-700'"
                                >
                                    Denda keterlambatan:
                                    <strong>Rp 10.000</strong> per hari

                                    <span x-show="daysLate > 0">
                                        ×
                                        <span x-text="daysLate"></span>
                                        hari =

                                        <strong
                                            x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"
                                        ></strong>
                                    </span>
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- KONDISI BUKU -->
                    <div class="space-y-4">

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Kondisi Buku
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                <!-- BAIK -->
                                <label class="relative cursor-pointer group">

                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="baik"
                                        class="peer sr-only"
                                        required
                                        x-model="kondisi"
                                    >

                                    <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-cyan-500 peer-checked:bg-cyan-50 border-gray-300 hover:border-cyan-300">

                                        <div class="text-center">

                                            <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-cyan-100 peer-checked:bg-cyan-500 text-cyan-600 peer-checked:text-white transition-colors">
                                                <i class="fas fa-check text-xl"></i>
                                            </div>

                                            <p class="font-semibold text-gray-700 peer-checked:text-cyan-600">
                                                Baik
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Tanpa denda
                                            </p>

                                        </div>

                                    </div>

                                </label>

                                <!-- RUSAK -->
                                <label class="relative cursor-pointer group">

                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="rusak"
                                        class="peer sr-only"
                                        x-model="kondisi"
                                    >

                                    <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 border-gray-300 hover:border-yellow-300">

                                        <div class="text-center">

                                            <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-yellow-100 peer-checked:bg-yellow-500 text-yellow-600 peer-checked:text-white transition-colors">
                                                <i class="fas fa-tools text-xl"></i>
                                            </div>

                                            <p class="font-semibold text-gray-700 peer-checked:text-yellow-600">
                                                Rusak
                                            </p>

                                            <p class="text-xs text-yellow-600 mt-1">
                                                Denda manual (default Rp 10.000)
                                            </p>

                                        </div>

                                    </div>

                                </label>

                                <!-- HILANG -->
                                <label class="relative cursor-pointer group">

                                    <input
                                        type="radio"
                                        name="kondisi"
                                        value="hilang"
                                        class="peer sr-only"
                                        x-model="kondisi"
                                    >

                                    <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-red-500 peer-checked:bg-red-50 border-gray-300 hover:border-red-300">

                                        <div class="text-center">

                                            <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-red-100 peer-checked:bg-red-500 text-red-600 peer-checked:text-white transition-colors">
                                                <i class="fas fa-times text-xl"></i>
                                            </div>

                                            <p class="font-semibold text-gray-700 peer-checked:text-red-600">
                                                Hilang
                                            </p>

                                            <p class="text-xs text-red-600 mt-1">
                                                Ganti rugi (default Rp 100.000)
                                            </p>

                                        </div>

                                    </div>

                                </label>

                            </div>

                        </div>

                        <!-- INPUT DENDA RUSAK -->
                        <div
                            x-show="kondisi === 'rusak'"
                            x-cloak
                            class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200"
                        >

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nominal Denda Kerusakan
                            </label>

                            <div class="flex items-center gap-3">

                                <span class="text-gray-600 font-medium">
                                    Rp
                                </span>

                                <input
                                    type="text"
                                    x-model="dendaRusakFormatted"
                                    @input="
                                        dendaRusak = parseNumber($event.target.value);
                                        dendaRusakFormatted = formatRupiah(dendaRusak);
                                    "
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100"
                                    placeholder="Contoh: 10.000"
                                >

                            </div>

                            <div class="mt-2 flex items-center gap-2 text-xs text-yellow-700">
                                <i class="fas fa-info-circle"></i>
                                <span>Default: Rp 10.000 (coretan ringan) | Rp 50.000 (sobek) | Rp 100.000 (rusak parah)</span>
                            </div>

                            <input
                                type="hidden"
                                name="denda_rusak"
                                :value="dendaRusak"
                            >

                        </div>

                        <!-- INPUT DENDA HILANG -->
                        <div
                            x-show="kondisi === 'hilang'"
                            x-cloak
                            class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200"
                        >

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nominal Ganti Rugi
                            </label>

                            <div class="flex items-center gap-3">

                                <span class="text-gray-600 font-medium">
                                    Rp
                                </span>

                                <input
                                    type="text"
                                    x-model="dendaHilangFormatted"
                                    @input="
                                        dendaHilang = parseNumber($event.target.value);
                                        dendaHilangFormatted = formatRupiah(dendaHilang);
                                    "
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100"
                                    placeholder="Contoh: 100.000"
                                >

                            </div>

                            <div class="mt-2 flex items-center gap-2 text-xs text-red-700">
                                <i class="fas fa-info-circle"></i>
                                <span>Default: Rp 100.000 (harga buku standar)</span>
                            </div>

                            <input
                                type="hidden"
                                name="denda_hilang"
                                :value="dendaHilang"
                            >

                        </div>

                        <!-- RINGKASAN TOTAL DENDA -->
                        <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-200">

                            <h4 class="font-semibold text-gray-800 mb-3">
                                Ringkasan Denda
                            </h4>

                            <div class="space-y-2">

                                <div class="flex justify-between text-sm">

                                    <span class="text-gray-600">
                                        Denda Keterlambatan
                                    </span>

                                    <span
                                        class="font-medium"
                                        x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"
                                    ></span>

                                </div>

                                <div
                                    class="flex justify-between text-sm"
                                    x-show="kondisi === 'rusak' && dendaRusak > 0"
                                >

                                    <span class="text-gray-600">
                                        Denda Kerusakan
                                    </span>

                                    <span
                                        class="font-medium text-yellow-700"
                                        x-text="'Rp ' + formatRupiah(dendaRusak)"
                                    ></span>

                                </div>

                                <div
                                    class="flex justify-between text-sm"
                                    x-show="kondisi === 'hilang' && dendaHilang > 0"
                                >

                                    <span class="text-gray-600">
                                        Ganti Rugi Hilang
                                    </span>

                                    <span
                                        class="font-medium text-red-700"
                                        x-text="'Rp ' + formatRupiah(dendaHilang)"
                                    ></span>

                                </div>

                                <div class="border-t border-gray-300 pt-2 mt-2">

                                    <div class="flex justify-between font-bold text-gray-900">

                                        <span>
                                            TOTAL DENDA
                                        </span>

                                        <span
                                            class="text-red-600 text-lg"
                                            x-text="'Rp ' + formatRupiah(totalDenda)"
                                        ></span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- HIDDEN INPUTS -->
                        <input
                            type="hidden"
                            name="denda"
                            :value="totalDenda"
                        >

                        <input
                            type="hidden"
                            name="days_late"
                            :value="daysLate"
                        >

                    </div>

                    <!-- BUTTON ACTION -->
                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">

                        <a
                            href="{{ route('peminjaman.riwayat') }}"
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors flex items-center gap-2"
                        >

                            <i class="fas fa-check"></i>

                            <span>
                                Proses Pengembalian
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

        @endif

    </div>

    <style>
        [x-cloak]{
            display:none !important;
        }
    </style>

@endsection