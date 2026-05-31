@extends('layouts.admin')

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

    <div class="flex items-center justify-between mb-5">
        <h3 class="text-2xl font-bold text-slate-900">Pengembalian Buku</h3>

        <a href="{{ route('admin.peminjaman.riwayat') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Cari Peminjaman</h3>
                <p class="text-sm text-slate-500 mt-0.5">
                    Cari berdasarkan nama, nomor identitas, judul buku, atau kode buku.
                </p>
            </div>

            <div class="p-6">
                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.pengembalian.search') }}" method="POST">
                    @csrf

                    <div class="flex flex-col gap-3 md:flex-row">
                        <div class="relative flex-1">
                            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ old('search', isset($loan) ? $loan->user->name : '') }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-full text-sm focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                                placeholder="Nama, NIM/NISN, judul, atau kode buku..."
                                minlength="2"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full text-sm font-medium flex items-center justify-center gap-2 transition-colors"
                        >
                            <i class="fas fa-search"></i>
                            <span>Cari</span>
                        </button>

                        <a
                            href="{{ route('admin.pengembalian.index') }}"
                            class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-full text-sm font-medium flex items-center justify-center gap-2 transition-colors"
                        >
                            <i class="fas fa-rotate-left"></i>
                            <span>Reset</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($loans) && $loans->count() > 0 && !isset($loan))
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Daftar Peminjaman Aktif</h3>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Ada {{ $loans->count() }} buku yang sedang dipinjam dan siap diproses pengembaliannya.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px]">
                        <thead>
                            <tr class="bg-emerald-600 text-white text-sm">
                                <th class="px-5 py-3 text-left font-semibold w-14">No</th>
                                <th class="px-5 py-3 text-left font-semibold">Judul Buku</th>
                                <th class="px-5 py-3 text-left font-semibold">Kode Buku</th>
                                <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                                <th class="px-5 py-3 text-left font-semibold">Nomor Identitas</th>
                                <th class="px-5 py-3 text-center font-semibold">Tgl Pinjam</th>
                                <th class="px-5 py-3 text-center font-semibold">Tgl Kembali</th>
                                <th class="px-5 py-3 text-center font-semibold w-24">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach($loans as $loanOption)
                                @php
                                    $tanggalKembaliOption = $loanOption->tanggal_kembali
                                        ? \Carbon\Carbon::parse($loanOption->tanggal_kembali)->startOfDay()
                                        : null;

                                    $isLate = $tanggalKembaliOption
                                        ? $tanggalKembaliOption->lt(now()->startOfDay())
                                        : false;
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4 text-sm text-slate-500">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $loanOption->bookItem->book->judul ?? '-' }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            Kategori: {{ $loanOption->bookItem->book->category->nama_kategori ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4 text-sm text-slate-600">
                                        {{ $loanOption->bookItem->kode_buku ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $loanOption->user->name ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4 text-sm text-slate-600">
                                        {{ $loanOption->user->nomor_identitas ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4 text-center text-sm text-slate-600">
                                        {{ $loanOption->tanggal_pinjam ? \Carbon\Carbon::parse($loanOption->tanggal_pinjam)->format('d M Y') : '-' }}
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm {{ $isLate ? 'text-red-600 font-bold' : 'text-slate-600' }}">
                                            {{ $loanOption->tanggal_kembali ? \Carbon\Carbon::parse($loanOption->tanggal_kembali)->format('d M Y') : '-' }}
                                        </span>

                                        @if($isLate)
                                            <p class="mt-1 text-xs font-semibold text-red-500">
                                                Terlambat
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <form action="{{ route('admin.pengembalian.search') }}" method="POST">
                                            @csrf

                                            <input type="hidden" name="loan_id" value="{{ $loanOption->id }}">

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-colors"
                                            >
                                                <i class="fas fa-check"></i>
                                                <span>Pilih</span>
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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-5">
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                        <i class="fas fa-book-open text-2xl"></i>
                    </div>

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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">Detail Peminjaman</h3>
                    <p class="text-sm text-slate-600 mt-1">
                        Informasi peminjaman yang akan dikembalikan
                    </p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-slate-50 rounded-lg">
                        <div>
                            <div class="flex items-start gap-4">
                                @if($loan->bookItem && $loan->bookItem->book && $loan->bookItem->book->foto)
                                    <img
                                        src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                        alt="{{ $loan->bookItem->book->judul }}"
                                        class="w-20 h-28 object-cover rounded shadow-sm border border-slate-200"
                                    >
                                @else
                                    <div class="w-20 h-28 bg-slate-200 rounded shadow-sm border border-slate-200 flex items-center justify-center">
                                        <i class="fas fa-image text-3xl text-slate-400"></i>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <h4 class="font-semibold text-slate-900 mb-1">
                                        {{ $loan->bookItem->book->judul ?? '-' }}
                                    </h4>

                                    <p class="text-sm text-slate-600">
                                        Kode: {{ $loan->bookItem->kode_buku ?? '-' }}
                                    </p>

                                    <p class="text-sm text-slate-600">
                                        Kategori: {{ $loan->bookItem->book->category->nama_kategori ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div>
                                <p class="text-sm text-slate-600">Peminjam</p>
                                <p class="font-semibold text-slate-900">
                                    {{ $loan->user->name ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $loan->user->nomor_identitas ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-slate-600">Tanggal Pinjam</p>
                                <p class="font-semibold text-slate-900">
                                    {{ $loan->tanggal_pinjam ? \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') : '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-slate-600">Tanggal Harus Kembali</p>
                                <p class="font-semibold text-slate-900">
                                    {{ $loan->tanggal_kembali ? \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') : '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-slate-600">Status</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    Disetujui
                                </span>
                            </div>
                        </div>
                    </div>

                    <form
                        action="{{ route('admin.pengembalian.store') }}"
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

                                kembali.setHours(0,0,0,0);
                                sekarang.setHours(0,0,0,0);

                                const diff = Math.floor((sekarang - kembali) / (1000 * 60 * 60 * 24));
                                return diff > 0 ? diff : 0;
                            },

                            get dendaKeterlambatan() {
                                return this.daysLate * this.dendaPerHari;
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
                                        <strong x-text="'Rp ' + formatRupiah(dendaPerHari)"></strong>
                                        per hari

                                        <span x-show="daysLate > 0">
                                            ×
                                            <span x-text="daysLate"></span>
                                            hari =
                                            <strong x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"></strong>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-3">
                                    Kondisi Buku
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="relative cursor-pointer group">
                                        <input
                                            type="radio"
                                            name="kondisi"
                                            value="baik"
                                            class="peer sr-only"
                                            required
                                            x-model="kondisi"
                                        >

                                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 border-slate-300 hover:border-emerald-300">
                                            <div class="text-center">
                                                <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-emerald-100 text-emerald-600 transition-colors">
                                                    <i class="fas fa-check text-xl"></i>
                                                </div>

                                                <p class="font-semibold text-slate-700">Baik</p>
                                                <p class="text-xs text-slate-500 mt-1">Tanpa denda</p>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="relative cursor-pointer group">
                                        <input
                                            type="radio"
                                            name="kondisi"
                                            value="rusak"
                                            class="peer sr-only"
                                            x-model="kondisi"
                                        >

                                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 border-slate-300 hover:border-yellow-300">
                                            <div class="text-center">
                                                <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-yellow-100 text-yellow-600 transition-colors">
                                                    <i class="fas fa-tools text-xl"></i>
                                                </div>

                                                <p class="font-semibold text-slate-700">Rusak</p>
                                                <p class="text-xs text-yellow-600 mt-1">
                                                    Denda manual
                                                </p>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="relative cursor-pointer group">
                                        <input
                                            type="radio"
                                            name="kondisi"
                                            value="hilang"
                                            class="peer sr-only"
                                            x-model="kondisi"
                                        >

                                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-red-500 peer-checked:bg-red-50 border-slate-300 hover:border-red-300">
                                            <div class="text-center">
                                                <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-red-100 text-red-600 transition-colors">
                                                    <i class="fas fa-times text-xl"></i>
                                                </div>

                                                <p class="font-semibold text-slate-700">Hilang</p>
                                                <p class="text-xs text-red-600 mt-1">
                                                    Ganti rugi
                                                </p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div
                                x-show="kondisi === 'rusak'"
                                x-cloak
                                class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200"
                            >
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Nominal Denda Kerusakan
                                </label>

                                <div class="flex items-center gap-3">
                                    <span class="text-slate-600 font-medium">Rp</span>

                                    <input
                                        type="text"
                                        x-model="dendaRusakFormatted"
                                        @input="
                                            dendaRusak = parseNumber($event.target.value);
                                            dendaRusakFormatted = formatRupiah(dendaRusak);
                                        "
                                        class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100"
                                        placeholder="Contoh: 10.000"
                                    >
                                </div>

                                <input type="hidden" name="denda_rusak" :value="dendaRusak">
                            </div>

                            <div
                                x-show="kondisi === 'hilang'"
                                x-cloak
                                class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200"
                            >
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Nominal Ganti Rugi
                                </label>

                                <div class="flex items-center gap-3">
                                    <span class="text-slate-600 font-medium">Rp</span>

                                    <input
                                        type="text"
                                        x-model="dendaHilangFormatted"
                                        @input="
                                            dendaHilang = parseNumber($event.target.value);
                                            dendaHilangFormatted = formatRupiah(dendaHilang);
                                        "
                                        class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100"
                                        placeholder="Contoh: 100.000"
                                    >
                                </div>

                                <input type="hidden" name="denda_hilang" :value="dendaHilang">
                            </div>

                            <div class="mt-6 p-4 bg-slate-100 rounded-lg border border-slate-200">
                                <h4 class="font-semibold text-slate-800 mb-3">
                                    Ringkasan Denda
                                </h4>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-600">Denda Keterlambatan</span>
                                        <span class="font-medium" x-text="'Rp ' + formatRupiah(dendaKeterlambatan)"></span>
                                    </div>

                                    <div class="flex justify-between text-sm" x-show="kondisi === 'rusak' && dendaRusak > 0">
                                        <span class="text-slate-600">Denda Kerusakan</span>
                                        <span class="font-medium text-yellow-700" x-text="'Rp ' + formatRupiah(dendaRusak)"></span>
                                    </div>

                                    <div class="flex justify-between text-sm" x-show="kondisi === 'hilang' && dendaHilang > 0">
                                        <span class="text-slate-600">Ganti Rugi Hilang</span>
                                        <span class="font-medium text-red-700" x-text="'Rp ' + formatRupiah(dendaHilang)"></span>
                                    </div>

                                    <div class="border-t border-slate-300 pt-2 mt-2">
                                        <div class="flex justify-between font-bold text-slate-900">
                                            <span>TOTAL DENDA</span>
                                            <span class="text-red-600 text-lg" x-text="'Rp ' + formatRupiah(totalDenda)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="denda" :value="totalDenda">
                            <input type="hidden" name="days_late" :value="daysLate">
                        </div>

                        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-200">
                            <a
                                href="{{ route('admin.pengembalian.index') }}"
                                class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition-colors"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <i class="fas fa-check"></i>
                                <span>Proses Pengembalian</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

@endsection