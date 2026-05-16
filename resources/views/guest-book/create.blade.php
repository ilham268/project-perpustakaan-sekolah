@extends('layouts.peminjam')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')

@section('content')

<div class="space-y-5">

    {{-- Success Message --}}
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

    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-6 shadow-md shadow-emerald-100/70 md:px-7 md:py-7">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Buku Tamu
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Silakan isi identitas dan keperluan Anda mengunjungi perpustakaan.
                </p>
            </div>

            <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/20 backdrop-blur-md md:flex">
                <i class="fas fa-book-open-reader text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 bg-gradient-to-r from-emerald-50 to-white px-5 py-4">
            <h3 class="text-base font-bold text-slate-900">
                Form Buku Tamu
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Lengkapi data berikut sebelum melanjutkan aktivitas di perpustakaan.
            </p>
        </div>

        <div class="p-5 md:p-6">
            <form action="{{ route('guest-book.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>
                        <label for="nama" class="mb-2 block text-sm font-bold text-slate-700">
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
                            class="w-full rounded-xl border bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 {{ $errors->has('nama') ? 'border-red-500' : 'border-slate-200' }}"
                        >

                        @error('nama')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Keperluan --}}
                    <div>
                        <label for="keperluan" class="mb-2 block text-sm font-bold text-slate-700">
                            Keperluan
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            id="keperluan"
                            name="keperluan"
                            rows="4"
                            placeholder="Contoh: Meminjam buku, membaca, mengerjakan tugas, dan lainnya."
                            required
                            class="w-full resize-none rounded-xl border bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 {{ $errors->has('keperluan') ? 'border-red-500' : 'border-slate-200' }}"
                        >{{ old('keperluan') }}</textarea>

                        @error('keperluan')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end border-t border-slate-100 pt-5">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
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