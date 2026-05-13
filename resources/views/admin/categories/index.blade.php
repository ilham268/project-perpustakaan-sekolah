@extends('layouts.admin')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

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
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800">Kategori Buku</h3>
        <button
            @click="$dispatch('open-modal', 'create-category')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors text-sm font-medium shadow-sm"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('categories.index') }}" id="filter-form" class="mb-6">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                placeholder="Cari kategori..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition"
                autocomplete="off"
            >
        </div>
    </form>

    <script>
        (function () {
            var form = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () { form.submit(); }, 400);
            });
        })();
    </script>

    <!-- Category List Accordion -->
    @if($categories->count())
        <div x-data="{ openRow: null }" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-600 text-white text-sm">
                            <th class="px-4 py-3 text-center font-semibold w-16"></th>
                            <th class="px-4 py-3 text-center font-semibold w-16">No</th>
                            <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                            <th class="px-4 py-3 text-left font-semibold w-56">Daftar Buku</th>
                            <th class="px-4 py-3 text-center font-semibold w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($categories as $index => $category)
                            <tr :class="openRow === {{ $category->id }} ? 'bg-emerald-50/40' : 'hover:bg-slate-50'" class="transition-colors">
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        @click="openRow = openRow === {{ $category->id }} ? null : {{ $category->id }}"
                                        :class="openRow === {{ $category->id }} ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-700'"
                                        class="w-8 h-8 rounded-lg transition-colors"
                                        :aria-expanded="openRow === {{ $category->id }}"
                                        title="Lihat daftar buku"
                                    >
                                        <i class="fas" :class="openRow === {{ $category->id }} ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-medium text-slate-700">{{ $categories->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-slate-800">{{ $category->nama_kategori }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium text-xs">
                                        <i class="fas fa-book mr-1 text-[10px]"></i>
                                        {{ $category->books_count }} buku
                                    </span>
                                 </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-600 transition-colors"
                                            title="Edit Kategori"
                                        >
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button
                                            @click="$dispatch('open-confirm-delete', { url: '{{ route('categories.destroy', $category->id) }}' })"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors"
                                            title="Hapus Kategori"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                 </td>
                             </tr>

                            <tr x-show="openRow === {{ $category->id }}" x-transition.opacity.duration.150ms class="bg-slate-50/50">
                                <td colspan="5" class="px-4 py-4">
                                    @if($category->books->count())
                                        <div class="rounded-xl bg-white overflow-hidden shadow-sm border border-slate-100">
                                            <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                                    <i class="fas fa-list mr-1"></i> Daftar Buku
                                                </p>
                                            </div>
                                            <table class="w-full">
                                                <thead>
                                                    <tr class="bg-white text-slate-500 text-xs">
                                                        <th class="px-4 py-2 text-left font-medium w-12">No</th>
                                                        <th class="px-4 py-2 text-left font-medium">Judul Buku</th>
                                                     </tr>
                                                </thead>
                                                <tbody class="text-sm">
                                                    @foreach($category->books as $bookIndex => $book)
                                                        <tr class="odd:bg-white even:bg-slate-50/60 hover:bg-emerald-50/40 transition-colors">
                                                            <td class="px-4 py-2 text-slate-500">{{ $bookIndex + 1 }}</td>
                                                            <td class="px-4 py-2 text-slate-700 font-medium">{{ $book->judul }}</td>
                                                         </tr>
                                                    @endforeach
                                                </tbody>
                                             </table>
                                        </div>
                                    @else
                                        <div class="rounded-xl bg-white px-4 py-6 text-center text-sm text-slate-500 shadow-sm border border-slate-100">
                                            <i class="fas fa-book-open text-3xl mb-2 block text-slate-300"></i>
                                            <p>Belum ada buku pada kategori ini.</p>
                                        </div>
                                    @endif
                                 </td>
                             </tr>
                        @endforeach
                    </tbody>
                 </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if ($categories->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center text-slate-300 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @php
                    $start = max(1, $categories->currentPage() - 1);
                    $end   = min($categories->lastPage(), $categories->currentPage() + 1);
                @endphp

                @if($start > 1)
                    <a href="{{ $categories->url(1) }}" class="w-8 h-8 flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">1</a>
                    @if($start > 2)
                        <span class="w-8 h-8 flex items-center justify-center text-sm text-slate-400">...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $categories->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center text-sm bg-emerald-600 text-white rounded-lg font-medium">{{ $i }}</span>
                    @else
                        <a href="{{ $categories->url($i) }}" class="w-8 h-8 flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">{{ $i }}</a>
                    @endif
                @endfor

                @if($end < $categories->lastPage())
                    @if($end < $categories->lastPage() - 1)
                        <span class="w-8 h-8 flex items-center justify-center text-sm text-slate-400">...</span>
                    @endif
                    <a href="{{ $categories->url($categories->lastPage()) }}" class="w-8 h-8 flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">{{ $categories->lastPage() }}</a>
                @endif

                @if ($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center text-slate-300 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif
    @else
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-slate-200">
            <i class="fas fa-tags text-5xl mb-3 block text-slate-300"></i>
            <p class="text-base font-medium text-slate-500">Belum ada kategori</p>
            <p class="text-sm text-slate-400 mt-1">Klik tombol "Tambah Kategori" untuk membuat kategori baru</p>
        </div>
    @endif

    <!-- Modals -->
    <x-modal name="create-category" title="Tambah Kategori" maxWidth="md">
        @include('admin.categories.partials.create-form')
    </x-modal>

    @foreach($categories as $category)
        <x-modal name="edit-category-{{ $category->id }}" title="Edit Kategori" maxWidth="md">
            @include('admin.categories.partials.edit-form', ['category' => $category])
        </x-modal>
    @endforeach

    <x-confirm-delete />
@endsection