@extends('layouts.petugas')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/70 md:px-7 md:py-6">

        {{-- Decorative Shape --}}
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Dashboard Petugas
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Kelola pengajuan peminjaman, pengembalian buku, dan denda perpustakaan.
                </p>
            </div>
        </div>

    </div>

    {{-- Alert Pending --}}
    @if($totalPending > 0)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3 text-sm font-medium text-amber-900">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 ring-1 ring-amber-200">
                        <i class="fas fa-bell text-base"></i>
                    </div>

                    <span>
                        Ada <strong>{{ $totalPending }}</strong> pengajuan peminjaman yang menunggu persetujuan.
                    </span>
                </div>

                <a
                    href="{{ route('peminjaman.index') }}?status=pending"
                    class="inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600"
                >
                    Proses sekarang
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>

            </div>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-5 xl:grid-cols-4">

        {{-- Menunggu Persetujuan --}}
        <a
            href="{{ route('peminjaman.index') }}?status=pending"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:bg-sky-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Menunggu Persetujuan
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalPending }}
                    </p>

                    <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        Lihat data
                        <i class="fas fa-arrow-right text-[10px] transition group-hover:translate-x-1"></i>
                    </div>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Sedang Dipinjam --}}
        <a
            href="{{ route('peminjaman.index') }}?status=disetujui"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Sedang Dipinjam
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $activeLoan }}
                    </p>

                    <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        Lihat data
                        <i class="fas fa-arrow-right text-[10px] transition group-hover:translate-x-1"></i>
                    </div>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Pengembalian Hari Ini --}}
        <a
            href="{{ route('peminjaman.riwayat') }}"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Pengembalian Hari Ini
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $todayReturns }}
                    </p>

                    <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        Lihat data
                        <i class="fas fa-arrow-right text-[10px] transition group-hover:translate-x-1"></i>
                    </div>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-undo-alt text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Denda Belum Bayar --}}
        <a
            href="{{ route('denda.index') }}"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Denda Belum Bayar
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $unpaidDendaCount }}
                    </p>

                    <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        Lihat data
                        <i class="fas fa-arrow-right text-[10px] transition group-hover:translate-x-1"></i>
                    </div>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
        </a>

    </div>

    {{-- Chart + Pending Panel --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        {{-- Chart Peminjaman --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 md:text-lg">
                        Statistik Peminjaman
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Perkembangan jumlah peminjaman selama 12 bulan terakhir.
                    </p>
                </div>

                <div class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                    <i class="fas fa-calendar-days"></i>
                    12 Bulan Terakhir
                </div>
            </div>

            <div class="p-4 md:p-6">
                <div class="h-[280px] sm:h-[320px]">
                    <canvas id="loanChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Pengajuan Menunggu Persetujuan --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 md:text-lg">
                        Perlu Diproses
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Pengajuan peminjaman terbaru.
                    </p>
                </div>

                <a
                    href="{{ route('peminjaman.index') }}"
                    class="shrink-0 text-xs font-semibold text-emerald-600 transition hover:text-emerald-700"
                >
                    Lihat Semua
                </a>
            </div>

            <div class="max-h-[390px] space-y-3 overflow-y-auto p-4">
                @forelse($pendingLoans as $loan)
                    <div class="group flex items-center gap-3 rounded-2xl border border-transparent p-3 transition-all hover:border-slate-200 hover:bg-slate-50">

                        {{-- Cover --}}
                        @if($loan->bookItem->book->foto)
                            <img
                                src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                                alt="{{ $loan->bookItem->book->judul }}"
                                class="h-16 w-11 shrink-0 rounded-xl border border-slate-200 object-cover shadow-sm"
                            >
                        @else
                            <div class="flex h-16 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-slate-300">
                                <i class="fas fa-book text-sm"></i>
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">
                                {{ $loan->bookItem->book->judul }}
                            </p>

                            <p class="mt-0.5 truncate text-xs text-slate-400">
                                {{ $loan->user->name }}
                            </p>

                            <p class="mt-0.5 text-xs font-medium text-emerald-600">
                                {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->translatedFormat('d M Y') }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex shrink-0 flex-col gap-1">
                            <form method="POST" action="{{ route('peminjaman.approve', $loan->id) }}">
                                @csrf

                                <button
                                    type="submit"
                                    title="Setujui"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-green-50 text-green-600 ring-1 ring-green-100 transition hover:bg-green-100"
                                >
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('peminjaman.reject', $loan->id) }}">
                                @csrf

                                <button
                                    type="submit"
                                    title="Tolak"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-500 ring-1 ring-red-100 transition hover:bg-red-100"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 py-12 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
                            <i class="fas fa-circle-check text-xl"></i>
                        </div>

                        <p class="mt-3 text-sm font-semibold text-slate-600">
                            Tidak ada pengajuan pending
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Semua pengajuan peminjaman sudah diproses.
                        </p>
                    </div>
                @endforelse
            </div>
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
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.42,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#10b981',
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
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
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