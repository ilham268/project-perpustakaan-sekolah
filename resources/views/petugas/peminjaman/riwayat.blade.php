@extends('layouts.petugas')

@section('title', 'Riwayat Pengembalian')
@section('page-title', 'Riwayat Pengembalian')

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
        <h3 class="text-2xl font-bold text-gray-900">Riwayat Pengembalian</h3>
        <a href="{{ route('pengembalian.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus"></i>
            <span>Tambah Pengembalian</span>
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('peminjaman.riwayat') }}" id="filter-form" class="flex items-center justify-between mb-5 gap-4">
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
            <span class="text-sm font-medium text-gray-600">Kondisi :</span>
            <select name="kondisi" id="kondisi-select" class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400">
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

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 gap-4 mb-5 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
            <div class="mb-4 flex items-center gap-3 text-gray-500">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-50 text-cyan-500">
                    <i class="fas fa-rotate-left text-sm"></i>
                </div>
                <p class="text-base font-medium text-gray-500">Selesai</p>
            </div>
            <p class="text-3xl font-semibold leading-none text-gray-950">{{ $totalPengembalian }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
            <div class="mb-4 flex items-center gap-3 text-gray-500">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-50 text-amber-500">
                    <i class="fas fa-triangle-exclamation text-sm"></i>
                </div>
                <p class="text-base font-medium text-gray-500">Bermasalah</p>
            </div>
            <p class="text-3xl font-semibold leading-none text-gray-950">{{ $totalBermasalah }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
            <div class="mb-4 flex items-center gap-3 text-gray-500">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-500">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </div>
                <p class="text-base font-medium text-gray-500">Denda</p>
            </div>
            <p class="text-3xl font-semibold leading-none text-gray-950">{{ number_format($totalDendaSum, 0, ',', '.') }}</p>
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
                        <th class="px-5 py-3 text-center font-semibold">Tgl Tempo</th>
                        <th class="px-5 py-3 text-center font-semibold">Tgl Kembali</th>
                        <th class="px-5 py-3 text-center font-semibold">Kondisi</th>
                        <th class="px-5 py-3 text-center font-semibold">Denda</th>


                        <th class="px-5 py-3 text-center font-semibold w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $return)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm text-gray-500">{{ $returns->firstItem() + $loop->index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($return->loan->bookItem->book->foto)
                                    <img src="{{ asset('storage/' . $return->loan->bookItem->book->foto) }}"
                                         class="w-9 h-12 object-cover rounded shrink-0 border border-gray-200">
                                @else
                                    <div class="w-9 h-12 bg-gray-100 rounded shrink-0 border border-gray-200 flex items-center justify-center">
                                        <i class="fas fa-book text-gray-300 text-xs"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate max-w-48">{{ $return->loan->bookItem->book->judul }}</p>
                                    <p class="text-xs text-gray-400">{{ $return->loan->bookItem->kode_buku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $return->loan->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $return->loan->user->nomor_identitas }}</p>
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($return->loan->tanggal_kembali)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($return->kondisi == 'baik')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Baik</span>
                            @elseif($return->kondisi == 'rusak')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700">Rusak</span>
                            @elseif($return->kondisi == 'hilang')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Hilang</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($return->denda > 0)
                                <span class="text-sm font-semibold text-red-600">Rp {{ number_format($return->denda, 0, ',', '.') }}</span>
                            @else
                                    <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>


                        <td class="px-5 py-4 text-center">
                            @if($return->denda > 0)
                                <a href="{{ route('pengembalian.invoice', $return->id) }}"
                                   class="w-8 h-8 inline-flex items-center justify-center bg-cyan-100 hover:bg-cyan-200 text-cyan-600 rounded-lg transition-colors"
                                   title="Download Invoice">
                                    <i class="fas fa-file-invoice text-sm"></i>
                                </a>
                            @else
                                    <span class="text-gray-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-history text-5xl mb-3 block"></i>
                            <p class="text-base font-medium text-gray-500">Belum ada riwayat pengembalian</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($returns->total() > 0)
    <div class="flex items-center justify-between mt-4">
        <p class="text-sm text-gray-500">
            Menampilkan {{ $returns->firstItem() }}&ndash;{{ $returns->lastItem() }} dari {{ $returns->total() }} data
        </p>
        <div class="flex items-center gap-1">
            @if($returns->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 bg-white border border-gray-200 text-sm cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $returns->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            @foreach($returns->getUrlRange(1, $returns->lastPage()) as $page => $url)
                @if($page == $returns->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-500 text-white text-sm font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm">{{ $page }}</a>
                @endif
            @endforeach
            @if($returns->hasMorePages())
                <a href="{{ $returns->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 bg-white border border-gray-200 text-sm cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        </div>
    </div>
    @endif

@endsection
