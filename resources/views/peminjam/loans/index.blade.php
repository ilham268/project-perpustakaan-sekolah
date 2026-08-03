@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')

@section('content')

<div class="space-y-6">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Alert Deleted --}}
    @if(session('deleted'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">{{ session('deleted') }}</span>
        </div>
    @endif

    {{-- Alert Updated --}}
    @if(session('updated'))
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-sky-800 shadow-sm">
            <span class="text-sm font-medium">{{ session('updated') }}</span>
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10">
            <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                Data&nbsp;Peminjaman
            </p>

            <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                Riwayat Peminjaman Saya
            </h1>

            <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                Daftar semua riwayat pengajuan, persetujuan, penolakan, dan pengembalian buku Anda.
            </p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Total Riwayat --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-[var(--emerald)]/10">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-[var(--emerald-tint)] transition group-hover:bg-[var(--emerald-tint)]"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                        Total Riwayat
                    </p>

                    <p class="font-mono-stat mt-2 text-3xl font-semibold tracking-tight text-[var(--text)]">
                        {{ $loans->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                    <i class="fas fa-book-open text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Menunggu --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                        Menunggu
                    </p>

                    <p class="font-mono-stat mt-2 text-3xl font-semibold tracking-tight text-[var(--text)]">
                        {{ $loans->where('status', 'pending')->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Dikembalikan --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:bg-sky-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="catalog-eyebrow font-semibold uppercase text-[var(--muted)]">
                        Dikembalikan
                    </p>

                    <p class="font-mono-stat mt-2 text-3xl font-semibold tracking-tight text-[var(--text)]">
                        {{ $loans->where('status', 'dikembalikan')->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="border-b border-[var(--hairline)] px-6 py-5">
            <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                Data Riwayat Peminjaman
            </h2>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Riwayat pengajuan, persetujuan, penolakan, dan pengembalian buku.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1180px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Judul Buku
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Kode Buku
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Kategori
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Status
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Petugas
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Tgl Pinjam
                        </th>

                        <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Tgl Kembali
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($loans as $index => $loan)
                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                {{ $index + 1 }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[280px] truncate font-semibold text-[var(--text)]">
                                    {{ $loan->bookItem->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $loan->bookItem->book->category->nama_kategori ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($loan->status == 'pending')
                                    <span class="font-semibold text-amber-600">
                                        Pending
                                    </span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="font-semibold text-[var(--emerald-deep)]">
                                        Disetujui
                                    </span>
                                @elseif($loan->status == 'dikembalikan')
                                    <span class="font-semibold text-sky-600">
                                        Dikembalikan
                                    </span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="font-semibold text-red-600">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="font-semibold text-[var(--text)]/70">
                                        {{ ucfirst($loan->status ?? '-') }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $loan->petugas->name ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                {{ $loan->tanggal_pinjam ? \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') : '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($loan->tanggal_kembali)
                                    <span class="text-[var(--muted)]">
                                        {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="italic text-[var(--muted)]/70">
                                        Belum dikembalikan
                                    </span>
                                @endif
                            </td>
                        </tr>

                        {{-- Alasan ditolak jika ada --}}
                        @if($loan->status == 'ditolak' && $loan->alasan_ditolak)
                            <tr class="bg-red-50">
                                <td colspan="8" class="border border-red-100 px-5 py-3">
                                    <span class="text-sm font-bold text-red-800">
                                        Alasan Ditolak:
                                    </span>

                                    <span class="ml-1 text-sm text-red-700">
                                        {{ $loan->alasan_ditolak }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                                    <i class="fas fa-book-open text-2xl"></i>
                                </div>

                                <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                                    Belum Ada Riwayat Peminjaman
                                </p>

                                <p class="mt-1 text-sm text-[var(--muted)]">
                                    Anda belum memiliki riwayat peminjaman buku.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection