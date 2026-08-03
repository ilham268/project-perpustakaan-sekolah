@extends('layouts.peminjam')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')

@section('content')

<div class="space-y-5">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-check-circle"></i>
                </div>

                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Kunjungan&nbsp;Perpustakaan
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Buku Tamu
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Silakan isi identitas dan keperluan Anda mengunjungi perpustakaan.
                </p>
            </div>

            <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-white backdrop-blur-md md:flex">
                <i class="fas fa-book-open-reader text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">

        <div class="border-b border-[var(--hairline)] bg-[var(--emerald-tint)]/60 px-5 py-4">
            <h3 class="font-display text-base font-semibold text-[var(--forest)]">
                Form Buku Tamu
            </h3>

            <p class="mt-1 text-xs text-[var(--muted)]">
                Lengkapi data berikut sebelum melanjutkan aktivitas di perpustakaan.
            </p>
        </div>

        <div class="p-5 md:p-6">
            <form action="{{ route('guest-book.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>
                        <label for="nama" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Nama Lengkap
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="{{ old('nama') }}"
                            placeholder="Masukkan nama lengkap"
                            required
                            class="w-full rounded-xl border bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('nama') ? 'border-red-500' : 'border-[var(--hairline)]' }}"
                        >

                        @error('nama')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <label for="kelas" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Kelas
                        </label>

                        <input
                            type="text"
                            id="kelas"
                            name="kelas"
                            value="{{ old('kelas') }}"
                            placeholder="Contoh: XI RPL 2"
                            class="w-full rounded-xl border bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('kelas') ? 'border-red-500' : 'border-[var(--hairline)]' }}"
                        >

                        @error('kelas')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Keperluan --}}
                    <div>
                        <label for="keperluan" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Keperluan
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            id="keperluan"
                            name="keperluan"
                            rows="4"
                            placeholder="Contoh: Meminjam buku, membaca, mengerjakan tugas, dan lainnya."
                            required
                            class="w-full resize-none rounded-xl border bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('keperluan') ? 'border-red-500' : 'border-[var(--hairline)]' }}"
                        >{{ old('keperluan') }}</textarea>

                        @error('keperluan')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end border-t border-[var(--hairline)] pt-5">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        >
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Kirim</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>

</div>

@endsection