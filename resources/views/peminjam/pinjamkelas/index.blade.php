@extends('layouts.peminjam')

@section('title', 'Buku Pinjaman')
@section('page-title', 'Buku Pinjaman - Peminjaman Kelas')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white">
        <h3 class="text-lg font-bold text-slate-800">Buku Pinjaman Kelas</h3>
        <p class="text-sm text-slate-500">Daftar buku yang sedang dipinjam secara kolektif</p>
    </div>

    @if($pinjamKelas->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-100 text-slate-700 text-sm">
                    <th class="px-5 py-3 text-left w-16">No</th>
                    <th class="px-5 py-3 text-left">Kategori</th>
                    <th class="px-5 py-3 text-left">Kode Buku</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pinjamKelas as $index => $item)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-5 py-3 text-sm text-slate-500">{{ $loop->iteration }}</td>
                    <td class="px-5 py-3 text-sm text-slate-800">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm text-slate-600">{{ $item->kode_buku ?? '-' }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($item->status == 'pending')
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                        @elseif($item->status == 'disetujui')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Disetujui</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Dikembalikan</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $pinjamKelas->links() }}
    </div>
    @else
    <div class="p-6 text-center">
        <div class="bg-slate-50 rounded-lg p-8">
            <i class="fas fa-book-open text-5xl text-emerald-500 mb-3 block"></i>
            <h4 class="font-semibold text-slate-700">Belum Ada Peminjaman Kelas</h4>
            <p class="text-sm text-slate-500 mt-1">Silakan ajukan peminjaman melalui menu <strong>Input Buku</strong></p>
            <a href="{{ route('siswa.pinjamkelas.input') }}" class="inline-block mt-4 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Input Buku
            </a>
        </div>
    </div>
    @endif
</div>

@endsection