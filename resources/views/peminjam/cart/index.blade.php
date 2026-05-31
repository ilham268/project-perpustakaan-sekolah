@extends('layouts.peminjam')

@section('title', 'Keranjang Saya')
@section('page-title', 'Keranjang Peminjaman')

@section('content')

@php
    $cartSiapDiajukan = $carts->isNotEmpty() && $carts->every(function ($cart) {
        if (!$cart->book) {
            return false;
        }

        $jenis = strtoupper(trim((string) $cart->book->jenis_koleksi));

        $isReferensi = str_contains($jenis, 'REFERENSI')
            || str_contains($jenis, 'REFERENCE')
            || str_contains($jenis, 'REFERANCE')
            || str_contains($jenis, 'RAFERANCE')
            || str_contains($jenis, 'REFEREN')
            || str_contains($jenis, 'REF');

        $availableCount = $cart->book->bookItems->filter(function ($item) {
            return $item->status === 'available' && !empty($item->kode_buku);
        })->count();

        return $isReferensi && $availableCount >= $cart->quantity && $availableCount > 0;
    });
@endphp

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

    @if(session('deleted'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">
                Data berhasil dihapus.
            </span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Keranjang Peminjaman
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Keranjang Saya
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    Buku yang bisa diajukan hanya buku Referensi yang sudah memiliki kode buku tersedia.
                </p>
            </div>

            <a
                href="{{ route('peminjam.list-buku') }}"
                class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                Tambah Buku
            </a>
        </div>
    </div>

    {{-- Stat Cards - kotak dipertahankan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
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

        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Siap Diajukan
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight {{ $cartSiapDiajukan ? 'text-slate-900' : 'text-red-600' }}">
                        {{ $cartSiapDiajukan ? 1 : 0 }}
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

        <div class="rounded-3xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
            <p class="text-base font-bold text-slate-700">
                Keranjang Kosong
            </p>

            <p class="mt-1 text-sm text-slate-400">
                Belum ada buku yang ditambahkan ke keranjang.
            </p>

            <a
                href="{{ route('peminjam.list-buku') }}"
                class="mt-6 inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
            >
                Jelajahi Katalog
            </a>
        </div>

    @else

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-white px-5 py-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Daftar Buku di Keranjang
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Stok dihitung dari kode buku yang sudah tersedia.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1080px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                                No
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Judul Buku
                            </th>

                            <th class="w-36 border border-slate-200 px-5 py-4 text-left">
                                Jenis
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Penerbit
                            </th>

                            <th class="w-32 border border-slate-200 px-5 py-4 text-center">
                                Stok Kode
                            </th>

                            <th class="w-40 border border-slate-200 px-5 py-4 text-center">
                                Jumlah
                            </th>

                            <th class="w-40 border border-slate-200 px-5 py-4 text-center">
                                Status
                            </th>

                            <th class="w-28 border border-slate-200 px-5 py-4 text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach($carts as $index => $cart)
                            @php
                                $jenis = strtoupper(trim((string) ($cart->book->jenis_koleksi ?? '')));

                                $isReferensi = str_contains($jenis, 'REFERENSI')
                                    || str_contains($jenis, 'REFERENCE')
                                    || str_contains($jenis, 'REFERANCE')
                                    || str_contains($jenis, 'RAFERANCE')
                                    || str_contains($jenis, 'REFEREN')
                                    || str_contains($jenis, 'REF');

                                $availableCount = $cart->book
                                    ? $cart->book->bookItems->filter(function ($item) {
                                        return $item->status === 'available' && !empty($item->kode_buku);
                                    })->count()
                                    : 0;

                                $rowReady = $cart->book && $isReferensi && $availableCount >= $cart->quantity && $availableCount > 0;
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $index + 1 }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="block max-w-[280px] truncate font-semibold text-slate-800">
                                        {{ $cart->book->judul ?? '-' }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    @if($isReferensi)
                                        <span class="font-semibold text-emerald-700">
                                            {{ $cart->book->jenis_koleksi ?? '-' }}
                                        </span>
                                    @else
                                        <span class="font-semibold text-red-700">
                                            {{ $cart->book->jenis_koleksi ?? '-' }}
                                        </span>
                                    @endif
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
                                            0 tersedia
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
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                                                {{ $cart->quantity <= 1 ? 'disabled' : '' }}
                                            >
                                                -
                                            </button>
                                        </form>

                                        <input
                                            type="text"
                                            value="{{ $cart->quantity }}"
                                            readonly
                                            class="h-8 w-12 rounded-lg border border-slate-200 bg-white text-center text-sm font-bold text-slate-700 outline-none"
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
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm font-bold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                                {{ $cart->quantity >= $availableCount || !$isReferensi ? 'disabled' : '' }}
                                            >
                                                +
                                            </button>
                                        </form>
                                    </div>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    @if($rowReady)
                                        <span class="font-semibold text-emerald-700">
                                            Siap
                                        </span>
                                    @elseif(!$isReferensi)
                                        <span class="font-semibold text-red-700">
                                            Bukan Referensi
                                        </span>
                                    @else
                                        <span class="font-semibold text-red-700">
                                            Kode Belum Ada
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center">
                                    <form action="{{ route('cart.destroy', $cart->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus"
                                            onclick="return confirm('Hapus buku ini dari keranjang?')"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <a
                href="{{ route('peminjam.list-buku') }}"
                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
            >
                Tambah Buku
            </a>

            <form action="{{ route('loans.store') }}" method="POST" class="inline">
                @csrf

                <button
                    type="submit"
                    class="{{ $cartSiapDiajukan ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-100' : 'cursor-not-allowed bg-slate-300 focus:ring-slate-100' }} inline-flex h-10 w-full items-center justify-center whitespace-nowrap rounded-lg px-4 text-sm font-semibold text-white shadow-sm transition focus:outline-none focus:ring-4 sm:w-auto"
                    {{ !$cartSiapDiajukan ? 'disabled' : '' }}
                >
                    Ajukan Peminjaman
                </button>
            </form>
        </div>

    @endif

</div>

@endsection