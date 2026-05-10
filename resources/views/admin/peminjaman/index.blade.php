@extends('layouts.admin')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Peminjaman')

@section('content')
    @if(session('success'))
        <x-flash-message type="success" />
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800">Daftar Peminjaman</h3>
        <button onclick="openExportModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
            <i class="fas fa-file-export text-xs"></i>
            <span>Export Excel</span>
        </button>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('admin.peminjaman.index') }}" id="filter-form" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Cari judul buku atau nama peminjam..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition" autocomplete="off">
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-slate-600">Status :</span>
            <select name="status" id="status-select" class="px-6 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                <option value="" {{ !request('status') ? 'selected' : '' }}>Semua</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
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
        <!-- Baru / Total Peminjaman -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Total Peminjaman</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalPeminjaman) }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-book-open text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Proses / Pending -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Menunggu Persetujuan</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalPending) }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Selesai / Disetujui -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Selesai</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalDisetujui) }}</p>
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
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="bg-emerald-600 text-white text-sm">
                        <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Judul Buku</th>
                        <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-5 py-3 text-center font-semibold">Kategori</th>
                        <th class="px-5 py-3 text-center font-semibold">Status</th>
                        <th class="px-5 py-3 text-center font-semibold">Tgl Pinjam</th>
                        <th class="px-5 py-3 text-center font-semibold">Tgl Kembali</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $loans->firstItem() + $loop->index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($loan->bookItem->book->foto)
                                    <img src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                         alt="{{ $loan->bookItem->book->judul }}"
                                         class="w-9 h-12 object-cover rounded border border-slate-200 shrink-0">
                                @else
                                    <div class="w-9 h-12 bg-slate-100 rounded border border-slate-200 shrink-0 flex items-center justify-center">
                                        <i class="fas fa-book text-slate-300 text-xs"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate max-w-52">{{ $loan->bookItem->book->judul }}</p>
                                    <p class="text-xs text-slate-400">{{ $loan->bookItem->kode_buku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-slate-800">{{ $loan->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $loan->user->nomor_identitas }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                {{ $loan->bookItem->book->category->nama_kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($loan->status == 'pending')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Pending</span>
                            @elseif($loan->status == 'disetujui')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Disetujui</span>
                            @elseif($loan->status == 'ditolak')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Ditolak</span>
                            @elseif($loan->status == 'dikembalikan')
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Dikembalikan</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->translatedFormat('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <i class="fas fa-book-open text-5xl mb-3 block text-slate-300"></i>
                            <p class="text-base font-medium text-slate-500">Tidak ada data peminjaman</p>
                            <p class="text-sm text-slate-400 mt-1">Belum ada pengajuan peminjaman buku</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($loans->total() > 0)
        <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $loans->firstItem() }}–{{ $loans->lastItem() }} dari {{ $loans->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if($loans->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 bg-white border border-slate-200 cursor-not-allowed">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $loans->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($loans->getUrlRange(1, $loans->lastPage()) as $page => $url)
                    @if($page == $loans->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-600 text-white text-sm font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-sm">{{ $page }}</a>
                    @endif
                @endforeach

                @if($loans->hasMorePages())
                    <a href="{{ $loans->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
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
        :route="route('admin.peminjaman.export')"
        title="Export Laporan Peminjaman"
        :statusOptions="[
            'pending' => 'Pending',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dikembalikan' => 'Dikembalikan'
        ]"
    />

@endsection