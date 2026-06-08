@extends('layouts.admin')

@section('title', 'Input Peminjaman Buku')
@section('page-title', 'Input Peminjaman Buku')

@section('content')

@php
    $tanggalPinjamDefault = old('tanggal_pinjam', now()->format('Y-m-d'));
    $tanggalKembaliDefault = old('tanggal_kembali', now()->addDays($lamaPinjamDefault ?? 7)->format('Y-m-d'));
@endphp

<div class="space-y-6">

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

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ $errors->first() }}
            </span>
        </div>
    @endif

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Peminjaman Manual
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Input Peminjaman Buku
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    Admin dapat menginput peminjaman buku untuk siswa secara langsung tanpa menunggu siswa mengajukan.
                </p>
            </div>

            <a
                href="{{ route('admin.peminjaman.index') }}"
                class="inline-flex h-10 w-fit items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-6 py-5">
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Form Input Peminjaman
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih siswa, pilih buku, masukkan kode buku fisik, lalu simpan peminjaman.
                    </p>
                </div>

                <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="space-y-5 p-6">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Siswa <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="user_id"
                            required
                            class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Pilih siswa</option>

                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}" {{ old('user_id') == $siswa->id ? 'selected' : '' }}>
                                    {{ $siswa->name }} - {{ $siswa->nomor_identitas ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Buku Referensi <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="book_id"
                            required
                            class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Pilih buku</option>

                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->judul }} - Stok tersedia: {{ $book->stok_tersedia ?? 0 }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Kode Buku Fisik <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode_buku"
                            value="{{ old('kode_buku') }}"
                            required
                            placeholder="Masukkan kode buku fisik"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                        >

                        <p class="mt-2 text-xs text-slate-400">
                            Kode buku harus sesuai dengan judul buku yang dipilih dan statusnya masih tersedia.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Tanggal Pinjam <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_pinjam"
                                id="tanggal_pinjam"
                                value="{{ $tanggalPinjamDefault }}"
                                required
                                class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Tanggal Kembali <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_kembali"
                                id="tanggal_kembali"
                                value="{{ $tanggalKembaliDefault }}"
                                required
                                class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                            >

                            <p class="mt-2 text-xs text-slate-400">
                                Otomatis {{ $lamaPinjamDefault ?? 7 }} hari dari tanggal pinjam, tapi tetap bisa diedit.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                        <a
                            href="{{ route('admin.peminjaman.index') }}"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            <i class="fas fa-save text-xs"></i>
                            <span>Simpan Peminjaman</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="rounded-3xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-blue-800">
                            Cara Kerja
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-blue-700">
                            Peminjaman yang diinput admin akan langsung berstatus disetujui.
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-blue-700">
                            Kode buku yang dipilih otomatis berubah menjadi dipinjam.
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-blue-700">
                            Tanggal kembali mengikuti setting lama pinjam default.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4 rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-amber-800">
                            Catatan
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-amber-700">
                            Jika kode buku tidak cocok dengan judul buku, sistem akan menolak input peminjaman.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    (function () {
        var tanggalPinjam = document.getElementById('tanggal_pinjam');
        var tanggalKembali = document.getElementById('tanggal_kembali');
        var lamaPinjamDefault = {{ (int) ($lamaPinjamDefault ?? 7) }};

        if (!tanggalPinjam || !tanggalKembali) {
            return;
        }

        tanggalPinjam.addEventListener('change', function () {
            if (!tanggalPinjam.value) {
                return;
            }

            var date = new Date(tanggalPinjam.value);
            date.setDate(date.getDate() + lamaPinjamDefault);

            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');

            tanggalKembali.value = year + '-' + month + '-' + day;
        });
    })();
</script>

@endsection