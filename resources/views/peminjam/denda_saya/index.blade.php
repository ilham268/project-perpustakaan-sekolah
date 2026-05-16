@extends('layouts.peminjam')

@section('title', 'Denda Saya')
@section('page-title', 'Denda Saya')

@section('content')

@if($denda->total() < 1)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-16 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-check-circle text-4xl text-green-500"></i>
            </div>

            <h3 class="text-lg font-semibold text-slate-800 mb-2">
                Selamat! Tidak Ada Denda
            </h3>

            <p class="text-slate-500">
                Anda tidak memiliki denda yang harus dibayarkan.
            </p>

            <p class="text-slate-400 text-sm mt-2">
                Terima kasih telah mengembalikan buku tepat waktu.
            </p>
        </div>
    </div>
@else
    <!-- Stats Card -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-red-600 to-red-500 rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80">Total Denda</p>

                    <p class="text-2xl font-bold text-white mt-1">
                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-white/60 mt-2">
                        Segera lunasi denda untuk melanjutkan peminjaman
                    </p>
                </div>

                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('siswa.denda.index') }}" class="mb-6">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari judul, kategori, atau kode buku..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition"
                autocomplete="off"
            >
        </div>
    </form>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-800">
                Daftar Denda
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul / Kategori
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Sumber
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Kondisi
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-right">
                            Denda
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tanggal
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($denda as $item)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $denda->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $item->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $item->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if(($item->tipe ?? '') == 'kelas')
                                    <span class="font-semibold text-purple-600">
                                        Pinjam Kelas
                                    </span>
                                @else
                                    <span class="font-semibold text-blue-600">
                                        Pinjam Buku
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if(($item->kondisi ?? '') == 'rusak')
                                    <span class="font-semibold text-orange-600">
                                        Rusak
                                    </span>
                                @elseif(($item->kondisi ?? '') == 'hilang')
                                    <span class="font-semibold text-red-600">
                                        Hilang
                                    </span>
                                @elseif(($item->kondisi ?? '') == 'baik')
                                    <span class="font-semibold text-green-600">
                                        Baik
                                    </span>
                                @else
                                    <span class="font-semibold text-slate-500">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-right">
                                <span class="font-bold text-red-600">
                                    Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ !empty($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if(($item->status ?? 'pending') == 'paid')
                                    <span class="font-semibold text-green-600">
                                        Lunas
                                    </span>
                                @else
                                    <span class="font-semibold text-yellow-600">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($denda->total() > 0)
            <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Menampilkan {{ $denda->firstItem() }}–{{ $denda->lastItem() }} dari {{ $denda->total() }} data
                </p>

                <div>
                    {{ $denda->links() }}
                </div>
            </div>
        @endif
    </div>
@endif

@endsection