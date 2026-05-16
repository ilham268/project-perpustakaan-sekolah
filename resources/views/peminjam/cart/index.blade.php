@extends('layouts.peminjam')

@section('title', 'Keranjang Saya')
@section('page-title', 'Keranjang Peminjaman')

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

    {{-- Alert Deleted --}}
    @if(session('deleted'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fas fa-trash"></i>
                </div>

                <span>{{ session('deleted') }}</span>
            </div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Keranjang Peminjaman
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Kelola daftar buku yang akan kamu ajukan untuk dipinjam.
            </p>
        </div>

        <a
            href="{{ route('peminjam.list-buku') }}"
            class="inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Buku</span>
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Total Judul --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Judul
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $carts->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        buku
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Item --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Item
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $carts->sum('quantity') }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        item
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Siap Diajukan --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Siap Diajukan
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $carts->isEmpty() ? 0 : 1 }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-slate-400">
                        pengajuan
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-paper-plane text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    @if($carts->isEmpty())

        {{-- Empty State --}}
        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>

            <p class="mt-4 text-base font-bold text-slate-700">
                Keranjang Kosong
            </p>

            <p class="mt-1 text-sm text-slate-400">
                Belum ada buku yang ditambahkan ke keranjang.
            </p>

            <a
                href="{{ route('peminjam.list-buku') }}"
                class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600"
            >
                <i class="fas fa-book text-xs"></i>
                <span>Jelajahi Katalog</span>
            </a>
        </div>

    @else

        {{-- Cart Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-bold text-slate-900">
                    Daftar Buku di Keranjang
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Periksa kembali jumlah buku sebelum mengajukan peminjaman.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                                No
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Judul Buku
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Kategori
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Penerbit
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-center">
                                Stok
                            </th>
                            <th class="border border-slate-200 px-5 py-4 text-center">
                                Jumlah
                            </th>
                            <th class="w-24 border border-slate-200 px-5 py-4 text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($carts as $index => $cart)
                            @php
                                $availableCount = $cart->book->availableItems()->count();
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50">

                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $index + 1 }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $cart->book->judul }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $cart->book->category->nama_kategori ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $cart->book->penerbit ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @if($availableCount > 0)
                                        <span class="font-semibold text-emerald-600">
                                            {{ $availableCount }} tersedia
                                        </span>
                                    @else
                                        <span class="font-semibold text-red-600">
                                            Habis
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        <form action="{{ route('cart.update', $cart->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')

                                            <input
                                                type="hidden"
                                                name="quantity"
                                                value="{{ max(1, $cart->quantity - 1) }}"
                                            >

                                            <button
                                                type="submit"
                                                title="Kurangi"
                                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-50"
                                                {{ $cart->quantity <= 1 ? 'disabled' : '' }}
                                            >
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                        </form>

                                        <input
                                            type="text"
                                            value="{{ $cart->quantity }}"
                                            readonly
                                            class="h-8 w-12 rounded-xl border border-slate-200 bg-white text-center text-sm font-bold text-slate-700 outline-none"
                                        >

                                        <form action="{{ route('cart.update', $cart->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')

                                            <input
                                                type="hidden"
                                                name="quantity"
                                                value="{{ $cart->quantity + 1 }}"
                                            >

                                            <button
                                                type="submit"
                                                title="Tambah"
                                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50"
                                                {{ $cart->quantity >= $availableCount ? 'disabled' : '' }}
                                            >
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    <form action="{{ route('cart.destroy', $cart->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <a
                href="{{ route('peminjam.list-buku') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Buku</span>
            </a>

            <form action="{{ route('loans.store') }}" method="POST" class="inline">
                @csrf

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600 sm:w-auto"
                >
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Ajukan Peminjaman</span>
                </button>
            </form>
        </div>

    @endif

</div>

@endsection