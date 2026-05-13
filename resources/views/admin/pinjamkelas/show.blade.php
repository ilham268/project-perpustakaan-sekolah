@extends('layouts.admin')

@section('title', 'Input Peminjaman Kelas - ' . $kategori->nama_kategori)
@section('page-title', 'Input Peminjaman Kelas: ' . $kategori->nama_kategori)

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl mx-auto">
    <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                <i class="fas fa-users text-emerald-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Form Peminjaman Kelas</h3>
                <p class="text-sm text-slate-500">Kelas: <span class="font-semibold text-emerald-600">{{ $kategori->kelas }}</span></p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <form action="{{ route('admin.pinjamkelas.kategori.proses') }}" method="POST">
            @csrf
            <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">

            <div class="space-y-5">
                <!-- Pilih Siswa -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fas fa-user-graduate text-emerald-500 mr-2"></i>
                        Pilih Siswa
                    </label>
                    <select name="user_id" id="user_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->name }} ({{ $siswa->nomor_identitas }}) - {{ $siswa->kelas }}</option>
                        @endforeach
                    </select>
                    @if($siswas->isEmpty())
                    <p class="text-xs text-red-500 mt-1">Belum ada data siswa untuk kelas {{ $kategori->kelas }}. Silakan tambahkan user dengan role siswa terlebih dahulu.</p>
                    @endif
                </div>

                <!-- Input Kode Buku -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fas fa-barcode text-emerald-500 mr-2"></i>
                        Kode Buku
                    </label>
                    <input type="text" name="kode_buku" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 uppercase" placeholder="Masukkan Kode Buku (contoh: B001)" required>
                </div>

                <!-- Button -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.pinjamkelas.kategori') }}" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Simpan Peminjaman</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Info Data Siswa -->
<div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl mx-auto">
    <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
        <h4 class="text-sm font-semibold text-slate-700">
            <i class="fas fa-users text-emerald-500 mr-2"></i>
            Daftar Siswa Kelas {{ $kategori->kelas }}
        </h4>
    </div>
    <div class="p-4 max-h-60 overflow-y-auto">
        @if($siswas->isEmpty())
            <p class="text-center text-slate-500 py-4">Belum ada data siswa untuk kelas ini</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach($siswas as $siswa)
                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 text-slate-700 rounded-full text-sm">
                    <i class="fas fa-user text-xs text-emerald-500"></i>
                    {{ $siswa->name }}
                    <span class="text-xs text-slate-400 ml-1">({{ $siswa->nomor_identitas }})</span>
                </span>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection