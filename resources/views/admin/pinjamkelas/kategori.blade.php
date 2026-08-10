@extends('layouts.admin')

@section('title', 'Buku Paket')
@section('page-title', 'Buku Paket')

@section('content')

@php
    $paketFilter = function ($q) {
        $q->where('jenis_koleksi', 'like', '%Paket%')
            ->orWhere('jenis_koleksi', 'like', '%Packet%')
            ->orWhere('jenis_koleksi', 'like', '%Pakett%')
            ->orWhere('jenis_koleksi', 'like', '%PKT%');
    };

    $tahunOptions = \App\Models\Book::query()
        ->where($paketFilter)
        ->whereNotNull('tahun_pengadaan')
        ->distinct()
        ->orderByDesc('tahun_pengadaan')
        ->pluck('tahun_pengadaan');

    $booksPaketQuery = \App\Models\Book::query()
        ->withCount([
            'bookItems as total_eksemplar',
            'bookItems as kode_terisi' => function ($q) {
                $q->whereNotNull('kode_buku')
                    ->where('kode_buku', '!=', '');
            },
            'bookItems as stok_tersedia' => function ($q) {
                $q->where('status', 'available')
                    ->whereNotNull('kode_buku')
                    ->where('kode_buku', '!=', '');
            },
        ])
        ->where($paketFilter);

    if (request('search')) {
        $search = request('search');

        $booksPaketQuery->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
                ->orWhere('penulis', 'like', "%{$search}%")
                ->orWhere('penerbit', 'like', "%{$search}%")
                ->orWhere('nomor_klasifikasi', 'like', "%{$search}%")
                ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
        });
    }

    if (request('tahun_pengadaan')) {
        $booksPaketQuery->where('tahun_pengadaan', 'like', '%' . request('tahun_pengadaan') . '%');
    }

    // AMBIL SEMUA DATA UNTUK DIGABUNGKAN DULU
    $allBooks = $booksPaketQuery
        ->orderByDesc('tahun_pengadaan')
        ->orderBy('judul')
        ->get();

    // LOGIKA PENGGABUNGAN (GROUPING) BERDASARKAN JUDUL BUKU
    $groupedBooks = $allBooks->groupBy(function($item) {
        return strtolower(trim($item->judul));
    })->map(function($group) {
        // Ambil data buku pertama sebagai template
        $first = $group->first();
        
        // Kumpulkan semua tahun pengadaan dari buku yang sama
        $years = [];
        foreach($group as $b) {
            if($b->tahun_pengadaan) {
                $parts = explode(',', $b->tahun_pengadaan);
                foreach($parts as $p) {
                    $years[] = trim($p);
                }
            }
        }
        $years = array_unique(array_filter($years));
        rsort($years);
        
        // Timpa nilai aslinya dengan nilai hasil gabungan
        $first->tahun_pengadaan = implode(', ', $years);
        $first->total_eksemplar = $group->sum('total_eksemplar');
        $first->kode_terisi = $group->sum('kode_terisi');
        $first->stok_tersedia = $group->sum('stok_tersedia');

        // Simpan semua id buku dalam grup ini, dipakai kalau nanti mau digabung manual
        // dengan grup lain (misal judulnya beda tipis karena typo/spasi ganda).
        $first->all_ids = $group->pluck('id')->implode(',');
        
        return $first;
    })->values();

    // MEMBUAT PAGINASI MANUAL KARENA DATA SUDAH DIGABUNG
    $perPage = 10;
    $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
    $currentPageItems = $groupedBooks->slice(($currentPage - 1) * $perPage, $perPage)->all();
    
    $booksPaket = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentPageItems, 
        $groupedBooks->count(), 
        $perPage, 
        $currentPage, 
        [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 
            'query' => request()->query()
        ]
    );

    // HITUNGAN TOTAL (Untuk Kartu di Atas)
    $totalPaket = $groupedBooks->count(); // Total judul unik setelah digabung
    $totalEksemplar = $groupedBooks->sum('total_eksemplar');
    $totalKodeTerisi = $groupedBooks->sum('kode_terisi');
@endphp

