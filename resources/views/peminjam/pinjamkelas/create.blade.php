@extends('layouts.peminjam')

@section('title', 'Input Buku')
@section('page-title', 'Input Buku - Peminjaman Kelas')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-2xl mx-auto">
    <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white">
        <h3 class="text-lg font-bold text-slate-800">Form Input Buku</h3>
        <p class="text-sm text-slate-500">
            Kelas Anda: 
            <strong class="text-emerald-600">{{ Auth::user()->kelas ?? 'Belum diatur' }}</strong>
        </p>
    </div>

    <div class="p-6">
        <form action="{{ route('siswa.pinjamkelas.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fas fa-layer-group text-emerald-500 mr-2"></i>
                        Pilih Kategori
                    </label>
                    
                    @if($kategoris->isNotEmpty())
                    <select name="kategori_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">*Kategori yang tampil sesuai dengan kelas Anda</p>
                    @else
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Belum ada kategori untuk kelas {{ Auth::user()->kelas }}. 
                        Silakan hubungi petugas.
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fas fa-barcode text-emerald-500 mr-2"></i>
                        Kode Buku
                    </label>
                    <input type="text" name="kode_buku" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400 uppercase" placeholder="Masukkan Kode Buku" required>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('siswa.pinjamkelas.index') }}" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                        Simpan Peminjaman
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection