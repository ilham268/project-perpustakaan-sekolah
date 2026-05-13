@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('deleted'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-trash text-red-500"></i>
            <span>{{ session('deleted') }}</span>
        </div>
    @endif

    @if(session('updated'))
        <div class="mb-4 bg-cyan-50 border border-cyan-200 text-cyan-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-pen text-cyan-500"></i>
            <span>{{ session('updated') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
            <i class="fas fa-times-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Riwayat Peminjaman Saya</h3>
            <p class="text-sm text-gray-500 mt-1">Daftar semua riwayat peminjaman buku Anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-5">
        <div class="rounded-xl p-5 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #22c4e8 0%, #0ea5c9 100%);">
            <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full opacity-20" style="background: rgba(255,255,255,0.5);"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80 mb-1">Total Riwayat</p>
                    <p class="text-2xl font-bold">{{ $loans->count() }} <span class="text-base font-normal">data</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(255,255,255,0.25);">
                    <i class="fas fa-book-open text-xl text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl p-5 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #22c4e8 0%, #0ea5c9 100%);">
            <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full opacity-20" style="background: rgba(255,255,255,0.5);"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80 mb-1">Menunggu</p>
                    <p class="text-2xl font-bold">{{ $loans->where('status', 'pending')->count() }} <span class="text-base font-normal">data</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(255,255,255,0.25);">
                    <i class="fas fa-clock text-xl text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl p-5 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #22c4e8 0%, #0ea5c9 100%);">
            <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full opacity-20" style="background: rgba(255,255,255,0.5);"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80 mb-1">Dikembalikan</p>
                    <p class="text-2xl font-bold">{{ $loans->where('status', 'dikembalikan')->count() }} <span class="text-base font-normal">data</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(255,255,255,0.25);">
                    <i class="fas fa-check-double text-xl text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px]">
                <thead>
                    <tr class="bg-cyan-500 text-white">
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider w-20">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Judul Buku</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider w-32">Kategori</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider w-28">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider w-32">Petugas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider w-36">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider w-36">Tgl Kembali</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($loans as $index => $loan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-4 py-4 text-sm text-gray-900 text-center font-medium">{{ $index + 1 }}</td>
                        <td class="px-4 py-4">
                            @if($loan->bookItem->book->foto)
                                <img src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                     alt="{{ $loan->bookItem->book->judul }}"
                                     class="w-14 h-20 object-cover rounded shadow-sm border border-gray-200">
                            @else
                                <div class="w-14 h-20 bg-gray-100 rounded shadow-sm border border-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-2xl text-gray-400"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $loan->bookItem->book->judul }}</div>
                            <div class="text-xs text-gray-500 mt-1">Kode: {{ $loan->bookItem->kode_buku }}</div>
                        </td>
                        <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 whitespace-nowrap">
                                {{ $loan->bookItem->book->category->nama_kategori }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($loan->status == 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1.5"></i>
                                    Pending
                                </span>
                            @elseif($loan->status == 'disetujui')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    Disetujui
                                </span>
                            @elseif($loan->status == 'dikembalikan')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-check-double mr-1.5"></i>
                                    Dikembalikan
                                </span>
                            @elseif($loan->status == 'ditolak')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1.5"></i>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $loan->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $loan->petugas->name ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 text-center font-medium">
                            {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-center">
                            @if($loan->tanggal_kembali)
                                <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}</span>
                            @else
                                <span class="text-gray-400 italic">Belum dikembalikan</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Alasan ditolak jika ada -->
                    @if($loan->status == 'ditolak' && $loan->alasan_ditolak)
                    <tr class="bg-red-50">
                        <td colspan="8" class="px-4 py-3">
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-info-circle text-red-500 mt-0.5"></i>
                                <div>
                                    <span class="text-sm font-medium text-red-800">Alasan Ditolak:</span>
                                    <span class="text-sm text-red-700 ml-2">{{ $loan->alasan_ditolak }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-book-reader text-4xl text-gray-400"></i>
                                </div>
                                <p class="text-lg font-semibold text-gray-700 mb-1">Belum Ada Riwayat Peminjaman</p>
                                <p class="text-sm text-gray-500">Anda belum memiliki riwayat peminjaman buku</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
