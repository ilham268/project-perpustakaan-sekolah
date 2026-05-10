@extends('layouts.admin')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')

@section('content')

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800">Buku Tamu</h3>
        <button onclick="openExportModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
            <i class="fas fa-file-export text-xs"></i>
            <span>Export Excel</span>
        </button>
    </div>

    <!-- Search & Date Filter -->
    <form method="GET" action="{{ route('admin.guest-book.index') }}" id="filter-form" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                placeholder="Cari nama atau keperluan..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition"
                autocomplete="off"
            >
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-slate-600">Tanggal :</span>
            <input type="date" name="start_date" id="start-date"
                   value="{{ request('start_date') }}"
                   class="px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
            <span class="text-sm text-slate-500">s/d</span>
            <input type="date" name="end_date" id="end-date"
                   value="{{ request('end_date') }}"
                   class="px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
        </div>
    </form>

    <script>
        (function () {
            var form        = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var startDate   = document.getElementById('start-date');
            var endDate     = document.getElementById('end-date');
            var debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () { form.submit(); }, 400);
            });
            startDate.addEventListener('change', function () { form.submit(); });
            endDate.addEventListener('change', function () { form.submit(); });
        })();
    </script>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
        <!-- Total Kunjungan -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Total Kunjungan</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalKunjungan) }} kunjungan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Hari Ini -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-500 to-teal-400 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Hari Ini</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($todayKunjungan) }} kunjungan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Bulan Ini -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-5 shadow-sm">
            <div class="absolute rounded-full bg-white/10" style="right: -40px; top: -50px; width: 150px; height: 150px;"></div>
            <div class="absolute rounded-full bg-white/15" style="right: -20px; top: -25px; width: 110px; height: 110px;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/90">Bulan Ini</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($monthKunjungan) }} kunjungan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead>
                    <tr class="bg-emerald-600 text-white text-sm">
                        <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Nama</th>
                        <th class="px-5 py-3 text-left font-semibold">Keperluan</th>
                        <th class="px-5 py-3 text-center font-semibold w-32">Tanggal</th>
                        <th class="px-5 py-3 text-center font-semibold w-20">Jam</th>
                        <th class="px-5 py-3 text-center font-semibold w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($guestBooks as $guest)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $guestBooks->firstItem() + $loop->index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                                    <i class="fas fa-user text-emerald-600 text-xs"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-800">{{ $guest->nama }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600 max-w-sm">
                            {{ Str::limit($guest->keperluan, 80) }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse($guest->created_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse($guest->created_at)->format('H:i') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button @click="$dispatch('open-confirm-delete', { url: '{{ route('admin.guest-book.destroy', $guest->id) }}' })"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors"
                                    title="Hapus Data">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <i class="fas fa-book-open text-5xl mb-3 block text-slate-300"></i>
                            <p class="text-base font-medium text-slate-500">Tidak ada data buku tamu</p>
                            <p class="text-sm text-slate-400 mt-1">Belum ada pengunjung yang mengisi buku tamu</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($guestBooks->total() > 0)
        <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $guestBooks->firstItem() }}–{{ $guestBooks->lastItem() }} dari {{ $guestBooks->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if($guestBooks->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 bg-white border border-slate-200 cursor-not-allowed">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $guestBooks->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($guestBooks->getUrlRange(1, $guestBooks->lastPage()) as $page => $url)
                    @if($page == $guestBooks->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-600 text-white text-sm font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-sm">{{ $page }}</a>
                    @endif
                @endforeach

                @if($guestBooks->hasMorePages())
                    <a href="{{ $guestBooks->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
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

    <x-confirm-delete title="Hapus Data Buku Tamu?" message="Apakah Anda yakin ingin menghapus data buku tamu ini? Tindakan ini tidak dapat dibatalkan." />

    <!-- Export Modal -->
    <x-export-modal
        :route="route('admin.guest-book.export')"
        title="Export Laporan Buku Tamu"
        :hasStatus="false"
    />

@endsection