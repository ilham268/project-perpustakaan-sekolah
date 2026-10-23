@extends('layouts.peminjam')

@section('title', 'Buku Tamu Guru')
@section('page-title', 'Buku Tamu Guru')

@section('content')

<div class="space-y-5">

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
                    Kunjungan Guru / Pendidik
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Buku Tamu Guru
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Silakan ketik nama Bapak/Ibu Guru dan isi keperluan kunjungan di perpustakaan.
                </p>
            </div>

            <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-white backdrop-blur-md md:flex">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">

        <div class="border-b border-[var(--hairline)] bg-[var(--emerald-tint)]/60 px-5 py-4">
            <h3 class="font-display text-base font-semibold text-[var(--forest)]">
                Form Buku Tamu Guru
            </h3>
            <p class="mt-1 text-xs text-[var(--muted)]">
                Ketik nama Anda di bawah ini.
            </p>
        </div>

        <div class="p-5 md:p-6">
            <form action="{{ route('teacher-guest-book.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    {{-- Form Nama Guru (Murni Kosong) --}}
                    <div>
                        <label for="nama" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Nama Lengkap Guru <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            list="guru_list"
                            autocomplete="off"
                            value="{{ old('nama') }}"
                            placeholder="Ketik nama Bapak/Ibu Guru..."
                            required
                            autofocus
                            class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('nama') ? 'border-red-500' : '' }}"
                        >

                        @if(isset($guruList) && count($guruList) > 0)
                            <datalist id="guru_list">
                                @foreach($guruList as $guru)
                                    <option value="{{ $guru->name ?? $guru->nama }}"></option>
                                @endforeach
                            </datalist>
                        @endif

                        @error('nama')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Form Keperluan --}}
                    @php
                        $oldKeperluan = old('keperluan');
                        $isOldCustom = $oldKeperluan && !in_array($oldKeperluan, [
                            'Meminjam buku',
                            'Mengembalikan buku',
                            'Membaca buku di tempat',
                            'Belajar atau mengerjakan tugas',
                            'Mencari referensi atau informasi',
                            'Berkunjung atau keperluan lainnya',
                        ]);
                        $initialKeperluan = $isOldCustom ? 'Lainnya' : ($oldKeperluan ?: 'Berkunjung atau keperluan lainnya');
                        $customKeperluanVal = old('keperluan_custom', ($isOldCustom ? preg_replace('/^Lainnya:\s*/', '', $oldKeperluan) : ''));
                    @endphp

                    <div x-data="{ selectedKeperluan: '{{ $initialKeperluan }}' }">
                        <label for="keperluan" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Keperluan <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select
                                id="keperluan"
                                name="keperluan"
                                x-model="selectedKeperluan"
                                required
                                class="w-full appearance-none rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 pr-10 text-sm font-medium text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('keperluan') ? 'border-red-500' : '' }}"
                            >
                                <option value="Meminjam buku">Meminjam buku</option>
                                <option value="Mengembalikan buku">Mengembalikan buku</option>
                                <option value="Membaca buku di tempat">Membaca buku di tempat</option>
                                <option value="Belajar atau mengerjakan tugas">Belajar atau mengerjakan tugas</option>
                                <option value="Mencari referensi atau informasi">Mencari referensi atau informasi</option>
                                <option value="Berkunjung atau keperluan lainnya">Berkunjung atau keperluan lainnya</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-[var(--muted)]">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>

                        {{-- Textarea jika Lainnya dipilih --}}
                        <div x-show="selectedKeperluan === 'Lainnya'" x-cloak class="mt-3">
                            <label for="keperluan_custom" class="mb-1.5 block text-xs font-semibold text-[var(--text)]/80">
                                Tuliskan Keperluan Lainnya <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="keperluan_custom"
                                name="keperluan_custom"
                                rows="3"
                                placeholder="Tuliskan keperluan kunjungan Bapak/Ibu Guru..."
                                :required="selectedKeperluan === 'Lainnya'"
                                class="w-full resize-none rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('keperluan_custom') ? 'border-red-500' : '' }}"
                            >{{ $customKeperluanVal }}</textarea>
                            @error('keperluan_custom')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @error('keperluan')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end border-t border-[var(--hairline)] pt-5">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        >
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Kirim Data Kunjungan</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var namaInput = document.getElementById('nama');
    if (namaInput) {
        namaInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var keperluanSelect = document.getElementById('keperluan');
                if (keperluanSelect) {
                    keperluanSelect.focus();
                }
            }
        });
    }
});
</script>

@endsection