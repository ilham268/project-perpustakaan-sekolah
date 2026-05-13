@extends('layouts.peminjam')

@section('title', 'Katalog Buku')

@section('content')
    <!-- Flash Messages -->
    @if(session('success'))
        <x-flash-message type="success" />
    @endif

    @if(session('error'))
        <x-flash-message type="error" message="{{ session('error') }}" />
    @endif

    <div x-data="{
        showDetail: false,
        selectedBook: {}
    }">
    <!-- Hero Section -->
    <div class="bg-linear-to-r from-cyan-500 to-cyan-600 rounded-xl p-4 sm:p-5 mb-6 text-white relative overflow-hidden">
        <!-- Subtle Lantera Logo in Top Right -->
        <div class="absolute top-3 right-4 opacity-20">
            <img src="{{ asset('image/logoTrans.png') }}" alt="Lantera" class="h-20 w-auto object-contain">
        </div>

        <div class="max-w-4xl relative z-10">
            <h1 class="text-xl sm:text-2xl font-bold mb-2">Temukan Buku Favoritmu</h1>
            <p class="text-white/90 text-sm mb-4 max-w-2xl">Jelajahi koleksi buku dari berbagai kategori. Pinjam, baca dan kembangkan wawasanmu!</p>

            <!-- Search Bar & Filter -->
            <form method="GET" action="{{ route('peminjam.list-buku') }}" class="flex flex-col gap-2 sm:flex-row">
                <div class="relative flex-1 min-w-0">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul buku, atau penulis..."
                        class="w-full pl-9 pr-3 py-2 rounded-lg bg-white text-gray-900 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-100 shadow"
                    >
                    <i class="fas fa-search text-gray-400 absolute left-3 top-2.5 text-sm"></i>
                </div>

                <select
                    name="category_id"
                    class="w-full sm:w-auto px-3 py-2 bg-white text-gray-900 text-sm rounded-lg focus:outline-none focus:ring-1 focus:ring-cyan-100 shadow"
                >
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-white hover:bg-gray-100 text-cyan-600 text-sm rounded-lg font-semibold transition-colors shadow">
                    <i class="fas fa-search mr-1"></i>
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Books Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Koleksi Terbaru</h2>
                <p class="text-gray-600 text-xs mt-0.5">Buku-buku yang baru saja ditambahkan</p>
            </div>
        </div>

        <!-- Books Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4">
            @forelse($books as $book)
                <!-- Book Card -->
                <div class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden border border-gray-100">
                    <!-- Book Image -->
                    <div class="relative h-36 overflow-hidden bg-gray-100">
                        <img src="{{ $book->foto ? Storage::url($book->foto) : 'https://via.placeholder.com/200x280/06b6d4/ffffff?text=No+Image' }}"
                             alt="{{ $book->judul }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                        <!-- Stock Badge -->
                        <div class="absolute top-2 right-2">
                            @php
                                $availableCount = $book->availableItems()->count();
                            @endphp
                            @if($availableCount > 0)
                                <span class="px-2 py-0.5 bg-green-500 text-white text-[10px] font-bold rounded-full shadow">
                                    Tersedia
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full shadow">
                                    Habis
                                </span>
                            @endif
                        </div>

                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                            <button
                                @click="selectedBook = {
                                    foto: '{{ $book->foto ? Storage::url($book->foto) : 'https://via.placeholder.com/200x280/06b6d4/ffffff?text=No+Image' }}',
                                    judul: '{{ $book->judul }}',
                                    penulis: '{{ $book->penulis }}',
                                    penerbit: '{{ $book->penerbit }}',
                                    tahun: '{{ $book->tahun }}',
                                    kategori: '{{ $book->category->nama_kategori }}',
                                    stok: {{ $book->availableItems()->count() }},
                                    id: {{ $book->id }},
                                    synopsis: '{{ addslashes($book->synopsis ?? '-') }}'
                                }; showDetail = true"
                                class="bg-white hover:bg-cyan-500 text-gray-900 hover:text-white font-medium py-1.5 px-3 rounded-lg transition-colors duration-200 flex items-center space-x-1.5">
                                <i class="fas fa-eye text-xs"></i>
                                <span class="text-xs">Detail</span>
                            </button>
                        </div>
                    </div>

                    <!-- Book Info -->
                    <div class="p-2">
                        <!-- Category Badge -->
                        <span class="inline-block px-1.5 py-0.5 bg-cyan-100 text-cyan-700 text-[9px] font-medium rounded mb-1">
                            {{ $book->category->nama_kategori }}
                        </span>

                        <!-- Title -->
                        <h3 class="font-bold text-gray-900 text-xs mb-1 line-clamp-2 group-hover:text-cyan-600 transition-colors" title="{{ $book->judul }}">
                            {{ $book->judul }}
                        </h3>

                        <!-- Footer -->
                        <div class="flex items-center justify-between  border-t border-gray-100">
                            <div class="flex items-center text-[10px] text-gray-500">
                                <i class="fas fa-box mr-0.5 text-[9px]"></i>
                                {{ $book->availableItems()->count() }} Stok
                            </div>
                            @auth
                                <form action="{{ route('cart.store', $book->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-1 hover:bg-cyan-50 rounded transition-colors group/cart bg-transparent border-0 cursor-pointer" title="Tambah ke Keranjang">
                                        <i class="fas fa-shopping-cart text-[10px] text-gray-400 group-hover/cart:text-cyan-600"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="p-1 hover:bg-cyan-50 rounded transition-colors group/cart" title="Login untuk Meminjam">
                                    <i class="fas fa-shopping-cart text-[10px] text-gray-400 group-hover/cart:text-cyan-600"></i>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-book text-gray-400 text-4xl"></i>
                    </div>
                    <p class="text-xl font-semibold text-gray-700 mb-2">Belum ada buku</p>
                    <p class="text-gray-500">Belum ada buku yang tersedia saat ini</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($books->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $books->links() }}
            </div>
        @endif
    </div>

    <!-- Floating Cart Button -->
    @auth
        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('cart.index') }}" class="relative bg-cyan-500 hover:bg-cyan-600 text-white p-4 rounded-full shadow-xl transition-all duration-200 flex items-center justify-center">
                <i class="fas fa-shopping-cart text-xl"></i>
                <!-- Badge -->
                @php
                    $cartCount = \App\Models\Cart::where('user_id', Auth::id())->count();
                @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
        </div>
    @endauth

    <!-- Modal Detail Buku -->
    @include('peminjam.book.partials.detail-modal')
    </div>
@endsection
