@extends('layouts.admin')

@section('title', 'Edit Buku')

@section('content')
<div class="mb-4">
    <nav class="flex items-center text-sm text-gray-700">
        <a href="{{ route('books.index') }}" class="font-semibold hover:text-cyan-600 transition-colors">Kelola Buku</a>
        <span class="mx-2 font-semibold">/</span>
        <span class="text-cyan-600 font-semibold">Edit Buku</span>
    </nav>
</div>

<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-900">Edit Buku</h3>
    <p class="mt-1 text-sm text-gray-500">
        Edit data utama buku sesuai kolom Excel dan tahun pengadaan.
    </p>
</div>

@if(session('error'))
    <x-flash-message type="error" message="{{ session('error') }}" />
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form method="POST" action="{{ route('books.update', $book->id) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="tahun_pengadaan" class="block text-sm text-gray-700 mb-1.5">
                    Tahun Pengadaan
                </label>

                <input
                    type="number"
                    name="tahun_pengadaan"
                    id="tahun_pengadaan"
                    value="{{ old('tahun_pengadaan', $book->tahun_pengadaan) }}"
                    min="2020"
                    max="2100"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    placeholder="Contoh: 2025"
                >

                @error('tahun_pengadaan')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nomor_klasifikasi" class="block text-sm text-gray-700 mb-1.5">
                    Nomor Klasifikasi
                </label>

                <input
                    type="text"
                    name="nomor_klasifikasi"
                    id="nomor_klasifikasi"
                    value="{{ old('nomor_klasifikasi', $book->nomor_klasifikasi) }}"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    placeholder="Contoh: 080, 420, 540"
                >

                @error('nomor_klasifikasi')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jenis_koleksi" class="block text-sm text-gray-700 mb-1.5">
                    Jenis Koleksi
                </label>

                <input
                    type="text"
                    name="jenis_koleksi"
                    id="jenis_koleksi"
                    value="{{ old('jenis_koleksi', $book->jenis_koleksi) }}"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    placeholder="BOS / Referensi / Paket"
                >

                @error('jenis_koleksi')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="judul" class="block text-sm text-gray-700 mb-1.5">
                Judul Buku <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="judul"
                id="judul"
                value="{{ old('judul', $book->judul) }}"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                placeholder="Masukkan judul buku"
                required
            >

            @error('judul')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="penulis" class="block text-sm text-gray-700 mb-1.5">
                    Pengarang / Penulis
                </label>

                <input
                    type="text"
                    name="penulis"
                    id="penulis"
                    value="{{ old('penulis', $book->penulis) }}"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    placeholder="Nama pengarang"
                >

                @error('penulis')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="penerbit" class="block text-sm text-gray-700 mb-1.5">
                    Penerbit
                </label>

                <input
                    type="text"
                    name="penerbit"
                    id="penerbit"
                    value="{{ old('penerbit', $book->penerbit) }}"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    placeholder="Nama penerbit"
                >

                @error('penerbit')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="tahun" class="block text-sm text-gray-700 mb-1.5">
                    Tahun Terbit
                </label>

                <input
                    type="number"
                    name="tahun"
                    id="tahun"
                    value="{{ old('tahun', $book->tahun) }}"
                    min="1900"
                    max="{{ date('Y') + 1 }}"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                >

                @error('tahun')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sumber_buku" class="block text-sm text-gray-700 mb-1.5">
                    Sumber Buku
                </label>

                <input
                    type="text"
                    name="sumber_buku"
                    id="sumber_buku"
                    value="{{ old('sumber_buku', $book->sumber_buku) }}"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    placeholder="BOS / BPOPP / SUMBANGAN"
                >

                @error('sumber_buku')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jumlah_eksemplar" class="block text-sm text-gray-700 mb-1.5">
                    Jumlah Eksemplar <span class="text-red-500">*</span>
                </label>

                <input
                    type="number"
                    name="jumlah_eksemplar"
                    id="jumlah_eksemplar"
                    value="{{ old('jumlah_eksemplar', $book->bookItems()->count()) }}"
                    min="0"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    required
                >

                @error('jumlah_eksemplar')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Tahun pengadaan dipakai untuk membedakan data import 2023, 2024, 2025, 2026, dan seterusnya. Tahun terbit tetap dipakai untuk tahun penerbitan buku.
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            Jika jumlah eksemplar dinaikkan, sistem akan menambah item kosong. Jika dikurangi, sistem hanya akan menghapus item yang belum punya kode buku.
        </div>

        <div class="flex items-center justify-center gap-3 pt-4">
            <a
                href="{{ route('books.index') }}"
                class="px-8 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors text-sm font-medium"
            >
                Kembali
            </a>

            <button
                type="submit"
                class="px-8 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors text-sm font-medium"
            >
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection