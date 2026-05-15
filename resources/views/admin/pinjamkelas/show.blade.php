@extends('layouts.admin')

@section('title', 'Input Peminjaman Kelas - ' . $kategori->nama_kategori)
@section('page-title', 'Input Peminjaman Kelas: ' . $kategori->nama_kategori)

@section('content')

<div class="space-y-6">

    {{-- Page Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 md:px-7 md:py-6 shadow-md shadow-emerald-100/60">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Input Peminjaman Kelas
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Tambahkan data peminjaman buku kelas berdasarkan kategori dan siswa yang sudah terdaftar.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 w-full lg:w-[360px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Kelas
                            </p>
                            <p class="mt-1 text-xl font-extrabold tracking-tight text-white truncate">
                                {{ $kategori->kelas }}
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas fa-school text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Data Siswa
                            </p>
                            <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                {{ $siswas->count() }}
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Form Card --}}
        <div class="xl:col-span-2">
            <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                            <i class="fas fa-book-reader text-lg"></i>
                        </div>

                        <div class="min-w-0">
                            <h2 class="text-lg md:text-xl font-bold text-slate-900">
                                Form Peminjaman Kelas
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Kategori:
                                <span class="font-semibold text-emerald-700">
                                    {{ $kategori->nama_kategori }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="p-5 md:p-6">
                    <form action="{{ route('admin.pinjamkelas.kategori.proses') }}" method="POST" class="space-y-6">
                        @csrf

                        <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">

                        {{-- Info Box --}}
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-circle-info"></i>
                                </div>

                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">
                                        Informasi Peminjaman
                                    </h4>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                        Pilih siswa sesuai kelas, lalu masukkan kode buku yang akan dipinjam.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Pilih Siswa --}}
                        <div>
                            <label for="user_id" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-user-graduate text-xs text-slate-400"></i>
                                Pilih Siswa
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="user_id"
                                id="user_id"
                                class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                required
                            >
                                <option value="">Pilih siswa kelas {{ $kategori->kelas }}</option>

                                @foreach($siswas as $siswa)
                                    <option value="{{ $siswa->id }}">
                                        {{ $siswa->name }} - {{ $siswa->nomor_identitas }} - {{ $siswa->kelas }}
                                    </option>
                                @endforeach
                            </select>

                            @if($siswas->isEmpty())
                                <div class="mt-2 flex items-start gap-2 rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600 ring-1 ring-red-100">
                                    <i class="fas fa-circle-exclamation mt-0.5 text-xs"></i>
                                    <span>
                                        Belum ada data siswa untuk kelas {{ $kategori->kelas }}. Tambahkan user dengan role siswa terlebih dahulu.
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Kode Buku --}}
                        <div>
                            <label for="kode_buku" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-barcode text-xs text-slate-400"></i>
                                Kode Buku
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="kode_buku"
                                id="kode_buku"
                                class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm font-semibold uppercase tracking-wide text-slate-700 shadow-sm placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                placeholder="Masukkan kode buku, contoh: B001"
                                required
                                oninput="this.value = this.value.toUpperCase()"
                            >

                            <p class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                                <i class="fas fa-circle-info text-slate-400"></i>
                                Pastikan kode buku sesuai dengan label atau barcode buku.
                            </p>
                        </div>

                        {{-- Action Button --}}
                        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                            <a
                                href="{{ route('admin.pinjamkelas.kategori') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-100/80 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-200/70 hover:text-slate-800"
                            >
                                <i class="fas fa-arrow-left text-xs"></i>
                                Kembali
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-emerald-100 transition hover:-translate-y-0.5 hover:bg-emerald-700"
                            >
                                <i class="fas fa-save text-xs"></i>
                                Simpan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="xl:col-span-1">
            <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden xl:sticky xl:top-6">

                {{-- Header --}}
                <div class="p-5 border-b border-slate-100 bg-white/80">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                            <i class="fas fa-users"></i>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                Siswa Kelas {{ $kategori->kelas }}
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Daftar siswa yang tersedia untuk kategori ini.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Student List --}}
                <div class="p-4 max-h-[420px] overflow-y-auto">
                    @if($siswas->isEmpty())
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-400 ring-1 ring-slate-200">
                                <i class="fas fa-user-slash text-xl"></i>
                            </div>

                            <p class="mt-4 text-sm font-bold text-slate-700">
                                Belum ada siswa
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-slate-400">
                                Tambahkan data siswa sesuai kelas agar bisa melakukan input peminjaman.
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($siswas as $siswa)
                                <div class="group rounded-2xl border border-slate-100 bg-slate-50/70 p-3 transition hover:border-emerald-100 hover:bg-emerald-50/50">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold text-slate-800">
                                                {{ $siswa->name }}
                                            </p>
                                            <p class="mt-0.5 truncate text-xs text-slate-500">
                                                {{ $siswa->nomor_identitas }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Footer Info --}}
                <div class="border-t border-slate-100 bg-slate-50/80 px-5 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-500">
                            Total Siswa
                        </span>

                        <span class="inline-flex items-center justify-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ $siswas->count() }} siswa
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection