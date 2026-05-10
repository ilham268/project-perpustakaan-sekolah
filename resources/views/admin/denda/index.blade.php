@extends('layouts.admin')

@section('title', 'Rekap Denda')
@section('page-title', 'Rekap Denda')

@section('content')

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800">Rekap Denda</h3>
        <button onclick="openExportModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
            <i class="fas fa-file-export text-xs"></i>
            <span>Export Excel</span>
        </button>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('admin.denda.index') }}" id="filter-form" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                placeholder="Cari nama peminjam atau judul buku..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition"
                autocomplete="off"
            >
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-slate-600">Status :</span>
            <select name="status" id="status-select" class="px-6 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                <option value="all" {{ !request('status') || request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
    </form>

    <script>
        (function () {
            var form = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var statusSelect = document.getElementById('status-select');
            var debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () { form.submit(); }, 400);
            });
            statusSelect.addEventListener('change', function () { form.submit(); });
        })();
    </script>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
        <!-- Total Denda -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Total Denda</p>
                    <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-sack-dollar text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Belum Dibayar -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Belum Dibayar</p>
                    <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalPending, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Sudah Dibayar -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Sudah Dibayar</p>
                    <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="bg-emerald-600 text-white text-sm">
                        <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Judul Buku</th>
                        <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-5 py-3 text-center font-semibold w-24">Kondisi</th>
                        <th class="px-5 py-3 text-center font-semibold w-28">Denda</th>
                        <th class="px-5 py-3 text-center font-semibold w-24">Status</th>
                        <th class="px-5 py-3 text-center font-semibold w-32">Tgl Kembali</th>
                        <th class="px-5 py-3 text-center font-semibold w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($denda as $index => $return)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $denda->firstItem() + $loop->index }}</td>
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
                                    <p class="text-sm font-medium text-slate-800 truncate max-w-56">{{ $return->loan->bookItem->book->judul }}</p>
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
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-bold text-red-600">Rp {{ number_format($return->denda, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($return->status == 'paid')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Lunas</span>
                            @else
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('admin.pengembalian.invoice', $return->id) }}"
                               class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-600 transition-colors"
                               title="Lihat Invoice">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <i class="fas fa-money-bill-wave text-5xl mb-3 block text-slate-300"></i>
                            <p class="text-base font-medium text-slate-500">Tidak ada data denda</p>
                            <p class="text-sm text-slate-400 mt-1">Belum ada pengembalian dengan denda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($denda->total() > 0)
        <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $denda->firstItem() }}–{{ $denda->lastItem() }} dari {{ $denda->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if($denda->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 bg-white border border-slate-200 cursor-not-allowed">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $denda->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($denda->getUrlRange(1, $denda->lastPage()) as $page => $url)
                    @if($page == $denda->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-600 text-white text-sm font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-sm">{{ $page }}</a>
                    @endif
                @endforeach

                @if($denda->hasMorePages())
                    <a href="{{ $denda->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
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

    <x-export-modal
        :route="route('admin.denda.export')"
        title="Export Laporan Denda"
    />
@endsection