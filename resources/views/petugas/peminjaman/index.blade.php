@extends('layouts.petugas')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Peminjaman')

@section('content')

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-times-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-2xl font-bold text-gray-900">Daftar Peminjaman</h3>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('peminjaman.index') }}" id="filter-form" class="flex items-center justify-between mb-5 gap-4">
        <div class="relative w-80">
            <i class="fas fa-search text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                placeholder="Cari peminjam, judul, kode buku..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-full text-sm focus:outline-none focus:border-gray-400"
                autocomplete="off"
            >
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-600">Status :</span>
            <select name="status" id="status-select" class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400">
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

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 gap-4 mb-5 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
            <div class="mb-4 flex items-center gap-3 text-gray-500">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-400">
                    <i class="fas fa-book-open text-sm"></i>
                </div>
                <p class="text-base font-medium text-gray-500">Baru</p>
            </div>
            <p class="text-3xl font-semibold leading-none text-gray-950">{{ $totalPeminjaman }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
            <div class="mb-4 flex items-center gap-3 text-gray-500">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-50 text-amber-500">
                    <i class="fas fa-gear text-sm"></i>
                </div>
                <p class="text-base font-medium text-gray-500">Proses</p>
            </div>
            <p class="text-3xl font-semibold leading-none text-gray-950">{{ $totalPending }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
            <div class="mb-4 flex items-center gap-3 text-gray-500">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-50 text-cyan-500">
                    <i class="fas fa-circle-check text-sm"></i>
                </div>
                <p class="text-base font-medium text-gray-500">Selesai</p>
            </div>
            <p class="text-3xl font-semibold leading-none text-gray-950">{{ $totalDisetujui }}</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-cyan-500 text-white text-sm">
                        <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Judul Buku</th>
                        <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-5 py-3 text-center font-semibold">Status</th>
                        <th class="px-5 py-3 text-center font-semibold">Tgl Pinjam</th>
                        <th class="px-5 py-3 text-center font-semibold">Tgl Tempo</th>
                        <th class="px-5 py-3 text-center font-semibold w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm text-gray-500">{{ $loans->firstItem() + $loop->index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($loan->bookItem->book->foto)
                                    <img src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                         class="w-9 h-12 object-cover rounded shrink-0 border border-gray-200">
                                @else
                                    <div class="w-9 h-12 bg-gray-100 rounded shrink-0 border border-gray-200 flex items-center justify-center">
                                        <i class="fas fa-book text-gray-300 text-xs"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate max-w-48">{{ $loan->bookItem->book->judul }}</p>
                                    <p class="text-xs text-gray-400">{{ $loan->bookItem->kode_buku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $loan->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $loan->user->nomor_identitas }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($loan->status == 'pending')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                            @elseif($loan->status == 'disetujui')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Disetujui</span>
                            @elseif($loan->status == 'ditolak')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Ditolak</span>
                            @elseif($loan->status == 'dikembalikan')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Dikembalikan</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($loan->status == 'pending')
                                    <form action="{{ route('peminjaman.approve', $loan->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-green-100 hover:bg-green-200 text-green-600 rounded-lg transition-colors"
                                                title="Setujui">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <button onclick="openRejectModal({{ $loan->id }}, '{{ addslashes($loan->bookItem->book->judul) }}', '{{ addslashes($loan->user->name) }}')"
                                            class="w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors"
                                            title="Tolak">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                @elseif($loan->status == 'disetujui')
                                    <a href="{{ route('peminjaman.download-kartu', $loan->id) }}"
                                       class="w-8 h-8 flex items-center justify-center bg-cyan-100 hover:bg-cyan-200 text-cyan-600 rounded-lg transition-colors"
                                       title="Download Kartu">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-inbox text-5xl mb-3 block"></i>
                            <p class="text-base font-medium text-gray-500">Tidak ada data peminjaman</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($loans->total() > 0)
    <div class="flex items-center justify-between mt-4">
        <p class="text-sm text-gray-500">
            Menampilkan {{ $loans->firstItem() }}&ndash;{{ $loans->lastItem() }} dari {{ $loans->total() }} data
        </p>
        <div class="flex items-center gap-1">
            @if($loans->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 bg-white border border-gray-200 text-sm cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $loans->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            @foreach($loans->getUrlRange(1, $loans->lastPage()) as $page => $url)
                @if($page == $loans->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-500 text-white text-sm font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm">{{ $page }}</a>
                @endif
            @endforeach
            @if($loans->hasMorePages())
                <a href="{{ $loans->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 bg-white border border-gray-200 text-sm cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        </div>
    </div>
    @endif

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/70 z-50 items-center justify-center">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <form id="rejectForm" method="POST" action="{{ session('reject_loan_id') ? route('peminjaman.reject', session('reject_loan_id')) : '' }}">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="alasan_ditolak" rows="3" required
                          class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 mb-1 {{ $errors->has('alasan_ditolak') ? 'border-red-500' : 'border-gray-300' }}"
                          placeholder="Masukkan alasan penolakan...">{{ old('alasan_ditolak') }}</textarea>
                @error('alasan_ditolak')
                    <p class="text-xs text-red-600 mb-3">{{ $message }}</p>
                @enderror
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm transition-colors">Batal</button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm transition-colors">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id, judul, nama) {
            document.getElementById('rejectForm').action = `{{ url('/peminjaman') }}/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }

        @if($errors->has('alasan_ditolak') && session('reject_loan_id'))
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        @endif
    </script>

@endsection

