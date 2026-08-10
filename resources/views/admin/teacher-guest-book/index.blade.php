@extends('layouts.admin')

@section('title', 'Buku Tamu Guru')
@section('page-title', 'Buku Tamu Guru')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10">
            <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                Data&nbsp;Kunjungan
            </p>

            <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                Buku Tamu Guru
            </h1>

            <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                Pantau data kunjungan guru berdasarkan nama, keperluan, dan waktu kunjungan.
            </p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="catalog-eyebrow uppercase text-[var(--muted)]">Total Kunjungan Guru</p>
            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--text)]">{{ $totalKunjungan }}</p>
        </div>
        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="catalog-eyebrow uppercase text-[var(--muted)]">Hari Ini</p>
            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--forest)]">{{ $todayKunjungan }}</p>
        </div>
        <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
            <p class="catalog-eyebrow uppercase text-[var(--muted)]">Bulan Ini</p>
            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--emerald-deep)]">{{ $monthKunjungan }}</p>
        </div>
    </div>

    {{-- Filter & Export --}}
    <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.teacher-guest-book.index') }}" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-[var(--muted)]">Cari Nama/Keperluan</label>
                    <div class="relative">
                        <i class="fas fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[var(--muted)]"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] py-2.5 pl-9 pr-3 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:bg-white focus:ring-4 focus:ring-[var(--emerald-tint)]">
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-[var(--muted)]">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:bg-white focus:ring-4 focus:ring-[var(--emerald-tint)]">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-[var(--muted)]">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-3 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:bg-white focus:ring-4 focus:ring-[var(--emerald-tint)]">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[var(--emerald-deep)] px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[var(--forest)]">
                    <i class="fas fa-filter mr-1.5"></i> Filter
                </button>
                <a href="{{ route('admin.teacher-guest-book.export', request()->all()) }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)]">
                    <i class="fas fa-file-excel mr-1.5"></i> Export Excel
                </a>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-14 border border-[var(--hairline)] px-5 py-3.5 font-semibold">#</th>
                        <th class="border border-[var(--hairline)] px-5 py-3.5 font-semibold">Nama Guru</th>
                        <th class="border border-[var(--hairline)] px-5 py-3.5 font-semibold">Keperluan</th>
                        <th class="w-44 border border-[var(--hairline)] px-5 py-3.5 font-semibold">Waktu Kunjungan</th>
                        <th class="w-28 border border-[var(--hairline)] px-5 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-[var(--text)]">
                    @forelse($guestBooks as $index => $item)
                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">{{ $guestBooks->firstItem() + $index }}</td>
                            <td class="border border-[var(--hairline)] px-5 py-4 font-semibold text-[var(--text)]">{{ $item->nama }}</td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--text)]/80">{{ $item->keperluan }}</td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-xs text-[var(--muted)]">{{ $item->created_at->format('d M Y, H:i') }}</td>
                            <td class="border border-[var(--hairline)] px-5 py-4 text-right">
                                <form action="{{ route('admin.teacher-guest-book.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 transition hover:text-red-700">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-[var(--hairline)] px-5 py-10 text-center text-xs text-[var(--muted)]">
                                Belum ada data kunjungan guru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[var(--hairline)] p-4">
            {{ $guestBooks->links() }}
        </div>
    </div>
</div>
@endsection