@extends('layouts.peminjam')

@section('title', 'Buku Tamu')

@section('content')
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <div class="bg-linear-to-r from-cyan-500 to-cyan-600 rounded-xl p-5 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Buku Tamu</h1>
                <p class="text-white/90 text-sm">Silakan isi identitas dan keperluan Anda mengunjungi perpustakaan</p>
            </div>
            <div class="hidden md:flex items-center space-x-2 text-white/80">
                <i class="fas fa-book-open-reader text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('guest-book.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nama"
                           name="nama"
                           value="{{ old('nama') }}"
                           placeholder="Masukkan nama lengkap"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-transparent @error('nama') border-red-500 @enderror"
                           required>
                    @error('nama')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keperluan -->
                <div>
                    <label for="keperluan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Keperluan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="keperluan"
                              name="keperluan"
                              rows="4"
                              placeholder="Contoh: Meminjam buku, Membaca, dll."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-transparent @error('keperluan') border-red-500 @enderror"
                              required>{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="px-6 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg transition-colors duration-200 inline-flex items-center space-x-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
