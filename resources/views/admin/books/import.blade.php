@extends('layouts.admin')

@section('title', 'Import Excel Buku')
@section('page-title', 'Import Excel Buku')

@section('content')
<div class="space-y-6">

    @if(session('error'))
        <x-flash-message type="error" message="{{ session('error') }}" />
    @endif

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-emerald-50">
                    <a href="{{ route('books.index') }}" class="transition hover:text-white">
                        Kelola Buku
                    </a>
                    <i class="fas fa-chevron-right text-[10px] text-emerald-100"></i>
                    <span class="text-white">Import Excel</span>
                </div>

                <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Import Data Buku dari Excel
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Pilih tahun data terlebih dahulu, lalu upload file Excel buku. Sistem akan menyimpan data berdasarkan tahun pengadaan.
                </p>
            </div>

            <a
                href="{{ route('books.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/15 px-4 py-3 text-sm font-bold text-white ring-1 ring-white/25 backdrop-blur-md transition hover:-translate-y-0.5 hover:bg-white/20"
            >
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('books.import') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="tahun_pengadaan" class="mb-2 block text-sm font-semibold text-slate-700">
                    Tahun Data / Tahun Pengadaan <span class="text-red-500">*</span>
                </label>

                <select
                    name="tahun_pengadaan"
                    id="tahun_pengadaan"
                    required
                    class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    <option value="">Pilih Tahun Data</option>
                    <option value="2023" {{ old('tahun_pengadaan') == '2023' ? 'selected' : '' }}>2023</option>
                    <option value="2024" {{ old('tahun_pengadaan') == '2024' ? 'selected' : '' }}>2024</option>
                    <option value="2025" {{ old('tahun_pengadaan') == '2025' ? 'selected' : '' }}>2025</option>
                    <option value="2026" {{ old('tahun_pengadaan') == '2026' ? 'selected' : '' }}>2026</option>
                    <option value="2027" {{ old('tahun_pengadaan') == '2027' ? 'selected' : '' }}>2027</option>
                    <option value="2028" {{ old('tahun_pengadaan') == '2028' ? 'selected' : '' }}>2028</option>
                    <option value="2029" {{ old('tahun_pengadaan') == '2029' ? 'selected' : '' }}>2029</option>
                    <option value="2030" {{ old('tahun_pengadaan') == '2030' ? 'selected' : '' }}>2030</option>
                </select>

                @error('tahun_pengadaan')
                    <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1.5 text-xs font-medium text-slate-400">
                    Contoh: kalau file Excel yang diupload adalah data 2025, pilih tahun 2025.
                </p>
            </div>

            <div>
                <label for="file_excel" class="mb-2 block text-sm font-semibold text-slate-700">
                    File Excel Buku <span class="text-red-500">*</span>
                </label>

                <input
                    type="file"
                    name="file_excel"
                    id="file_excel"
                    accept=".xlsx,.xls"
                    required
                    class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >

                @error('file_excel')
                    <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800">
                <p class="font-semibold">Catatan Import</p>
                <p class="mt-1">
                    Data akan disimpan sesuai tahun pengadaan yang dipilih. Jadi data 2023, 2024, 2025, dan 2026 tidak saling menimpa.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-700">Struktur data yang dibaca:</p>

                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                        <p class="text-xs font-bold uppercase text-slate-400">Kategori Utama</p>
                        <p class="mt-1 text-sm font-bold text-slate-800">BOS</p>
                    </div>

                    <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                        <p class="text-xs font-bold uppercase text-slate-400">Isi BOS</p>
                        <p class="mt-1 text-sm font-bold text-slate-800">Referensi</p>
                    </div>

                    <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                        <p class="text-xs font-bold uppercase text-slate-400">Isi BOS</p>
                        <p class="mt-1 text-sm font-bold text-slate-800">Paket</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-800">
                <p class="font-semibold">Penting</p>
                <p class="mt-1">
                    Tahun data berbeda dengan tahun terbit buku. Tahun data dipakai untuk mengelompokkan import buku seperti 2023, 2024, 2025, dan 2026.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('books.index') }}"
                    class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    <i class="fas fa-file-excel mr-1"></i>
                    Import Excel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection