@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Ringkasan&nbsp;Perpustakaan
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[34px]">
                    Dashboard Admin
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Pantau data buku, siswa, peminjaman, kunjungan, dan denda perpustakaan.
                </p>
            </div>

            <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                    <p class="catalog-eyebrow uppercase text-white/70">
                        Total Buku
                    </p>

                    <p class="font-mono-stat mt-1 text-2xl font-semibold leading-none text-white">
                        {{ $totalBooks }}
                    </p>
                </div>

                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                    <p class="catalog-eyebrow uppercase text-white/70">
                        Total Siswa
                    </p>

                    <p class="font-mono-stat mt-1 text-2xl font-semibold leading-none text-white">
                        {{ $totalAnggota }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-5 xl:grid-cols-4">

        {{-- Total Buku --}}
        <a
            href="{{ route('books.index') }}"
            class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[var(--emerald)]/30 hover:shadow-lg hover:shadow-[var(--emerald)]/10"
        >
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-[var(--emerald-tint)] transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Buku
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ $totalBooks }}
                    </p>

                    <p class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-[var(--emerald-deep)]">
                        Lihat data <i class="fas fa-arrow-right-long text-[10px] transition group-hover:translate-x-0.5"></i>
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Total Siswa --}}
        <a
            href="{{ route('users.index') }}?role=siswa"
            class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[var(--gold)]/30 hover:shadow-lg hover:shadow-[var(--gold)]/10"
        >
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-[#F6EEE0] transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Siswa
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ $totalAnggota }}
                    </p>

                    <p class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-[var(--emerald-deep)]">
                        Lihat data <i class="fas fa-arrow-right-long text-[10px] transition group-hover:translate-x-0.5"></i>
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#F6EEE0] text-[var(--gold)] ring-1 ring-[var(--gold)]/20">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Denda Terkumpul --}}
        <a
            href="{{ route('admin.denda.index') }}?status=paid"
            class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-amber-300/50 hover:shadow-lg hover:shadow-amber-100/60"
        >
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Denda Terkumpul
                    </p>

                    <p class="font-mono-stat mt-2 truncate text-2xl font-semibold leading-none tracking-tight text-[var(--text)]">
                        Rp {{ number_format($totalDendaSudahBayar, 0, ',', '.') }}
                    </p>

                    <p class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-[var(--emerald-deep)]">
                        Lihat data <i class="fas fa-arrow-right-long text-[10px] transition group-hover:translate-x-0.5"></i>
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Total Kunjungan --}}
        <a
            href="{{ route('admin.guest-book.index') }}"
            class="group relative overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-sky-300/50 hover:shadow-lg hover:shadow-sky-100/60"
        >
            <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:scale-110"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[var(--muted)]">
                        Total Kunjungan
                    </p>

                    <p class="font-mono-stat mt-2 text-[28px] font-semibold leading-none tracking-tight text-[var(--text)]">
                        {{ $totalKunjungan }}
                    </p>

                    <p class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-[var(--emerald-deep)]">
                        Lihat data <i class="fas fa-arrow-right-long text-[10px] transition group-hover:translate-x-0.5"></i>
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
            </div>
        </a>

    </div>

    {{-- Chart + Popular Books --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        {{-- Chart Peminjaman --}}
        <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-2 border-b border-[var(--hairline)] px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
                <div>
                    <h3 class="font-display text-base font-semibold text-[var(--forest)] md:text-lg">
                        Statistik Peminjaman
                    </h3>

                    <p class="mt-1 text-xs text-[var(--muted)]">
                        Perkembangan jumlah peminjaman selama 12 bulan terakhir.
                    </p>
                </div>

                <p class="catalog-eyebrow uppercase text-[var(--muted)]">
                    12 Bulan Terakhir
                </p>
            </div>

            <div class="p-4 md:p-6">
                <div class="h-[280px] sm:h-[320px]">
                    <canvas id="loanChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Buku Terpopuler --}}
        <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-[var(--hairline)] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-base font-semibold text-[var(--forest)] md:text-lg">
                        Buku Terpopuler
                    </h3>

                    <p class="mt-1 text-xs text-[var(--muted)]">
                        Buku paling sering dipinjam.
                    </p>
                </div>

                <a
                    href="{{ route('books.index') }}"
                    class="inline-flex h-9 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                >
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] border-collapse text-sm">
                    <thead>
                        <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                            <th class="w-14 border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                                No
                            </th>

                            <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                                Judul Buku
                            </th>

                            <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                                Penulis
                            </th>

                            <th class="w-32 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                                Total
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @forelse($popularBooks as $i => $book)
                            <tr class="transition-colors hover:bg-[var(--sand)]/30">
                                <td class="border border-[var(--hairline)] px-4 py-3 text-[var(--muted)]">
                                    {{ $i + 1 }}
                                </td>

                                <td class="border border-[var(--hairline)] px-4 py-3">
                                    <span class="block max-w-[260px] truncate font-semibold text-[var(--text)]">
                                        {{ $book->judul }}
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-4 py-3">
                                    <span class="block max-w-[180px] truncate text-[var(--muted)]">
                                        {{ $book->penulis }}
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-4 py-3 text-center">
                                    <span class="font-mono-stat font-semibold text-[var(--emerald-deep)]">
                                        {{ $book->total_pinjam }}
                                    </span>
                                    <span class="text-xs text-[var(--muted)]">
                                        kali
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border border-[var(--hairline)] px-5 py-12 text-center">
                                    <p class="text-sm font-semibold text-[var(--text)]">
                                        Belum ada data peminjaman
                                    </p>

                                    <p class="mt-1 text-xs text-[var(--muted)]">
                                        Data buku populer akan muncul setelah ada transaksi.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Denda Belum Dibayar --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-[var(--hairline)] px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
            <div>
                <h3 class="font-display text-base font-semibold text-[var(--forest)] md:text-lg">
                    Denda Belum Dibayar
                </h3>

                <p class="mt-1 text-xs text-[var(--muted)]">
                    Daftar pengembalian buku yang masih memiliki tagihan denda.
                </p>
            </div>

            <a
                href="{{ route('admin.denda.index') }}?status=pending"
                class="inline-flex h-9 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
            >
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] border-collapse text-sm">
                <thead>
                    <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                        <th class="w-14 border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            No
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            Nama Siswa
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            Nomor Identitas
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            Kelas
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            Judul Buku
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">
                            Kode Buku
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Kondisi
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Denda
                        </th>

                        <th class="border border-[var(--hairline)] px-4 py-3 text-center font-semibold">
                            Tgl Kembali
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($unpaidDenda as $i => $return)
                        <tr class="transition-colors hover:bg-[var(--sand)]/30">
                            <td class="border border-[var(--hairline)] px-4 py-3 text-[var(--muted)]">
                                {{ $i + 1 }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3">
                                <span class="font-semibold text-[var(--text)]">
                                    {{ $return->loan->user->name }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3 text-[var(--muted)]">
                                {{ $return->loan->user->nomor_identitas }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3 text-[var(--muted)]">
                                @if($return->loan->user->kelas)
                                    Kelas {{ $return->loan->user->kelas }} - {{ $return->loan->user->jurusan ?? '' }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3">
                                <span class="block max-w-[260px] truncate font-semibold text-[var(--text)]">
                                    {{ $return->loan->bookItem->book->judul }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3 text-[var(--muted)]">
                                {{ $return->loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3 text-center">
                                @if($return->kondisi == 'baik')
                                    <span class="font-semibold text-[var(--emerald-deep)]">
                                        Baik
                                    </span>
                                @elseif($return->kondisi == 'rusak')
                                    <span class="font-semibold text-amber-600">
                                        Rusak
                                    </span>
                                @elseif($return->kondisi == 'hilang')
                                    <span class="font-semibold text-red-600">
                                        Hilang
                                    </span>
                                @else
                                    <span class="text-[var(--muted)]">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3 text-center">
                                <span class="font-mono-stat font-semibold text-red-600">
                                    Rp {{ number_format($return->denda, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="border border-[var(--hairline)] px-4 py-3 text-center text-[var(--muted)]">
                                {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->translatedFormat('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-[var(--hairline)] px-5 py-14 text-center">
                                <p class="text-sm font-semibold text-[var(--text)]">
                                    Semua denda sudah dibayar
                                </p>

                                <p class="mt-1 text-xs text-[var(--muted)]">
                                    Tidak ada tagihan denda yang tertunda saat ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartElement = document.getElementById('loanChart');

        if (!chartElement) {
            return;
        }

        const ctx = chartElement.getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 320);
        gradient.addColorStop(0, 'rgba(20, 122, 84, 0.22)');
        gradient.addColorStop(1, 'rgba(20, 122, 84, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#147A54',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.42,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#147A54',
                    pointBorderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#147A54',
                    pointHoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0F3D2E',
                        titleColor: '#f8fafc',
                        bodyColor: '#dbe7e0',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        titleFont: {
                            size: 12,
                            weight: '600'
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function (context) {
                                return 'Peminjaman: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        ticks: {
                            stepSize: 1,
                            color: '#94a3b8',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>

@endsection