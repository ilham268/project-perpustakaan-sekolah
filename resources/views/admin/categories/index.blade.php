@extends('layouts.admin')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')

<div class="space-y-5">

    {{-- Flash Messages --}}
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

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Kategori Buku
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Kelola kategori buku dan lihat daftar buku pada setiap kategori.
            </p>
        </div>

        <button
            type="button"
            @click="$dispatch('open-modal', 'create-category')"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    {{-- Search --}}
    <form
        method="GET"
        action="{{ route('categories.index') }}"
        id="filter-form"
        class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm"
    >
        <div class="relative w-full sm:max-w-md">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                placeholder="Cari kategori..."
                autocomplete="off"
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
            >
        </div>
    </form>

    {{-- Category List --}}
    @if($categories->count())
        <div
            x-data="{ openRow: null }"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="w-20 border border-slate-200 px-5 py-4 text-center">
                                Detail
                            </th>
                            <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                                No
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Kategori
                            </th>
                            <th class="w-40 border border-slate-200 px-5 py-4 text-center">
                                Jumlah Buku
                            </th>
                            <th class="w-32 border border-slate-200 px-5 py-4 text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($categories as $index => $category)
                            <tr
                                :class="openRow === {{ $category->id }} ? 'bg-emerald-50/30' : 'hover:bg-slate-50'"
                                class="transition-colors"
                            >
                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    <button
                                        type="button"
                                        @click="openRow = openRow === {{ $category->id }} ? null : {{ $category->id }}"
                                        :class="openRow === {{ $category->id }}
                                            ? 'bg-emerald-500 text-white ring-emerald-100'
                                            : 'bg-emerald-50 text-emerald-600 ring-emerald-100 hover:bg-emerald-100'"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl ring-1 transition"
                                        :aria-expanded="openRow === {{ $category->id }}"
                                        title="Lihat daftar buku"
                                    >
                                        <i
                                            class="fas text-xs"
                                            :class="openRow === {{ $category->id }} ? 'fa-chevron-up' : 'fa-chevron-down'"
                                        ></i>
                                    </button>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $categories->firstItem() + $index }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $category->nama_kategori }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                    {{ $category->books_count }} buku
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                                            title="Edit Kategori"
                                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                        >
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-delete', { url: '{{ route('categories.destroy', $category->id) }}' })"
                                            title="Hapus Kategori"
                                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Detail Buku --}}
                            <tr
                                x-show="openRow === {{ $category->id }}"
                                x-transition.opacity.duration.150ms
                                x-cloak
                                class="bg-slate-50/50"
                            >
                                <td colspan="5" class="border border-slate-200 px-5 py-5">

                                    @if($category->books->count())
                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                            <div class="border-b border-slate-200 px-5 py-3">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                    Daftar Buku
                                                </p>
                                            </div>

                                            <div class="overflow-x-auto">
                                                <table class="w-full border-collapse text-sm">
                                                    <thead>
                                                        <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                            <th class="w-16 border border-slate-200 px-4 py-3 text-left">
                                                                No
                                                            </th>
                                                            <th class="border border-slate-200 px-4 py-3 text-left">
                                                                Judul Buku
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach($category->books as $bookIndex => $book)
                                                            <tr class="transition-colors hover:bg-slate-50">
                                                                <td class="border border-slate-200 px-4 py-3 text-slate-500">
                                                                    {{ $bookIndex + 1 }}
                                                                </td>

                                                                <td class="border border-slate-200 px-4 py-3">
                                                                    <span class="font-medium text-slate-700">
                                                                        {{ $book->judul }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-4 py-8 text-center">
                                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                                <i class="fas fa-book-open text-xl"></i>
                                            </div>

                                            <p class="mt-3 text-sm font-semibold text-slate-600">
                                                Belum ada buku pada kategori ini
                                            </p>
                                        </div>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Menampilkan {{ $categories->firstItem() ?? 0 }}&ndash;{{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} data
                </p>

                <div class="flex flex-wrap items-center gap-1">

                    @if ($categories->onFirstPage())
                        <span class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-300">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a
                            href="{{ $categories->previousPageUrl() }}"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    @php
                        $start = max(1, $categories->currentPage() - 1);
                        $end = min($categories->lastPage(), $categories->currentPage() + 1);
                    @endphp

                    @if($start > 1)
                        <a
                            href="{{ $categories->url(1) }}"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            1
                        </a>

                        @if($start > 2)
                            <span class="flex h-8 w-8 items-center justify-center text-sm text-slate-400">
                                ...
                            </span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $categories->currentPage())
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-sm font-semibold text-white">
                                {{ $i }}
                            </span>
                        @else
                            <a
                                href="{{ $categories->url($i) }}"
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                            >
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if($end < $categories->lastPage())
                        @if($end < $categories->lastPage() - 1)
                            <span class="flex h-8 w-8 items-center justify-center text-sm text-slate-400">
                                ...
                            </span>
                        @endif

                        <a
                            href="{{ $categories->url($categories->lastPage()) }}"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            {{ $categories->lastPage() }}
                        </a>
                    @endif

                    @if ($categories->hasMorePages())
                        <a
                            href="{{ $categories->nextPageUrl() }}"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    @else
                        <span class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-300">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </span>
                    @endif

                </div>
            </div>
        @endif
    @else
        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <i class="fas fa-tags text-2xl"></i>
            </div>

            <p class="mt-4 text-sm font-bold text-slate-700">
                Belum ada kategori
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Klik tombol "Tambah Kategori" untuk membuat kategori baru.
            </p>
        </div>
    @endif

</div>

{{-- Modals --}}
<x-modal name="create-category" title="Tambah Kategori" maxWidth="md">
    @include('admin.categories.partials.create-form')
</x-modal>

@foreach($categories as $category)
    <x-modal name="edit-category-{{ $category->id }}" title="Edit Kategori" maxWidth="md">
        @include('admin.categories.partials.edit-form', ['category' => $category])
    </x-modal>
@endforeach

<x-confirm-delete />

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var debounceTimer;

        if (!form || !searchInput) {
            return;
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                form.submit();
            }, 400);
        });
    })();
</script>

@endsection