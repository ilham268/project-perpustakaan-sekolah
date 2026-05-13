@extends('layouts.petugas')

@section('title', 'Kelola Denda')
@section('page-title', 'Kelola Denda')

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

<div class="flex items-center justify-between mb-5">
    <h3 class="text-2xl font-bold text-gray-900">Kelola Denda</h3>
</div>

<form method="GET" action="{{ route('denda.index') }}" id="filter-form" class="flex items-center justify-between mb-5 gap-4">
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
            <option value="" {{ !request('status') || request('status') == 'all' ? 'selected' : '' }}>Semua</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
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

<div class="grid grid-cols-1 gap-5 mb-5 md:grid-cols-3">
    <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
        <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
        <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
        <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>

        <div class="relative z-10 flex h-full items-center justify-between gap-5">
            <div class="pr-4">
                <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Total Denda</p>
                <p class="text-[18px] font-bold leading-tight text-white">
                    Rp {{ number_format($totalDenda, 0, ',', '.') }}
                </p>
            </div>

            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                <i class="fas fa-sack-dollar text-[18px]" style="color: #1799c9;"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
        <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
        <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
        <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>

        <div class="relative z-10 flex h-full items-center justify-between gap-5">
            <div class="pr-4">
                <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Belum Dibayar</p>
                <p class="text-[18px] font-bold leading-tight text-white">
                    Rp {{ number_format($totalPending, 0, ',', '.') }}
                </p>
            </div>

            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                <i class="fas fa-wallet text-[18px]" style="color: #1799c9;"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
        <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
        <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
        <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>

        <div class="relative z-10 flex h-full items-center justify-between gap-5">
            <div class="pr-4">
                <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Sudah Dibayar</p>
                <p class="text-[18px] font-bold leading-tight text-white">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </p>
            </div>

            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                <i class="fas fa-money-check-dollar text-[18px]" style="color: #1799c9;"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px]">
            <thead>
                <tr class="bg-cyan-500 text-white text-sm">
                    <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                    <th class="px-5 py-3 text-left font-semibold">Judul / Kategori</th>
                    <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                    <th class="px-5 py-3 text-center font-semibold">Sumber</th>
                    <th class="px-5 py-3 text-center font-semibold">Kondisi</th>
                    <th class="px-5 py-3 text-center font-semibold">Denda</th>
                    <th class="px-5 py-3 text-center font-semibold">Status</th>
                    <th class="px-5 py-3 text-center font-semibold">Tanggal</th>
                    <th class="px-5 py-3 text-center font-semibold w-20">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($denda as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm text-gray-500">
                            {{ $denda->firstItem() + $loop->index }}
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if(($item->tipe ?? '') == 'buku' && !empty($item->foto))
                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                         class="w-9 h-12 object-cover rounded shrink-0 border border-gray-200">
                                @else
                                    <div class="w-9 h-12 bg-gray-100 rounded shrink-0 border border-gray-200 flex items-center justify-center">
                                        @if(($item->tipe ?? '') == 'kelas')
                                            <i class="fas fa-users text-gray-300 text-xs"></i>
                                        @else
                                            <i class="fas fa-book text-gray-300 text-xs"></i>
                                        @endif
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate max-w-48">
                                        {{ $item->judul ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $item->kode_buku ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $item->nama_peminjam ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $item->nomor_identitas ?? '-' }}
                            </p>
                        </td>

                        <td class="px-5 py-4 text-center">
                            @if(($item->tipe ?? '') == 'kelas')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                    Pinjam Kelas
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                    Pinjam Buku
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center">
                            @if(($item->kondisi ?? '') == 'baik')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Baik</span>
                            @elseif(($item->kondisi ?? '') == 'rusak')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700">Rusak</span>
                            @elseif(($item->kondisi ?? '') == 'hilang')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Hilang</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">-</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold text-red-600">
                                Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                            </span>
                        </td>

                        <td class="px-5 py-4 text-center">
                            @if(($item->status ?? 'pending') == 'paid')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                    Lunas
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center text-sm text-gray-600">
                            {{ !empty($item->tanggal_pengembalian) ? \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d M Y') : '-' }}
                        </td>

                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if(($item->tipe ?? '') == 'buku')
                                    @if(($item->status ?? 'pending') == 'paid')
                                        <a href="{{ $item->invoice_route }}"
                                           class="w-8 h-8 inline-flex items-center justify-center bg-cyan-100 hover:bg-cyan-200 text-cyan-600 rounded-lg transition-colors"
                                           title="Unduh Nota Pembayaran">
                                            <i class="fas fa-file-download text-sm"></i>
                                        </a>
                                    @else
                                        <form action="{{ route('denda.paid', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Tandai denda ini sebagai lunas?')"
                                                class="w-8 h-8 inline-flex items-center justify-center bg-green-100 hover:bg-green-200 text-green-600 rounded-lg transition-colors"
                                                title="Tandai Lunas">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-money-bill-wave text-5xl mb-3 block"></i>
                            <p class="text-base font-medium text-gray-500">Tidak ada data denda</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($denda->total() > 0)
    <div class="flex items-center justify-between mt-4">
        <p class="text-sm text-gray-500">
            Menampilkan {{ $denda->firstItem() }}&ndash;{{ $denda->lastItem() }} dari {{ $denda->total() }} data
        </p>

        <div class="flex items-center gap-1">
            @if($denda->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 bg-white border border-gray-200 text-sm cursor-not-allowed">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $denda->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif

            @foreach($denda->getUrlRange(1, $denda->lastPage()) as $page => $url)
                @if($page == $denda->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-500 text-white text-sm font-medium">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            @if($denda->hasMorePages())
                <a href="{{ $denda->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 bg-white border border-gray-200 text-sm cursor-not-allowed">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </div>
@endif

@endsection