<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--emerald-tint)] px-4 py-3 text-sm text-[var(--emerald-deep)] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                    <i class="fas fa-check"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-red-600 ring-1 ring-red-100">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">Koleksi&nbsp;Buku&nbsp;Paket</p>
                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">Buku Paket</h1>
                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">Daftar Buku Paket untuk peminjaman siswa. Buku Paket tidak lagi dipisah berdasarkan kelas atau jurusan.</p>
            </div>

            <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-3 lg:w-[520px]">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                    <p class="catalog-eyebrow uppercase text-white/70">Total Judul</p>
                    <p class="font-mono-stat mt-1 text-2xl font-semibold tracking-tight text-white">{{ $totalPaket }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                    <p class="catalog-eyebrow uppercase text-white/70">Total Eksemplar</p>
                    <p class="font-mono-stat mt-1 text-2xl font-semibold tracking-tight text-white">{{ $totalEksemplar }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                    <p class="catalog-eyebrow uppercase text-white/70">Kode Terisi</p>
                    <p class="font-mono-stat mt-1 text-2xl font-semibold tracking-tight text-white">{{ $totalKodeTerisi }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
        <div class="flex items-start gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sky-600 ring-1 ring-sky-100">
                <i class="fas fa-code-merge text-sm"></i>
            </div>

            <p class="text-sm leading-relaxed text-sky-800">
                Kalau ada 2 buku yang sebenarnya sama tapi belum otomatis tergabung (misal karena judulnya beda tipis), centang buku-buku itu lalu klik <strong>"Gabungkan Terpilih"</strong> di bawah.
            </p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <form id="merge-form" action="{{ route('books.merge') }}" method="POST">
            @csrf
            <input type="hidden" name="target_id" id="merge-target-id" value="">
            <div id="merge-book-ids-container"></div>
        </form>

        <div class="border-b border-[var(--hairline)] px-5 py-5 md:px-6">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex flex-col gap-1">
                    <h2 class="font-display text-lg font-semibold text-[var(--forest)] md:text-xl">Daftar Buku Paket</h2>
                    <p class="text-sm text-[var(--muted)]">Admin input kode buku dari menu detail buku, lalu kode ditempel pada buku fisik.</p>
                </div>

                <button
                    type="button"
                    id="open-merge-modal"
                    disabled
                    class="inline-flex h-10 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-sky-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-100 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400"
                >
                    <i class="fas fa-code-merge text-xs"></i>
                    <span>Gabungkan Terpilih (<span id="selected-merge-count" class="font-mono-stat">0</span>)</span>
                </button>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ url()->current() }}" id="filter-form" class="mt-5">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="relative md:col-span-6">
                        <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>
                        <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Cari judul, penulis, penerbit, klasifikasi..." autocomplete="off" class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]">
                    </div>

                    <div class="md:col-span-3">
                        <select name="tahun_pengadaan" id="tahun-select" class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]">
                            <option value="">Semua Tahun</option>
                            @php
                                $cleanTahun = [];
                                foreach($tahunOptions as $thn) {
                                    if($thn) {
                                        $parts = explode(',', $thn);
                                        foreach($parts as $p) $cleanTahun[] = trim($p);
                                    }
                                }
                                $cleanTahun = array_unique(array_filter($cleanTahun));
                                rsort($cleanTahun);
                            @endphp
                            @foreach($cleanTahun as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_pengadaan') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:col-span-3">
                        <button type="submit" class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]">Filter</button>
                        <a href="{{ url()->current() }}" class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1150px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-12 border border-[var(--hairline)] px-4 py-4 text-center">
                            <input type="checkbox" id="select-all-merge" class="h-4 w-4 rounded border-[var(--hairline)] text-sky-600 focus:ring-sky-500">
                        </th>
                        <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">No</th>
                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">Judul Buku</th>
                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Tahun Data</th>
                        <th class="w-48 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">Penulis</th>
                        <th class="w-48 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">Penerbit</th>
                        <th class="w-24 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Eksemplar</th>
                        <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Kode</th>
                        <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Tersedia</th>
                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($booksPaket as $index => $book)
                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                <input
                                    type="checkbox"
                                    class="merge-checkbox h-4 w-4 rounded border-[var(--hairline)] text-sky-600 focus:ring-sky-500"
                                    data-ids="{{ $book->all_ids }}"
                                    data-judul="{{ $book->judul }}"
                                    data-eksemplar="{{ $book->total_eksemplar }}"
                                >
                            </td>
                            <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                {{ $booksPaket->firstItem() + $index }}
                            </td>
                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <div class="max-w-[340px] font-semibold text-[var(--text)]">{{ $book->judul }}</div>
                                <div class="mt-1 text-xs text-[var(--muted)]">No. Klasifikasi: {{ $book->nomor_klasifikasi ?? '-' }}</div>
                            </td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($book->tahun_pengadaan)
                                    @php
                                        $tahunArray = array_map('trim', explode(',', $book->tahun_pengadaan));
                                        $tahunArray = array_unique(array_filter($tahunArray));
                                        sort($tahunArray);
                                    @endphp
                                    @if(count($tahunArray) > 1)
                                        <div title="Buku dari pengadaan tahun: {{ implode(', ', $tahunArray) }}">
                                            <p class="catalog-eyebrow font-semibold uppercase text-[var(--emerald-deep)]">Gabungan</p>
                                            <p class="font-mono-stat mt-1 text-xs text-[var(--muted)]">{{ implode(', ', $tahunArray) }}</p>
                                        </div>
                                    @else
                                        <span class="font-mono-stat text-sm font-medium text-[var(--text)]/80">{{ $tahunArray[0] ?? '-' }}</span>
                                    @endif
                                @else
                                    <span class="text-[var(--muted)]">-</span>
                                @endif
                            </td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">{{ $book->penulis ?? '-' }}</td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">{{ $book->penerbit ?? '-' }}</td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                <span class="font-mono-stat font-semibold text-[var(--text)]/80">{{ $book->total_eksemplar }}</span>
                            </td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($book->kode_terisi == $book->total_eksemplar && $book->total_eksemplar > 0)
                                    <span class="font-mono-stat font-semibold text-[var(--emerald-deep)]">{{ $book->kode_terisi }}/{{ $book->total_eksemplar }}</span>
                                @else
                                    <span class="font-mono-stat font-semibold text-amber-600">{{ $book->kode_terisi }}/{{ $book->total_eksemplar }}</span>
                                @endif
                            </td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                <span class="font-mono-stat font-semibold text-[var(--emerald-deep)]">{{ $book->stok_tersedia }}</span>
                            </td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                <a href="{{ route('books.show', $book->id) }}" class="inline-flex h-9 items-center justify-center whitespace-nowrap rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]">
                                    Input Kode
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]"><i class="fas fa-book-open text-2xl"></i></div>
                                <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">Belum ada Buku Paket</p>
                                <p class="mt-1 text-sm text-[var(--muted)]">Import Excel BOS dulu, lalu pastikan sheet Paket sudah terbaca.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-[var(--hairline)] px-5 py-4">
            {{ $booksPaket->links() }}
        </div>
    </div>
</div>

{{-- Modal: Gabungkan Buku Manual --}}
<div id="merge-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-[var(--ink)]/50" id="merge-modal-overlay"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-[var(--hairline)] px-5 py-4">
                <div>
                    <h4 class="font-display text-base font-semibold text-[var(--forest)]">Gabungkan Buku</h4>
                    <p class="mt-1 text-xs text-[var(--muted)]">
                        Pilih salah satu sebagai buku utama. Buku lain akan dipindahkan eksemplarnya ke buku utama, lalu dihapus.
                    </p>
                </div>

                <button type="button" id="close-merge-modal" class="flex h-8 w-8 items-center justify-center rounded-lg text-[var(--muted)] transition hover:bg-[var(--sand)]/60">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="max-h-80 space-y-2 overflow-y-auto p-5" id="merge-modal-list"></div>

            <div class="mx-5 mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                <p class="text-xs leading-relaxed text-amber-800">
                    <i class="fas fa-triangle-exclamation"></i>
                    Kalau ada kode buku yang sama persis di kedua buku, penggabungan akan ditolak. Ganti dulu salah satu kode itu sebelum digabung.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-[var(--hairline)] px-5 py-4">
                <button type="button" id="cancel-merge" class="rounded-xl border border-[var(--hairline)] bg-white px-4 py-2 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/50">
                    Batal
                </button>

                <button type="button" id="submit-merge" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-700">
                    Gabungkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filter-form');
    const tahunSelect = document.getElementById('tahun-select');
    const searchInput = document.getElementById('search-input');

    if (tahunSelect) {
        tahunSelect.addEventListener('change', function () { form.submit(); });
    }
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });
    }

    // ---------- Gabung manual ----------
    const selectAll = document.getElementById('select-all-merge');
    const checkboxes = Array.prototype.slice.call(document.querySelectorAll('.merge-checkbox'));
    const openMergeBtn = document.getElementById('open-merge-modal');
    const selectedCountEl = document.getElementById('selected-merge-count');

    const mergeModal = document.getElementById('merge-modal');
    const mergeModalList = document.getElementById('merge-modal-list');
    const closeMergeModal = document.getElementById('close-merge-modal');
    const cancelMerge = document.getElementById('cancel-merge');
    const mergeOverlay = document.getElementById('merge-modal-overlay');
    const submitMergeBtn = document.getElementById('submit-merge');

    const mergeForm = document.getElementById('merge-form');
    const mergeTargetInput = document.getElementById('merge-target-id');
    const mergeIdsContainer = document.getElementById('merge-book-ids-container');

    function updateSelectedCount() {
        const checked = checkboxes.filter(function (c) { return c.checked; });
        selectedCountEl.textContent = checked.length;
        openMergeBtn.disabled = checked.length < 2;

        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
            selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (c) { c.checked = selectAll.checked; });
            updateSelectedCount();
        });
    }

    checkboxes.forEach(function (c) {
        c.addEventListener('change', updateSelectedCount);
    });

    function toggleMergeModal(show) {
        mergeModal.classList.toggle('hidden', !show);
        document.body.classList.toggle('overflow-hidden', show);
    }

    function openModal() {
        const checked = checkboxes.filter(function (c) { return c.checked; });

        if (checked.length < 2) {
            return;
        }

        mergeModalList.innerHTML = '';

        checked.forEach(function (c, index) {
            const wrapper = document.createElement('label');
            wrapper.className = 'flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-sky-300 hover:bg-sky-50';

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'merge-target-choice';
            radio.value = c.getAttribute('data-ids');
            radio.className = 'mt-1 h-4 w-4 shrink-0 text-sky-600 focus:ring-sky-500';
            if (index === 0) radio.checked = true;

            const textWrap = document.createElement('div');
            textWrap.className = 'min-w-0 flex-1';

            const title = document.createElement('p');
            title.className = 'text-sm font-semibold text-slate-800';
            title.textContent = c.getAttribute('data-judul');

            const meta = document.createElement('p');
            meta.className = 'mt-1 text-xs text-slate-500';
            meta.textContent = c.getAttribute('data-eksemplar') + ' eksemplar';

            textWrap.appendChild(title);
            textWrap.appendChild(meta);
            wrapper.appendChild(radio);
            wrapper.appendChild(textWrap);
            mergeModalList.appendChild(wrapper);
        });

        toggleMergeModal(true);
    }

    if (openMergeBtn) {
        openMergeBtn.addEventListener('click', openModal);
    }

    [closeMergeModal, cancelMerge, mergeOverlay].forEach(function (el) {
        if (!el) return;
        el.addEventListener('click', function () { toggleMergeModal(false); });
    });

    if (submitMergeBtn) {
        submitMergeBtn.addEventListener('click', function () {
            const checked = checkboxes.filter(function (c) { return c.checked; });
            const selectedTarget = mergeModalList.querySelector('input[name="merge-target-choice"]:checked');

            if (!selectedTarget) {
                alert('Pilih salah satu buku sebagai buku utama.');
                return;
            }

            if (!confirm('Yakin ingin menggabungkan ' + checked.length + ' buku ini? Tindakan ini tidak bisa dibatalkan.')) {
                return;
            }

            // Kumpulkan SEMUA book_id dari semua grup yang dicentang.
            let allIds = [];
            checked.forEach(function (c) {
                const ids = c.getAttribute('data-ids').split(',').map(function (v) { return v.trim(); });
                allIds = allIds.concat(ids);
            });
            allIds = Array.from(new Set(allIds));

            // Target id: ambil id pertama dari grup yang dipilih sebagai buku utama.
            const targetIds = selectedTarget.value.split(',').map(function (v) { return v.trim(); });
            const targetId = targetIds[0];

            mergeIdsContainer.innerHTML = '';
            allIds.forEach(function (id) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'book_ids[]';
                input.value = id;
                mergeIdsContainer.appendChild(input);
            });

            mergeTargetInput.value = targetId;

            mergeForm.submit();
        });
    }

    updateSelectedCount();
});
</script>
@endsection