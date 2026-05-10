@extends('layouts.admin')

@section('title', 'Riwayat Pengembalian')
@section('page-title', 'Riwayat Pengembalian')

@section('content')

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800">Riwayat Pengembalian</h3>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('admin.peminjaman.riwayat') }}" id="filter-form" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Cari judul buku atau nama peminjam..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition" autocomplete="off">
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-slate-600">Kondisi :</span>
            <select name="kondisi" id="kondisi-select" class="px-6 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                <option value="" {{ !request('kondisi') ? 'selected' : '' }}>Semua</option>
                <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
            </select>
        </div>
    </form>

    <script>
        (function () {
            var form = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var kondisiSelect = document.getElementById('kondisi-select');
            var debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () { form.submit(); }, 400);
            });
            kondisiSelect.addEventListener('change', function () { form.submit(); });
        })();
    </script>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
        <!-- Selesai / Total Pengembalian -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Total Pengembalian</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalPengembalian) }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-rotate-left text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Bermasalah -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Bermasalah</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalBermasalah) }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Denda -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Total Denda</p>
                    <p class="text-xl font-bold text-white mt-1">Rp {{ number_format($totalDendaSum, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="bg-emerald-600 text-white text-sm">
                        <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Judul Buku</th>
                        <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-5 py-3 text-center font-semibold w-24">Kondisi</th>
                        <th class="px-5 py-3 text-center font-semibold w-28">Denda</th>
                        <th class="px-5 py-3 text-center font-semibold w-32">Tgl Kembali</th>
                        <th class="px-5 py-3 text-center font-semibold w-32">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $return)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $returns->firstItem() + $loop->index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($return->loan->bookItem->book->foto)
                                    <img src="{{ asset('storage/' . $return->loan->bookItem->book->foto) }}"
                                         alt="{{ $return->loan->bookItem->book->judul }}"
                                         class="w-9 h-12 object-cover rounded border border-slate-200 shrink-0">
                                @else
                                    <div class="w-9 h-12 bg-slate-100 rounded border border-slate-200 shrink-0 flex items-center justify-center">
                                        <i class="fas fa-book text-slate-300 text-xs"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate max-w-48">{{ $return->loan->bookItem->book->judul }}</p>
                                    <p class="text-xs text-slate-400">{{ $return->loan->bookItem->kode_buku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-slate-800">{{ $return->loan->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $return->loan->user->nomor_identitas }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($return->kondisi == 'baik')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Baik</span>
                            @elseif($return->kondisi == 'rusak')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700">Rusak</span>
                            @elseif($return->kondisi == 'hilang')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Hilang</span>
                            @endif
                        </tr>
                        <td class="px-5 py-4 text-center">
                            @if($return->denda > 0)
                                <span class="text-sm font-bold text-red-600">Rp {{ number_format($return->denda, 0, ',', '.') }}</span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ $return->loan->petugas->name ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <i class="fas fa-history text-5xl mb-3 block text-slate-300"></i>
                            <p class="text-base font-medium text-slate-500">Belum ada riwayat pengembalian</p>
                            <p class="text-sm text-slate-400 mt-1">Data pengembalian buku akan muncul di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($returns->total() > 0)
        <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $returns->firstItem() }}–{{ $returns->lastItem() }} dari {{ $returns->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if($returns->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 bg-white border border-slate-200 cursor-not-allowed">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $returns->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($returns->getUrlRange(1, $returns->lastPage()) as $page => $url)
                    @if($page == $returns->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-600 text-white text-sm font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-sm">{{ $page }}</a>
                    @endif
                @endforeach

                @if($returns->hasMorePages())
                    <a href="{{ $returns->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 bg-white border border-slate-200 cursor-not-allowed">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

@endsection