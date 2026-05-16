@extends('layouts.peminjam')

@section('title', 'Buku Pinjaman')
@section('page-title', 'Buku Pinjaman - Peminjaman Kelas')

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

    {{-- Page Header --}}
    <div class="flex flex-col gap-1">
        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Buku Pinjaman Kelas
        </h3>

        <p class="text-sm text-slate-500">
            Daftar buku yang sedang dipinjam secara kolektif.
        </p>
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 bg-gradient-to-r from-emerald-50 to-white px-5 py-4">
            <h3 class="text-base font-bold text-slate-900">
                Data Buku Pinjaman Kelas
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Riwayat pengajuan dan status peminjaman buku kelas.
            </p>
        </div>

        @if($pinjamKelas->isNotEmpty())

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                                No
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Kategori
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Kode Buku
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-center">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pinjamKelas as $index => $item)
                            <tr class="transition-colors hover:bg-slate-50">

                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $pinjamKelas->firstItem() + $index }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $item->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $item->kode_buku ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @if($item->status == 'pending')
                                        <span class="font-semibold text-amber-600">
                                            Pending
                                        </span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="font-semibold text-emerald-600">
                                            Disetujui
                                        </span>
                                    @elseif($item->status == 'dikembalikan')
                                        <span class="font-semibold text-blue-600">
                                            Dikembalikan
                                        </span>
                                    @else
                                        <span class="font-semibold text-slate-500">
                                            {{ ucfirst($item->status ?? '-') }}
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-slate-100 bg-white px-5 py-4">
                {{ $pinjamKelas->links() }}
            </div>

        @else

            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>

                <p class="mt-4 text-base font-bold text-slate-700">
                    Belum Ada Peminjaman Kelas
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Silakan ajukan peminjaman melalui menu Input Buku.
                </p>

                <a
                    href="{{ route('siswa.pinjamkelas.input') }}"
                    class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
                >
                    <i class="fas fa-plus text-xs"></i>
                    <span>Input Buku</span>
                </a>
            </div>

        @endif

    </div>

</div>

@endsection