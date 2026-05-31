@extends('layouts.petugas')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Peminjaman')

@section('content')

<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('error') }}
            </span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ $errors->first() }}
            </span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                Data Peminjaman
            </p>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                Daftar Peminjaman
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                Kelola pengajuan, tanggal kembali, dan status peminjaman buku.
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route('peminjaman.index') }}"
        id="filter-form"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
    >
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
            <div class="relative lg:col-span-8">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari peminjam, judul, kode buku..."
                    autocomplete="off"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="lg:col-span-3">
                <select
                    name="status"
                    id="status-select"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                    href="{{ route('peminjaman.index') }}"
                    class="inline-flex h-11 w-full items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Total Peminjaman
            </p>

            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                {{ number_format($totalPeminjaman) }}
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Menunggu Persetujuan
            </p>

            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                {{ number_format($totalPending) }}
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">
                Disetujui
            </p>

            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                {{ number_format($totalDisetujui) }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-white px-5 py-4">
            <h2 class="text-lg font-extrabold text-slate-900">
                Data Peminjaman
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Tanggal kembali bisa diedit langsung dari tabel.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1280px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>

                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Peminjam
                        </th>

                        <th class="w-40 border border-slate-200 px-5 py-4 text-left">
                            Nomor Identitas
                        </th>

                        <th class="w-32 border border-slate-200 px-5 py-4 text-center">
                            Status
                        </th>

                        <th class="w-36 border border-slate-200 px-5 py-4 text-center">
                            Tgl Pinjam
                        </th>

                        <th class="w-64 border border-slate-200 px-5 py-4 text-center">
                            Tgl Kembali
                        </th>

                        <th class="w-48 border border-slate-200 px-5 py-4 text-center">
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

                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $loans->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[280px] truncate font-semibold text-slate-800">
                                    {{ $loan->bookItem->book->judul ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="block max-w-[220px] truncate font-semibold text-slate-800">
                                    {{ $loan->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $loan->user->nomor_identitas ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($loan->status == 'pending')
                                    <span class="font-semibold text-amber-600">
                                        Pending
                                    </span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="font-semibold text-emerald-600">
                                        Disetujui
                                    </span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="font-semibold text-red-600">
                                        Ditolak
                                    </span>
                                @elseif($loan->status == 'dikembalikan')
                                    <span class="font-semibold text-blue-600">
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="font-semibold text-slate-500">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ $tanggalPinjamText }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($bolehEditTanggal)
                                    <form
                                        action="{{ route('peminjaman.update-tanggal-kembali', $loan->id) }}"
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
                                            class="h-10 w-40 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-emerald-300 focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                        >

                                        <button
                                            type="submit"
                                            title="Simpan Tanggal Kembali"
                                            class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                        >
                                            Simpan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-500">
                                        {{ $tanggalKembaliText }}
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($loan->status == 'pending')
                                        <form action="{{ route('peminjaman.approve', $loan->id) }}" method="POST" class="inline">
                                            @csrf

                                            <button
                                                type="submit"
                                                title="Setujui"
                                                onclick="return confirm('Setujui peminjaman ini?')"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                            >
                                                Setujui
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            onclick="openRejectModal({{ $loan->id }})"
                                            title="Tolak"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                        >
                                            Tolak
                                        </button>
                                    @elseif($loan->status == 'disetujui')
                                        <a
                                            href="{{ route('peminjaman.download-kartu', $loan->id) }}"
                                            title="Download Kartu"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                                        >
                                            Download
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">
                                            —
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-slate-200 px-6 py-16 text-center">
                                <p class="text-sm font-bold text-slate-700">
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
        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Menampilkan
                    <span class="font-semibold text-slate-700">{{ $loans->firstItem() }}</span>&ndash;<span class="font-semibold text-slate-700">{{ $loans->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-slate-700">{{ $loans->total() }}</span>
                    data
                </p>

                <div class="flex flex-wrap items-center gap-1">
                    @if($loans->onFirstPage())
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
                            Prev
                        </span>
                    @else
                        <a
                            href="{{ $loans->previousPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >
                            Prev
                        </a>
                    @endif

                    @foreach($loans->getUrlRange(1, $loans->lastPage()) as $page => $url)
                        @if($page == $loans->currentPage())
                            <span class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-emerald-600 px-3 text-sm font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a
                                href="{{ $url }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                            >
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if($loans->hasMorePages())
                        <a
                            href="{{ $loans->nextPageUrl() }}"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >
                            Next
                        </a>
                    @else
                        <span class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-300">
                            Next
                        </span>
                    @endif
                </div>
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

        <form id="rejectForm" method="POST" action="">
            @csrf

            <label class="mb-1 block text-sm font-semibold text-slate-700">
                Alasan Penolakan <span class="text-red-500">*</span>
            </label>

            <textarea
                name="alasan_ditolak"
                rows="3"
                required
                placeholder="Masukkan alasan penolakan..."
                class="mb-1 w-full rounded-lg border px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100 {{ $errors->has('alasan_ditolak') ? 'border-red-500' : 'border-slate-200' }}"
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
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
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

<script>
    (function () {
        var form = document.getElementById('filter-form');
        var searchInput = document.getElementById('search-input');
        var statusSelect = document.getElementById('status-select');
        var debounceTimer;

        if (!form) {
            return;
        }

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
        document.getElementById('rejectForm').action = `{{ url('/peminjaman') }}/${id}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }

    @if($errors->has('alasan_ditolak') && session('reject_loan_id'))
        document.getElementById('rejectForm').action = `{{ url('/peminjaman') }}/{{ session('reject_loan_id') }}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    @endif
</script>

@endsection