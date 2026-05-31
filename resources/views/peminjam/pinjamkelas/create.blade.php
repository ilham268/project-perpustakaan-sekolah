@extends('layouts.peminjam')

@section('title', 'Input Buku Paket')
@section('page-title', 'Input Buku Paket')

@section('content')

@php
    $booksPaket = $booksPaket ?? collect();
@endphp

<div class="mx-auto max-w-3xl space-y-6">

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
                Peminjaman Buku Paket
            </p>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                Input Buku Paket
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                Pilih judul Buku Paket, lalu masukkan kode buku yang ada pada label buku fisik.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
            <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                Form Peminjaman Buku Paket
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kode buku harus sesuai dengan label yang ditempel pada buku.
            </p>
        </div>

        <div class="p-5 md:p-6">
            <form action="{{ route('siswa.pinjamkelas.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Pilih Buku Paket <span class="text-red-500">*</span>
                        </label>

                        @if($booksPaket->isNotEmpty())
                            <select
                                name="book_id"
                                required
                                class="h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                                <option value="">Pilih Buku Paket</option>

                                @foreach($booksPaket as $book)
                                    <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->judul }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-2 text-xs text-slate-400">
                                Buku yang tampil hanya Buku Paket.
                            </p>
                        @else
                            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4">
                                <p class="text-sm font-bold text-red-700">
                                    Buku Paket belum tersedia
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-red-600">
                                    Belum ada data Buku Paket yang bisa dipinjam. Silakan hubungi petugas.
                                </p>
                            </div>
                        @endif

                        @error('book_id')
                            <p class="mt-2 text-xs font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Kode Buku <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode_buku"
                            value="{{ old('kode_buku') }}"
                            required
                            placeholder="Masukkan kode buku"
                            class="h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-semibold uppercase tracking-wide text-slate-700 placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            oninput="this.value = this.value.toUpperCase()"
                        >

                        <p class="mt-2 text-xs text-slate-400">
                            Masukkan kode buku sesuai label yang ditempel pada buku fisik.
                        </p>

                        @error('kode_buku')
                            <p class="mt-2 text-xs font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4">
                        <p class="text-sm font-bold text-blue-700">
                            Cara peminjaman
                        </p>

                        <p class="mt-1 text-sm leading-relaxed text-blue-600">
                            Pilih judul Buku Paket, lalu masukkan kode buku. Sistem akan mengecek apakah kode tersebut valid dan masih tersedia.
                        </p>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                        <a
                            href="{{ route('siswa.pinjamkelas.index') }}"
                            class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:hover:bg-slate-300"
                            {{ $booksPaket->isEmpty() ? 'disabled' : '' }}
                        >
                            Simpan Peminjaman
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</div>

@endsection