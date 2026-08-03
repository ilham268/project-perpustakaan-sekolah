@extends('layouts.admin')

@section('title', 'Edit Buku')

@section('content')
<div class="mb-4">
    <nav class="flex items-center text-sm">
        <a href="{{ route('books.index') }}" class="font-semibold text-[var(--text)]/70 transition-colors hover:text-[var(--emerald-deep)]">Kelola Buku</a>
        <span class="mx-2 font-semibold text-[var(--hairline)]">/</span>
        <span class="font-semibold text-[var(--emerald-deep)]">Edit Buku</span>
    </nav>
</div>

<div class="mb-6">
    <h3 class="font-display text-2xl font-semibold text-[var(--forest)]">Edit Buku</h3>
    <p class="mt-1 text-sm text-[var(--muted)]">
        Edit data utama buku sesuai kolom Excel dan tahun pengadaan.
    </p>
</div>

@if(session('error'))
    <x-flash-message type="error" message="{{ session('error') }}" />
@endif

<div class="rounded-2xl border border-[var(--hairline)] bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('books.update', $book->id) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label for="tahun_pengadaan" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Tahun Pengadaan
                </label>

                <input
                    type="number"
                    name="tahun_pengadaan"
                    id="tahun_pengadaan"
                    value="{{ old('tahun_pengadaan', $book->tahun_pengadaan) }}"
                    min="2020"
                    max="2100"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    placeholder="Contoh: 2025"
                >

                @error('tahun_pengadaan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nomor_klasifikasi" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Nomor Klasifikasi
                </label>

                <input
                    type="text"
                    name="nomor_klasifikasi"
                    id="nomor_klasifikasi"
                    value="{{ old('nomor_klasifikasi', $book->nomor_klasifikasi) }}"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    placeholder="Contoh: 080, 420, 540"
                >

                @error('nomor_klasifikasi')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jenis_koleksi" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Jenis Koleksi
                </label>

                <input
                    type="text"
                    name="jenis_koleksi"
                    id="jenis_koleksi"
                    value="{{ old('jenis_koleksi', $book->jenis_koleksi) }}"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    placeholder="BOS / Referensi / Paket"
                >

                @error('jenis_koleksi')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="judul" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                Judul Buku <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="judul"
                id="judul"
                value="{{ old('judul', $book->judul) }}"
                class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                placeholder="Masukkan judul buku"
                required
            >

            @error('judul')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="penulis" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Pengarang / Penulis
                </label>

                <input
                    type="text"
                    name="penulis"
                    id="penulis"
                    value="{{ old('penulis', $book->penulis) }}"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    placeholder="Nama pengarang"
                >

                @error('penulis')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="penerbit" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Penerbit
                </label>

                <input
                    type="text"
                    name="penerbit"
                    id="penerbit"
                    value="{{ old('penerbit', $book->penerbit) }}"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    placeholder="Nama penerbit"
                >

                @error('penerbit')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label for="tahun" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Tahun Terbit
                </label>

                <input
                    type="number"
                    name="tahun"
                    id="tahun"
                    value="{{ old('tahun', $book->tahun) }}"
                    min="1900"
                    max="{{ date('Y') + 1 }}"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >

                @error('tahun')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sumber_buku" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Sumber Buku
                </label>

                <input
                    type="text"
                    name="sumber_buku"
                    id="sumber_buku"
                    value="{{ old('sumber_buku', $book->sumber_buku) }}"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    placeholder="BOS / BPOPP / SUMBANGAN"
                >

                @error('sumber_buku')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jumlah_eksemplar" class="mb-1.5 block text-sm font-semibold text-[var(--text)]">
                    Jumlah Eksemplar <span class="text-red-500">*</span>
                </label>

                <input
                    type="number"
                    name="jumlah_eksemplar"
                    id="jumlah_eksemplar"
                    value="{{ old('jumlah_eksemplar', $book->bookItems()->count()) }}"
                    min="0"
                    class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    required
                >

                @error('jumlah_eksemplar')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Tahun pengadaan dipakai untuk membedakan data import 2023, 2024, 2025, 2026, dan seterusnya. Tahun terbit tetap dipakai untuk tahun penerbitan buku.
        </div>

        <div class="rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)]/70">
            Jika jumlah eksemplar dinaikkan, sistem akan menambah item kosong. Jika dikurangi, sistem hanya akan menghapus item yang belum punya kode buku.
        </div>

        <div class="flex items-center justify-center gap-3 pt-4">
            <a
                href="{{ route('books.index') }}"
                class="rounded-xl border border-[var(--hairline)] bg-white px-8 py-2.5 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/50"
            >
                Kembali
            </a>

            <button
                type="submit"
                class="rounded-xl bg-[var(--emerald-deep)] px-8 py-2.5 text-sm font-semibold text-white transition hover:bg-[var(--forest)]"
            >
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection