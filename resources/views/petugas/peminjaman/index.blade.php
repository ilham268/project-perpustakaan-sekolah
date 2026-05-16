@extends('layouts.petugas')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Peminjaman')

@section('content')

<div class="space-y-5">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Alert Error --}}
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

    {{-- Page Header --}}
    <div class="flex flex-col gap-1">
        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Daftar Peminjaman
        </h3>
        <p class="text-sm text-slate-500">
            Kelola data pengajuan dan status peminjaman buku.
        </p>
    </div>

    {{-- Search & Filter --}}
    <form
        method="GET"
        action="{{ route('peminjaman.index') }}"
        id="filter-form"
        class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm"
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div class="relative w-full lg:max-w-md">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari peminjam, judul, kode buku..."
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <span class="text-sm font-semibold text-slate-600">
                    Status:
                </span>

                <select
                    name="status"
                    id="status-select"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                >
                    <option value="" {{ !request('status') ? 'selected' : '' }}>Semua</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>

        </div>
    </form>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Baru --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-rose-50 transition group-hover:bg-rose-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Baru
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalPeminjaman }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 ring-1 ring-rose-100">
                    <i class="fas fa-book-open text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Proses --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Proses
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalPending }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 ring-1 ring-amber-100">
                    <i class="fas fa-gear text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Selesai
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalDisetujui }}
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-circle-check text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="text-base font-bold text-slate-900">
                Data Peminjaman
            </h3>
            <p class="mt-1 text-xs text-slate-500">
                Daftar seluruh pengajuan peminjaman buku.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1080px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Peminjam
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nomor Identitas
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Status
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tgl Pinjam
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tgl Tempo
                        </th>
                        <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($loans as $loan)
                        <tr class="transition-colors hover:bg-slate-50">

                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $loans->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $loan->bookItem->book->judul }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $loan->bookItem->kode_buku }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $loan->user->name }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $loan->user->nomor_identitas }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($loan->status == 'pending')
                                    <span class="inline-flex items-center justify-center rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700 ring-1 ring-yellow-100">
                                        Pending
                                    </span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="inline-flex items-center justify-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                                        Disetujui
                                    </span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="inline-flex items-center justify-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-100">
                                        Ditolak
                                    </span>
                                @elseif($loan->status == 'dikembalikan')
                                    <span class="inline-flex items-center justify-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-green-100">
                                        Dikembalikan
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">

                                    @if($loan->status == 'pending')
                                        <form action="{{ route('peminjaman.approve', $loan->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                title="Setujui"
                                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-green-50 text-green-600 ring-1 ring-green-100 transition hover:bg-green-100"
                                            >
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            onclick="openRejectModal({{ $loan->id }}, @json($loan->bookItem->book->judul), @json($loan->user->name))"
                                            title="Tolak"
                                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                        >
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    @elseif($loan->status == 'disetujui')
                                        <a
                                            href="{{ route('peminjaman.download-kartu', $loan->id) }}"
                                            title="Download Kartu"
                                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 ring-1 ring-cyan-100 transition hover:bg-cyan-100"
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
                            <td colspan="9" class="border border-slate-200 px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-inbox text-2xl"></i>
                                </div>

                                <p class="mt-4 text-sm font-bold text-slate-700">
                                    Tidak ada data peminjaman
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Data akan muncul setelah ada pengajuan peminjaman.
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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $loans->firstItem() }}&ndash;{{ $loans->lastItem() }} dari {{ $loans->total() }} data
            </p>

            <div class="flex flex-wrap items-center gap-1">

                @if($loans->onFirstPage())
                    <span class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-300">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a
                        href="{{ $loans->previousPageUrl() }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                    >
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($loans->getUrlRange(1, $loans->lastPage()) as $page => $url)
                    @if($page == $loans->currentPage())
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-sm font-semibold text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $url }}"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if($loans->hasMorePages())
                    <a
                        href="{{ $loans->nextPageUrl() }}"
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

</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-slate-900">
                Tolak Pengajuan
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Masukkan alasan penolakan pengajuan peminjaman.
            </p>
        </div>

        <form
            id="rejectForm"
            method="POST"
            action="{{ session('reject_loan_id') ? route('peminjaman.reject', session('reject_loan_id')) : '' }}"
        >
            @csrf

            <label class="mb-1 block text-sm font-semibold text-slate-700">
                Alasan Penolakan <span class="text-red-500">*</span>
            </label>

            <textarea
                name="alasan_ditolak"
                rows="3"
                required
                placeholder="Masukkan alasan penolakan..."
                class="mb-1 w-full rounded-xl border px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100 {{ $errors->has('alasan_ditolak') ? 'border-red-500' : 'border-slate-200' }}"
            >{{ old('alasan_ditolak') }}</textarea>

            @error('alasan_ditolak')
                <p class="mb-3 text-xs font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <div class="mt-4 flex gap-3">
                <button
                    type="button"
                    onclick="closeRejectModal()"
                    class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-600"
                >
                    Tolak Pengajuan
                </button>
            </div>
        </form>

    </div>
</div>

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