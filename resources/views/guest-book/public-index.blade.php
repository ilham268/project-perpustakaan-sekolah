@extends('layouts.peminjam')

@section('title', 'Buku Tamu Siswa')
@section('page-title', 'Buku Tamu Siswa')

@section('content')

<div class="space-y-6">

    {{-- Header Banner --}}
    <div
        class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7"
        style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);"
    >
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Buku Tamu
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Buku Tamu Siswa
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Daftar catatan kunjungan siswa perpustakaan SMKN 1 Cerme. Menampilkan identitas, keperluan, dan waktu kunjungan.
                </p>
            </div>

            <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-white backdrop-blur-md md:flex">
                <i class="fas fa-users-viewfinder text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        {{-- Total Kunjungan --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-[var(--emerald)]/30 hover:shadow-lg hover:shadow-[var(--emerald)]/10">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-[var(--emerald-tint)] transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Kunjungan Siswa
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ number_format($totalKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        kunjungan tercatat
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Hari Ini --}}
        <div class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-amber-300/50 hover:shadow-lg hover:shadow-amber-100/60">
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Kunjungan Hari Ini
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ number_format($todayKunjungan) }}
                    </p>

                    <p class="mt-1 text-xs font-medium text-[var(--muted)]">
                        siswa berkunjung hari ini
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Filter & Pencarian --}}
    <form
        method="GET"
        action="{{ route('guest-book.public') }}"
        id="filter-form"
        class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm"
    >
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">
            {{-- Search Input --}}
            <div class="relative sm:col-span-2 lg:col-span-4">
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                <input
                    type="text"
                    name="search"
                    id="search-input"
                    value="{{ request('search') }}"
                    placeholder="Cari nama siswa, jurusan, atau keperluan..."
                    autocomplete="off"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] pl-10 pr-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            {{-- Filter Kelas --}}
            <div class="lg:col-span-2">
                <select
                    name="kelas"
                    id="kelas-select"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Mulai --}}
            <div class="lg:col-span-2">
                <input
                    type="date"
                    name="start_date"
                    id="start-date"
                    value="{{ request('start_date') }}"
                    aria-label="Tanggal Mulai"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            {{-- Tanggal Selesai --}}
            <div class="lg:col-span-2">
                <input
                    type="date"
                    name="end_date"
                    id="end-date"
                    value="{{ request('end_date') }}"
                    aria-label="Tanggal Selesai"
                    class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-medium text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-2">
                <button
                    type="submit"
                    class="inline-flex h-11 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    <i class="fas fa-magnifying-glass text-xs"></i>
                    <span>Cari</span>
                </button>

                <a
                    href="{{ route('guest-book.public') }}"
                    title="Reset Filter"
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white text-sm font-semibold text-[var(--muted)] shadow-sm transition hover:border-[var(--emerald)]/40 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)]"
                >
                    <i class="fas fa-rotate-left text-xs"></i>
                </a>
            </div>
        </div>
    </form>

    {{-- Tabel Data Kunjungan (Read-Only) --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="border-b border-[var(--hairline)] px-5 py-4">
            <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                Daftar Kunjungan Siswa
            </h2>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Data absensi kunjungan siswa yang tercatat di sistem perpustakaan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-14 border border-[var(--hairline)] px-4 py-3.5 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-3.5 text-left font-semibold">
                            Nama Siswa
                        </th>

                        <th class="w-44 border border-[var(--hairline)] px-4 py-3.5 text-left font-semibold">
                            Kelas / Rombel
                        </th>

                        <th class="border border-[var(--hairline)] px-5 py-3.5 text-left font-semibold">
                            Keperluan
                        </th>

                        <th class="w-32 border border-[var(--hairline)] px-4 py-3.5 text-center font-semibold">
                            Tanggal
                        </th>

                        <th class="w-24 border border-[var(--hairline)] px-4 py-3.5 text-center font-semibold">
                            Jam
                        </th>

                        <th class="w-32 border border-[var(--hairline)] px-4 py-3.5 text-center font-semibold">
                            Metode
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($guestBooks as $guest)
                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-4 py-3.5 font-medium text-[var(--muted)]">
                                {{ $guestBooks->firstItem() + $loop->index }}
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-3.5">
                                <span class="block max-w-[220px] truncate font-semibold text-[var(--text)]">
                                    {{ $guest->nama }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3.5">
                                <span class="font-medium text-[var(--text)]/80">
                                    {{ \App\Models\Kelas::formatLabel($guest->kelas, $guest->jurusan) ?: '-' }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-5 py-3.5">
                                <span class="line-clamp-2 text-[var(--text)]/90">
                                    {{ $guest->keperluan }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3.5 text-center font-mono-stat text-xs text-[var(--muted)]">
                                {{ $guest->created_at->format('d/m/Y') }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3.5 text-center font-mono-stat text-xs text-[var(--muted)]">
                                {{ $guest->created_at->format('H:i') }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3.5 text-center">
                                @if(strtolower($guest->metode_absensi ?? '') === 'qr code')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-600/10">
                                        <i class="fas fa-qrcode text-[10px]"></i>
                                        QR Code
                                    </span>
                                @elseif(strtolower($guest->metode_absensi ?? '') === 'rfid')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700 ring-1 ring-purple-600/10">
                                        <i class="fas fa-id-card text-[10px]"></i>
                                        RFID
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                                        <i class="fas fa-pen text-[10px]"></i>
                                        Manual
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--paper)] text-[var(--muted)] ring-1 ring-[var(--hairline)]">
                                    <i class="fas fa-users-slash text-2xl"></i>
                                </div>
                                <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                                    Belum Ada Data Kunjungan Siswa
                                </p>
                                <p class="mt-1 text-xs text-[var(--muted)]">
                                    Tidak ada data kunjungan yang cocok dengan pencarian atau filter yang dipilih.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($guestBooks->hasPages())
            <div class="border-t border-[var(--hairline)] px-5 py-4">
                {{ $guestBooks->links() }}
            </div>
        @endif
    </div>

</div>

<script>
    (function () {
        var searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }
    })();
</script>

@endsection
