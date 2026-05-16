@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')

@section('content')

<div class="space-y-5">

    {{-- Alert Success --}}
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

    {{-- Alert Deleted --}}
    @if(session('deleted'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-trash"></i>
                </div>

                <span>{{ session('deleted') }}</span>
            </div>
        </div>
    @endif

    {{-- Alert Updated --}}
    @if(session('updated'))
        <div class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-blue-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                    <i class="fas fa-pen"></i>
                </div>

                <span>{{ session('updated') }}</span>
            </div>
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>

                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col gap-1">
        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Riwayat Peminjaman Saya
        </h3>

        <p class="text-sm text-slate-500">
            Daftar semua riwayat peminjaman buku Anda.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Total Riwayat --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Riwayat
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $loans->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-book-open text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Menunggu --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Menunggu
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $loans->where('status', 'pending')->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Dikembalikan --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Dikembalikan
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $loans->where('status', 'dikembalikan')->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="text-base font-bold text-slate-900">
                Data Riwayat Peminjaman
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Riwayat pengajuan, persetujuan, penolakan, dan pengembalian buku.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1180px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kategori
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Status
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Petugas
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tgl Pinjam
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tgl Kembali
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($loans as $index => $loan)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $index + 1 }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $loan->bookItem->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $loan->bookItem->book->category->nama_kategori ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($loan->status == 'pending')
                                    <span class="font-semibold text-amber-600">
                                        Pending
                                    </span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="font-semibold text-emerald-600">
                                        Disetujui
                                    </span>
                                @elseif($loan->status == 'dikembalikan')
                                    <span class="font-semibold text-blue-600">
                                        Dikembalikan
                                    </span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="font-semibold text-red-600">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="font-semibold text-slate-500">
                                        {{ ucfirst($loan->status ?? '-') }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $loan->petugas->name ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ $loan->tanggal_pinjam ? \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') : '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($loan->tanggal_kembali)
                                    <span class="text-slate-500">
                                        {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="italic text-slate-400">
                                        Belum dikembalikan
                                    </span>
                                @endif
                            </td>
                        </tr>

                        {{-- Alasan ditolak jika ada --}}
                        @if($loan->status == 'ditolak' && $loan->alasan_ditolak)
                            <tr class="bg-red-50">
                                <td colspan="8" class="border border-red-100 px-5 py-3">
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-info-circle mt-0.5 text-sm text-red-500"></i>

                                        <div>
                                            <span class="text-sm font-bold text-red-800">
                                                Alasan Ditolak:
                                            </span>

                                            <span class="ml-1 text-sm text-red-700">
                                                {{ $loan->alasan_ditolak }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-book-reader text-2xl"></i>
                                </div>

                                <p class="mt-4 text-base font-bold text-slate-700">
                                    Belum Ada Riwayat Peminjaman
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
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