@extends('layouts.admin')

@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')

@section('content')
    <!-- Flash Messages -->
    @if(session('success') || request()->query('created') == '1')
        <x-flash-message type="success" />
    @endif
    @if(session('deleted'))
        <x-flash-message type="deleted" />
    @endif
    @if(session('updated') || request()->query('updated') == '1')
        <x-flash-message type="updated" />
    @endif
    @if(session('error'))
        <x-flash-message type="error" message="{{ session('error') }}" />
    @endif

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-2xl font-bold text-gray-900">Kelola Buku</h3>
        <a href="{{ route('books.create') }}" class="flex items-center gap-2 px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors text-sm font-medium">
            <i class="fas fa-plus"></i>
            <span>Tambah Buku</span>
        </a>
    </div>

    <!-- Search & Filter (outside card) -->
    <form method="GET" action="{{ route('books.index') }}" id="filter-form" class="flex items-center justify-between mb-5">
        <!-- Search pill -->
        <div class="relative w-80">
            <i class="fas fa-search text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                placeholder="Search"
                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-full text-sm focus:outline-none focus:border-gray-400"
                autocomplete="off"
            >
        </div>

        <!-- Right: Category filter -->
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-600">Kategori :</span>
            <select name="category_id" id="category-select" class="gap-2 px-9 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400">
                <option value="">Semua</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->nama_kategori }}
                    </option>
                @endforeach
            </select>

        </div>
    </form>

    <script>
        (function () {
            var form = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var categorySelect = document.getElementById('category-select');
            var debounceTimer;

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 400);
            });

            categorySelect.addEventListener('change', function () {
                form.submit();
            });
        })();
    </script>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 mb-5 md:grid-cols-3">
        <!-- Total Judul -->
        <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
            <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
            <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
            <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>
            <div class="relative z-10 flex h-full items-center justify-between gap-5">
                <div class="pr-4">
                    <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Total Judul</p>
                    <p class="text-[18px] font-bold leading-tight text-white">{{ $totalJudul }} buku</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                    <i class="fas fa-book text-[18px]" style="color: #1799c9;"></i>
                </div>
            </div>
        </div>
        <!-- Total Eksemplar -->
        <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
            <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
            <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
            <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>
            <div class="relative z-10 flex h-full items-center justify-between gap-5">
                <div class="pr-4">
                    <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Total Eksemplar</p>
                    <p class="text-[18px] font-bold leading-tight text-white">{{ $totalEksemplar }} eksemplar</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                    <i class="fas fa-layer-group text-[18px]" style="color: #1799c9;"></i>
                </div>
            </div>
        </div>
        <!-- Kategori Aktif -->
        <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
            <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
            <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
            <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>
            <div class="relative z-10 flex h-full items-center justify-between gap-5">
                <div class="pr-4">
                    <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Kategori Aktif</p>
                    <p class="text-[18px] font-bold leading-tight text-white">{{ $totalKategori }} kategori</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                    <i class="fas fa-tags text-[18px]" style="color: #1799c9;"></i>
                </div>
            </div>
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
                        <th class="px-5 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-5 py-3 text-left font-semibold">Penulis</th>
                        <th class="px-5 py-3 text-left font-semibold">Tahun</th>
                        <th class="px-5 py-3 text-center font-semibold w-24">Stok</th>
                        <th class="px-5 py-3 text-center font-semibold w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $index => $book)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm text-gray-500">{{ $books->firstItem() + $index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($book->foto)
                                    <img src="{{ asset('storage/' . $book->foto) }}" alt="{{ $book->judul }}" class="w-9 h-12 object-cover rounded shrink-0 border border-gray-200">
                                @else
                                    <div class="w-9 h-12 bg-gray-100 rounded shrink-0 border border-gray-200 flex items-center justify-center">
                                        <i class="fas fa-book text-gray-300 text-xs"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate max-w-56">{{ $book->judul }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-56">{{ $book->penerbit }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $book->category->nama_kategori }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $book->penulis }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $book->tahun }}</td>
                        <td class="px-5 py-4 text-center">
                            @php
                                $availableCount = $book->availableItems()->count();
                                $totalCount = $book->bookItems()->count();
                            @endphp
                            @if($availableCount > 0)
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                    {{ $availableCount }}/{{ $totalCount }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                    Habis
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('books.show', $book->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-100 hover:bg-cyan-200 text-cyan-600 transition-colors">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('books.edit', $book->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-600 transition-colors">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button type="button" @click="$dispatch('open-confirm-delete', { url: '{{ route('books.destroy', $book->id) }}' })" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-book text-5xl mb-3 block"></i>
                            <p class="text-base font-medium text-gray-500">Tidak ada data buku</p>
                            <p class="text-sm mt-1">Klik tombol "Tambah Buku" untuk menambahkan buku baru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $books->firstItem() ?? 0 }}–{{ $books->lastItem() ?? 0 }} dari {{ $books->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if ($books->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center text-gray-300 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $books->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @php
                    $start = max(1, $books->currentPage() - 1);
                    $end   = min($books->lastPage(), $books->currentPage() + 1);
                @endphp

                @if($start > 1)
                    <a href="{{ $books->url(1) }}" class="w-8 h-8 flex items-center justify-center text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">1</a>
                    @if($start > 2)
                        <span class="w-8 h-8 flex items-center justify-center text-sm text-gray-400">...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $books->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center text-sm bg-cyan-500 text-white rounded-lg font-medium">{{ $i }}</span>
                    @else
                        <a href="{{ $books->url($i) }}" class="w-8 h-8 flex items-center justify-center text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">{{ $i }}</a>
                    @endif
                @endfor

                @if($end < $books->lastPage())
                    @if($end < $books->lastPage() - 1)
                        <span class="w-8 h-8 flex items-center justify-center text-sm text-gray-400">...</span>
                    @endif
                    <a href="{{ $books->url($books->lastPage()) }}" class="w-8 h-8 flex items-center justify-center text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">{{ $books->lastPage() }}</a>
                @endif

                @if ($books->hasMorePages())
                    <a href="{{ $books->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center text-gray-300 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <x-confirm-delete />
@endsection

