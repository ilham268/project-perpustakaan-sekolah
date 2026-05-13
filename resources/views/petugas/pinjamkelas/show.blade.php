@extends('layouts.petugas')

@section('title', 'Input Peminjaman Kelas - ' . $kategori->nama_kategori)
@section('page-title', 'Input Peminjaman Kelas: ' . $kategori->nama_kategori)

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl mx-auto">
    <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white">
        <h3 class="text-lg font-bold text-slate-800">Form Peminjaman Kelas</h3>
        <p class="text-sm text-slate-500">Kelas: <span class="font-semibold text-emerald-600">{{ $kategori->kelas }}</span></p>
    </div>

    <div class="p-6">
        <form action="{{ route('petugas.pinjamkelas.store') }}" method="POST">
            @csrf
            <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fas fa-user-graduate text-emerald-500 mr-2"></i>
                        Pilih Siswa
                    </label>
                    <select name="user_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->name }} ({{ $siswa->nomor_identitas }})</option>
                        @endforeach
                    </select>
                    @if($siswas->isEmpty())
                    <p class="text-xs text-red-500 mt-1">Belum ada data siswa untuk kelas {{ $kategori->kelas }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fas fa-barcode text-emerald-500 mr-2"></i>
                        Kode Buku
                    </label>
                    <input type="text" name="kode_buku" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400 uppercase" placeholder="Masukkan Kode Buku (contoh: B001)" required>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('petugas.pinjamkelas.kategori') }}" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition">
                        Kembali
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                        Simpan Peminjaman
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl mx-auto">
    <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
        <h4 class="text-sm font-semibold text-slate-700">Daftar Siswa Kelas {{ $kategori->kelas }}</h4>
    </div>
    <div class="p-4">
        @if($siswas->isEmpty())
            <p class="text-center text-slate-500 py-4">Belum ada data siswa untuk kelas ini</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach($siswas as $siswa)
                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 text-slate-700 rounded-full text-sm">
                    <i class="fas fa-user text-xs text-emerald-500"></i>
                    {{ $siswa->name }}
                </span>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection