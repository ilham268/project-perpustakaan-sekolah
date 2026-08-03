@extends('layouts.admin')

@section('title', 'Import Excel Buku')
@section('page-title', 'Import Excel Buku')

@section('content')
<div class="space-y-6">

    @if(session('error'))
        <x-flash-message type="error" message="{{ session('error') }}" />
    @endif

    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="catalog-eyebrow mb-3 flex items-center gap-2 uppercase text-white/70">
                    <a href="{{ route('books.index') }}" class="transition hover:text-white">
                        Kelola Buku
                    </a>
                    <i class="fas fa-chevron-right text-[9px] text-white/50"></i>
                    <span class="text-white">Import Excel</span>
                </div>

                <h1 class="font-display text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Import Data Buku dari Excel
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Ketik atau pilih tahun data terlebih dahulu, lalu upload file Excel buku. Sistem akan menyimpan data berdasarkan tahun pengadaan.
                </p>
            </div>

            <a
                href="{{ route('books.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/15 px-4 py-3 text-sm font-semibold text-white backdrop-blur-md transition hover:-translate-y-0.5 hover:bg-white/20"
            >
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-[var(--hairline)] bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('books.import') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="tahun_pengadaan" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                    Tahun Data / Tahun Pengadaan <span class="text-red-500">*</span>
                </label>

                {{-- PERUBAHAN DI SINI: Menggunakan input number alih-alih select dropdown --}}
                <input
                    type="number"
                    name="tahun_pengadaan"
                    id="tahun_pengadaan"
                    min="1990"
                    max="2100"
                    step="1"
                    value="{{ old('tahun_pengadaan', date('Y')) }}"
                    required
                    class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >

                @error('tahun_pengadaan')
                    <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1.5 text-xs font-medium text-[var(--muted)]">
                    Contoh: Anda bisa langsung mengetik tahun 2025 atau menggunakan panah atas/bawah di pojok kotak.
                </p>
            </div>

            <div>
                <label for="file_excel" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                    File Excel Buku <span class="text-red-500">*</span>
                </label>

                <input
                    type="file"
                    name="file_excel"
                    id="file_excel"
                    accept=".xlsx,.xls"
                    required
                    class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >

                @error('file_excel')
                    <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-[var(--hairline)] bg-[var(--emerald-tint)] p-4 text-sm text-[var(--emerald-deep)]">
                <p class="font-semibold">Catatan Import</p>
                <p class="mt-1">
                    Data akan disimpan sesuai tahun pengadaan yang dipilih. Jadi data 2023, 2024, 2025, dan 2026 tidak saling menimpa.
                </p>
            </div>

            <div class="rounded-xl border border-[var(--hairline)] bg-[var(--paper)] p-4 text-sm text-[var(--text)]/70">
                <p class="font-semibold text-[var(--text)]">Struktur data yang dibaca:</p>

                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-[var(--hairline)] bg-white p-4">
                        <p class="catalog-eyebrow uppercase text-[var(--muted)]">Kategori Utama</p>
                        <p class="font-display mt-1 text-sm font-semibold text-[var(--forest)]">BOS</p>
                    </div>

                    <div class="rounded-xl border border-[var(--hairline)] bg-white p-4">
                        <p class="catalog-eyebrow uppercase text-[var(--muted)]">Isi BOS</p>
                        <p class="font-display mt-1 text-sm font-semibold text-[var(--forest)]">Referensi</p>
                    </div>

                    <div class="rounded-xl border border-[var(--hairline)] bg-white p-4">
                        <p class="catalog-eyebrow uppercase text-[var(--muted)]">Isi BOS</p>
                        <p class="font-display mt-1 text-sm font-semibold text-[var(--forest)]">Paket</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-800">
                <p class="font-semibold">Penting</p>
                <p class="mt-1">
                    Tahun data berbeda dengan tahun terbit buku. Tahun data dipakai untuk mengelompokkan import buku seperti 2023, 2024, 2025, dan seterusnya.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('books.index') }}"
                    class="rounded-xl border border-[var(--hairline)] bg-white px-5 py-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-[var(--emerald-deep)] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)]"
                >
                    <i class="fas fa-file-excel mr-1"></i>
                    Import Excel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection