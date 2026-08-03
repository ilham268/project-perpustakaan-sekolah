@extends('layouts.admin')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Peminjaman')

@section('content')

<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-check-circle"></i>
                </div>

                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>

                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                </div>

                <span>{{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Data&nbsp;Peminjaman
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Daftar Peminjaman
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Lama pinjam default: {{ $lamaPinjamDefault ?? 7 }} hari. Tanggal kembali bisa diedit langsung di tabel.
                </p>
            </div>

            <button
                type="button"
                onclick="openExportModal()"
                class="inline-flex h-10 w-fit shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                <i class="fas fa-file-export text-xs"></i>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route('admin.peminjaman.index') }}"
        id="filter-form"
        class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm"
    >
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
            <div class="relative lg:col-span-8">
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari judul buku atau nama peminjam..."
                    autocomplete="off"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            <div class="lg:col-span-3">
                <select
                    name="status"
                    id="status-select"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    <option value="" {{ !request('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <a
                    href="{{ route('admin.peminjaman.index') }}"
                    title="Reset Filter"
                    class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Total Peminjaman
            </p>

            <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight text-[var(--text)]">
                {{ number_format($totalPeminjaman) }}
            </p>
        </div>

        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Menunggu Persetujuan
            </p>

            <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight text-[var(--text)]">
                {{ number_format($totalPending) }}
            </p>
        </div>

        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[var(--muted)]">
                Disetujui
            </p>

            <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight text-[var(--text)]">
                {{ number_format($totalDisetujui) }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="border-b border-[var(--hairline)] px-5 py-4">
            <div class="flex flex-col gap-1">
                <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                    Data Peminjaman
                </h2>

                <p class="text-sm text-[var(--muted)]">
                    Kelola pengajuan peminjaman, status, dan tanggal kembali.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1280px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Judul Buku
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Kode Buku
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Peminjam
                        </th>

                        <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                            Nomor Identitas
                        </th>

                        <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Status
                        </th>

                        <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Tgl Pinjam
                        </th>

                        <th class="w-64 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Tgl Kembali
                        </th>

                        <th class="w-48 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($loans as $loan)
                        @php
                            $tanggalPinjamText = $loan->tanggal_pinjam ? \Carbon\Carbon::parse($loan->tanggal_pinjam)->translatedFormat('d M Y') : '-';
                            $tanggalKembaliText = $loan->tanggal_kembali ? \Carbon\Carbon::parse($loan->tanggal_kembali)->translatedFormat('d M Y') : '-';
                            $tanggalKembaliInput = $loan->tanggal_kembali ? \Carbon\Carbon::parse($loan->tanggal_kembali)->format('Y-m-d') : '';
                            $bolehEditTanggal = !in_array($loan->status, ['ditolak', 'dikembalikan']);
                        @endphp

                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                {{ $loans->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[280px] truncate font-semibold text-[var(--text)]">
                                    {{ $loan->bookItem->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-[var(--text)]">
                                    {{ $loan->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                {{ $loan->user->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($loan->status == 'pending')
                                    <span class="font-semibold text-amber-600">
                                        Pending
                                    </span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="font-semibold text-[var(--emerald-deep)]">
                                        Disetujui
                                    </span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="font-semibold text-red-600">
                                        Ditolak
                                    </span>
                                @elseif($loan->status == 'dikembalikan')
                                    <span class="font-semibold text-sky-600">
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="font-semibold text-[var(--muted)]">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                {{ $tanggalPinjamText }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                @if($bolehEditTanggal)
                                    <form
                                        action="{{ route('admin.peminjaman.update-tanggal-kembali', $loan->id) }}"
                                        method="POST"
                                        class="flex items-center justify-center gap-2"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <input
                                            type="date"
                                            name="tanggal_kembali"
                                            value="{{ $tanggalKembaliInput }}"
                                            required
                                            title="{{ $tanggalKembaliText }}"
                                            class="h-10 w-40 rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)] transition hover:border-sky-300 focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                        >

                                        <button
                                            type="submit"
                                            title="Simpan Tanggal Kembali"
                                            class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-sky-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                        >
                                            Simpan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[var(--muted)]">
                                        {{ $tanggalKembaliText }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($loan->status == 'pending')
                                        <form action="{{ route('admin.peminjaman.approve', $loan->id) }}" method="POST" class="inline">
                                            @csrf

                                            <button
                                                type="submit"
                                                title="Setujui"
                                                onclick="return confirm('Setujui peminjaman ini?')"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                            >
                                                Setujui
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            onclick="openRejectModal({{ $loan->id }})"
                                            title="Tolak"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                        >
                                            Tolak
                                        </button>
                                    @elseif($loan->status == 'disetujui')
                                        <a
                                            href="{{ route('admin.peminjaman.download-kartu', $loan->id) }}"
                                            title="Download Kartu"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        >
                                            Download
                                        </a>
                                    @else
                                        <span class="text-xs text-[var(--muted)]">
                                            —
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                                    <i class="fas fa-book-open text-2xl"></i>
                                </div>

                                <p class="font-display mt-4 text-sm font-semibold text-[var(--text)]">
                                    Tidak ada data peminjaman
                                </p>

                                <p class="mt-1 text-xs text-[var(--muted)]">
                                    Belum ada pengajuan peminjaman buku.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($loans->total() > 0)
        <div class="rounded-2xl border border-[var(--hairline)] bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-center text-sm text-[var(--muted)] sm:text-left">
                    Menampilkan
                    <span class="font-semibold text-[var(--text)]">{{ $loans->firstItem() }}</span>&ndash;<span class="font-semibold text-[var(--text)]">{{ $loans->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-[var(--text)]">{{ $loans->total() }}</span>
                    data
                </p>

                <div class="flex flex-wrap items-center justify-center gap-1 sm:justify-end">
                    @if($loans->onFirstPage())
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-slate-300">
                            Prev
                        </span>
                    @else
                        <a
                            href="{{ $loans->previousPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                        >
                            Prev
                        </a>
                    @endif

                    @foreach($loans->getUrlRange(1, $loans->lastPage()) as $page => $url)
                        @if($page == $loans->currentPage())
                            <span class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg bg-[var(--emerald-deep)] px-3 text-sm font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a
                                href="{{ $url }}"
                                class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                            >
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if($loans->hasMorePages())
                        <a
                            href="{{ $loans->nextPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60"
                        >
                            Next
                        </a>
                    @else
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-sm text-slate-300">
                            Next
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[var(--ink)]/70 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5">
            <h3 class="font-display text-lg font-semibold text-[var(--forest)]">
                Tolak Peminjaman
            </h3>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Masukkan alasan penolakan pengajuan peminjaman.
            </p>
        </div>

        <form id="rejectForm" method="POST" action="">
            @csrf

            <label class="mb-1 block text-sm font-semibold text-[var(--text)]">
                Alasan Penolakan <span class="text-red-500">*</span>
            </label>

            <textarea
                name="alasan_ditolak"
                rows="3"
                required
                placeholder="Masukkan alasan penolakan..."
                class="mb-1 w-full rounded-lg border px-3 py-2 text-sm text-[var(--text)] outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100 {{ $errors->has('alasan_ditolak') ? 'border-red-500' : 'border-[var(--hairline)]' }}"
            >{{ old('alasan_ditolak') }}</textarea>

            @error('alasan_ditolak')
                <p class="mb-3 text-xs font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                <button
                    type="button"
                    onclick="closeRejectModal()"
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/50 focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-lg bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
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
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var statusSelect = document.getElementById('status-select');
        var debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 400);
            });
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', function () {
                form.submit();
            });
        }
    })();

    function openRejectModal(id) {
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