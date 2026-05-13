@extends('layouts.admin')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Peminjaman')

@section('content')
    @if(session('success'))
        <x-flash-message type="success" />
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
            <i class="fas fa-times-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
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
            <input 
                type="text" 
                name="search" 
                id="search-input" 
                value="{{ request('search') }}" 
                placeholder="Cari judul buku atau nama peminjam..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition" 
                autocomplete="off"
            >
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
                debounceTimer = setTimeout(function () { 
                    form.submit(); 
                }, 400);
            });

            statusSelect.addEventListener('change', function () { 
                form.submit(); 
            });
        })();
    </script>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
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
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="bg-emerald-600 text-white text-sm">
                        <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Judul Buku</th>
                        <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-5 py-3 text-center font-semibold">Kategori</th>
                        <th class="px-5 py-3 text-center font-semibold">Status</th>
                        <th class="px-5 py-3 text-center font-semibold">Tgl Pinjam</th>
                        <th class="px-5 py-3 text-center font-semibold">Tgl Kembali</th>
                        <th class="px-5 py-3 text-center font-semibold w-28">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-slate-500">
                            {{ $loans->firstItem() + $loop->index }}
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($loan->bookItem && $loan->bookItem->book && $loan->bookItem->book->foto)
                                    <img src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                         alt="{{ $loan->bookItem->book->judul }}"
                                         class="w-9 h-12 object-cover rounded border border-slate-200 shrink-0">
                                @else
                                    <div class="w-9 h-12 bg-slate-100 rounded border border-slate-200 shrink-0 flex items-center justify-center">
                                        <i class="fas fa-book text-slate-300 text-xs"></i>
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate max-w-52">
                                        {{ $loan->bookItem->book->judul ?? '-' }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $loan->bookItem->kode_buku ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-slate-800">
                                {{ $loan->user->name ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $loan->user->nomor_identitas ?? '-' }}
                            </p>
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
                            @else
                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">-</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ $loan->tanggal_pinjam ? \Carbon\Carbon::parse($loan->tanggal_pinjam)->translatedFormat('d M Y') : '-' }}
                        </td>

                        <td class="px-5 py-4 text-center text-sm text-slate-500">
                            {{ $loan->tanggal_kembali ? \Carbon\Carbon::parse($loan->tanggal_kembali)->translatedFormat('d M Y') : '-' }}
                        </td>

                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($loan->status == 'pending')
                                    <form action="{{ route('admin.peminjaman.approve', $loan->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button 
                                            type="submit"
                                            class="w-8 h-8 flex items-center justify-center bg-green-100 hover:bg-green-200 text-green-600 rounded-lg transition-colors"
                                            title="Setujui"
                                            onclick="return confirm('Setujui peminjaman ini?')"
                                        >
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>

                                    <button 
                                        type="button"
                                        onclick="openRejectModal({{ $loan->id }}, '{{ addslashes($loan->bookItem->book->judul ?? '-') }}', '{{ addslashes($loan->user->name ?? '-') }}')"
                                        class="w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors"
                                        title="Tolak"
                                    >
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                @elseif($loan->status == 'disetujui')
                                    <a 
                                        href="{{ route('admin.peminjaman.download-kartu', $loan->id) }}"
                                        class="w-8 h-8 flex items-center justify-center bg-emerald-100 hover:bg-emerald-200 text-emerald-600 rounded-lg transition-colors"
                                        title="Download Kartu"
                                    >
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
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
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-600 text-white text-sm font-medium">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-sm">
                            {{ $page }}
                        </a>
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

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/70 z-50 items-center justify-center">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <form id="rejectForm" method="POST" action="">
                @csrf

                <h3 class="text-lg font-bold text-slate-800 mb-4">Tolak Peminjaman</h3>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>

                <textarea 
                    name="alasan_ditolak" 
                    rows="3" 
                    required
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-1 {{ $errors->has('alasan_ditolak') ? 'border-red-500' : 'border-slate-300' }}"
                    placeholder="Masukkan alasan penolakan..."
                >{{ old('alasan_ditolak') }}</textarea>

                @error('alasan_ditolak')
                    <p class="text-xs text-red-600 mb-3">{{ $message }}</p>
                @enderror

                <div class="flex gap-3 mt-4">
                    <button 
                        type="button" 
                        onclick="closeRejectModal()"
                        class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 text-sm transition-colors"
                    >
                        Batal
                    </button>

                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm transition-colors"
                    >
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
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

    <script>
        function openRejectModal(id, judul, nama) {
            document.getElementById('rejectForm').action = `{{ url('/admin/peminjaman') }}/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }

        @if($errors->has('alasan_ditolak') && session('reject_loan_id'))
            document.getElementById('rejectForm').action = `{{ url('/admin/peminjaman') }}/{{ session('reject_loan_id') }}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        @endif
    </script>

@endsection