@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/70 md:px-7 md:py-6">

        {{-- Decorative Shape --}}
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            {{-- Text --}}
            <div class="max-w-2xl">
                <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Dashboard Admin
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Pantau data buku, siswa, peminjaman, kunjungan, dan denda perpustakaan.
                </p>
            </div>

            {{-- Small Summary Cards --}}
            <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md transition hover:bg-white/20">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Total Buku
                            </p>

                            <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                {{ $totalBooks }}
                            </p>
                        </div>

                        <div class="hidden h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20 sm:flex">
                            <i class="fas fa-book text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md transition hover:bg-white/20">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Total Siswa
                            </p>

                            <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                {{ $totalAnggota }}
                            </p>
                        </div>

                        <div class="hidden h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20 sm:flex">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-5 xl:grid-cols-4">

        {{-- Total Buku --}}
        <a
            href="{{ route('books.index') }}"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Buku
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalBooks }}
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

        {{-- Total Siswa --}}
        <a
            href="{{ route('users.index') }}?role=siswa"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Siswa
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalAnggota }}
                    </p>

                    <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        Lihat data
                        <i class="fas fa-arrow-right text-[10px] transition group-hover:translate-x-1"></i>
                    </div>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Denda Terkumpul --}}
        <a
            href="{{ route('admin.denda.index') }}?status=paid"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-50 transition group-hover:bg-amber-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Denda Terkumpul
                    </p>

                    <p class="mt-2 truncate text-2xl font-bold tracking-tight text-slate-900">
                        Rp {{ number_format($totalDendaSudahBayar, 0, ',', '.') }}
                    </p>

                    <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        Lihat data
                        <i class="fas fa-arrow-right text-[10px] transition group-hover:translate-x-1"></i>
                    </div>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
        </a>

        {{-- Total Kunjungan --}}
        <a
            href="{{ route('admin.guest-book.index') }}"
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-violet-50 transition group-hover:bg-violet-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Kunjungan
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalKunjungan }}
                    </p>

                    <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        Lihat data
                        <i class="fas fa-arrow-right text-[10px] transition group-hover:translate-x-1"></i>
                    </div>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 ring-1 ring-violet-100">
                    <i class="fas fa-door-open text-xl"></i>
                </div>
            </div>
        </a>

    </div>

    {{-- Chart + Popular Books --}}
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

        {{-- Buku Terpopuler --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 md:text-lg">
                        Buku Terpopuler
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Buku paling sering dipinjam.
                    </p>
                </div>

                <a
                    href="{{ route('books.index') }}"
                    class="shrink-0 text-xs font-semibold text-emerald-600 transition hover:text-emerald-700"
                >
                    Lihat Semua
                </a>
            </div>

            <div class="max-h-[390px] space-y-3 overflow-y-auto p-4">
                @forelse($popularBooks as $i => $book)
                    @php
                        $barMax = $popularBooks->first()->total_pinjam ?? 1;
                        $barPct = $barMax > 0 ? round(($book->total_pinjam / $barMax) * 100) : 0;
                    @endphp

                    <div class="group flex items-center gap-3 rounded-2xl border border-transparent p-3 transition-all hover:border-slate-200 hover:bg-slate-50">

                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
                            {{ $i + 1 }}
                        </div>

                        @if($book->foto)
                            <img
                                src="{{ asset('storage/' . $book->foto) }}"
                                alt="{{ $book->judul }}"
                                class="h-16 w-11 shrink-0 rounded-xl border border-slate-200 object-cover shadow-sm"
                            >
                        @else
                            <div class="flex h-16 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-slate-300">
                                <i class="fas fa-book text-sm"></i>
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">
                                {{ $book->judul }}
                            </p>

                            <p class="mt-0.5 truncate text-xs text-slate-400">
                                {{ $book->penulis }}
                            </p>

                            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                                    style="width: {{ $barPct }}%"
                                ></div>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            <p class="text-lg font-bold text-emerald-600">
                                {{ $book->total_pinjam }}
                            </p>

                            <p class="text-[10px] font-medium text-slate-400">
                                kali
                            </p>
                        </div>

                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 py-12 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fas fa-book-open text-xl"></i>
                        </div>

                        <p class="mt-3 text-sm font-semibold text-slate-600">
                            Belum ada data peminjaman
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Data buku populer akan muncul setelah ada transaksi.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Denda Belum Dibayar --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
            <div>
                <h3 class="text-base font-bold text-slate-900 md:text-lg">
                    Denda Belum Dibayar
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Daftar pengembalian buku yang masih memiliki tagihan denda.
                </p>
            </div>

            <a
                href="{{ route('admin.denda.index') }}?status=pending"
                class="inline-flex w-fit items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100"
            >
                Lihat Semua
                <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                            No
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nama Siswa
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Nomor Identitas
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kelas
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Judul Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-left">
                            Kode Buku
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Kondisi
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Denda
                        </th>
                        <th class="border border-slate-200 px-5 py-4 text-center">
                            Tgl Kembali
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($unpaidDenda as $i => $return)
                        <tr class="transition-colors hover:bg-slate-50">

                            <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                {{ $i + 1 }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $return->loan->user->name }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $return->loan->user->nomor_identitas }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                @if($return->loan->user->kelas)
                                    Kelas {{ $return->loan->user->kelas }} - {{ $return->loan->user->jurusan ?? '' }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4">
                                <span class="font-semibold text-slate-800">
                                    {{ $return->loan->bookItem->book->judul }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                {{ $return->loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                @if($return->kondisi == 'baik')
                                    <span class="inline-flex items-center justify-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-green-100">
                                        Baik
                                    </span>
                                @elseif($return->kondisi == 'rusak')
                                    <span class="inline-flex items-center justify-center rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-100">
                                        Rusak
                                    </span>
                                @elseif($return->kondisi == 'hilang')
                                    <span class="inline-flex items-center justify-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-100">
                                        Hilang
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center rounded-xl bg-red-50 px-3 py-1.5 text-sm font-bold text-red-600">
                                    Rp {{ number_format($return->denda, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->translatedFormat('d M Y') }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-slate-200 px-5 py-14 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
                                    <i class="fas fa-circle-check text-2xl"></i>
                                </div>

                                <p class="mt-4 text-sm font-bold text-slate-700">
                                    Semua denda sudah dibayar
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
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