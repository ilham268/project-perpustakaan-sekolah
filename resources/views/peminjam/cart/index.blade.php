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
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
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
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Keranjang&nbsp;Peminjaman
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Keranjang Saya
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Buku yang bisa diajukan hanya buku Referensi yang sudah memiliki kode buku tersedia.
                </p>
            </div>

            <a
                href="{{ route('peminjam.list-buku') }}"
                class="inline-flex h-10 w-fit shrink-0 items-center justify-center whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                Tambah Buku
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[var(--emerald)]/30 hover:shadow-lg hover:shadow-[var(--emerald)]/10">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-[var(--emerald-tint)] transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Judul
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight text-[var(--text)]">
                        {{ $carts->count() }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        buku
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-sky-300/50 hover:shadow-lg hover:shadow-sky-100/60">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Item
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight text-[var(--text)]">
                        {{ $carts->sum('quantity') }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        item
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-amber-300/50 hover:shadow-lg hover:shadow-amber-100/60">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Siap Diajukan
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold tracking-tight {{ $cartSiapDiajukan ? 'text-[var(--text)]' : 'text-red-600' }}">
                        {{ $cartSiapDiajukan ? 1 : 0 }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
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

        <div class="rounded-2xl border border-dashed border-[var(--hairline)] bg-white px-6 py-16 text-center shadow-sm">
            <p class="font-display text-base font-semibold text-[var(--text)]">
                Keranjang Kosong
            </p>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Belum ada buku yang ditambahkan ke keranjang.
            </p>

            <a
                href="{{ route('peminjam.list-buku') }}"
                class="mt-6 inline-flex h-10 items-center justify-center rounded-xl bg-[var(--emerald-deep)] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
            >
                Jelajahi Katalog
            </a>
        </div>

    @else

        <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
            <div class="border-b border-[var(--hairline)] px-5 py-4">
                <h3 class="font-display text-lg font-semibold text-[var(--forest)]">
                    Daftar Buku di Keranjang
                </h3>

                <p class="mt-1 text-sm text-[var(--muted)]">
                    Stok dihitung dari kode buku yang sudah tersedia.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1080px] border-collapse text-sm">
                    <thead>
                        <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                            <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                No
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Judul Buku
                            </th>

                            <th class="w-36 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Jenis
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Penerbit
                            </th>

                            <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Stok Kode
                            </th>

                            <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Jumlah
                            </th>

                            <th class="w-40 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Status
                            </th>

                            <th class="w-28 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
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

                            <tr class="transition-colors hover:bg-[var(--sand)]/30">
                                <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                    {{ $index + 1 }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
                                    <span class="block max-w-[280px] truncate font-semibold text-[var(--text)]">
                                        {{ $cart->book->judul ?? '-' }}
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
                                    @if($isReferensi)
                                        <span class="font-semibold text-[var(--emerald-deep)]">
                                            {{ $cart->book->jenis_koleksi ?? '-' }}
                                        </span>
                                    @else
                                        <span class="font-semibold text-red-600">
                                            {{ $cart->book->jenis_koleksi ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                    {{ $cart->book->penerbit ?? '-' }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                    @if($availableCount > 0)
                                        <span class="font-mono-stat font-semibold text-[var(--emerald-deep)]">
                                            {{ $availableCount }} tersedia
                                        </span>
                                    @else
                                        <span class="font-mono-stat font-semibold text-red-600">
                                            0 tersedia
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
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
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white text-sm font-bold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--hairline)] hover:bg-[var(--sand)]/50 disabled:cursor-not-allowed disabled:opacity-50"
                                                {{ $cart->quantity <= 1 ? 'disabled' : '' }}
                                            >
                                                -
                                            </button>
                                        </form>

                                        <input
                                            type="text"
                                            value="{{ $cart->quantity }}"
                                            readonly
                                            class="font-mono-stat h-8 w-12 rounded-lg border border-[var(--hairline)] bg-white text-center text-sm font-bold text-[var(--text)] outline-none"
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
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white text-sm font-bold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)] disabled:cursor-not-allowed disabled:opacity-50"
                                                {{ $cart->quantity >= $availableCount || !$isReferensi ? 'disabled' : '' }}
                                            >
                                                +
                                            </button>
                                        </form>
                                    </div>
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                    @if($rowReady)
                                        <span class="font-semibold text-[var(--emerald-deep)]">
                                            Siap
                                        </span>
                                    @elseif(!$isReferensi)
                                        <span class="font-semibold text-red-600">
                                            Bukan Referensi
                                        </span>
                                    @else
                                        <span class="font-semibold text-red-600">
                                            Kode Belum Ada
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center">
                                    <form action="{{ route('cart.destroy', $cart->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus"
                                            onclick="return confirm('Hapus buku ini dari keranjang?')"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white px-3 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
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
                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
            >
                Tambah Buku
            </a>

            <form action="{{ route('loans.store') }}" method="POST" class="inline">
                @csrf

                <button
                    type="submit"
                    class="{{ $cartSiapDiajukan ? 'bg-[var(--emerald-deep)] hover:bg-[var(--forest)] focus:ring-[var(--emerald-tint)]' : 'cursor-not-allowed bg-slate-300 focus:ring-slate-100' }} inline-flex h-10 w-full items-center justify-center whitespace-nowrap rounded-xl px-4 text-sm font-semibold text-white shadow-sm transition focus:outline-none focus:ring-4 sm:w-auto"
                    {{ !$cartSiapDiajukan ? 'disabled' : '' }}
                >
                    Ajukan Peminjaman
                </button>
            </form>
        </div>

    @endif

</div>

@endsection