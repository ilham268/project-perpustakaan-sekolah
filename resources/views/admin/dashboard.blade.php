@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/70 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Ringkasan Perpustakaan
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Dashboard Admin
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Pantau data buku, siswa, peminjaman, kunjungan, dan denda perpustakaan.
                </p>
            </div>

            <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Total Buku
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                        {{ $totalBooks }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Total Siswa
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
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
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-50 transition group-hover:bg-emerald-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Total Buku
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalBooks }}
                    </p>

                    <p class="mt-4 text-xs font-semibold text-emerald-600">
                        Lihat data
                    </p>
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
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-sky-50 transition group-hover:bg-sky-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Total Siswa
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalAnggota }}
                    </p>

                    <p class="mt-4 text-xs font-semibold text-emerald-600">
                        Lihat data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
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

                    <p class="mt-4 text-xs font-semibold text-emerald-600">
                        Lihat data
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
            class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60"
        >
            <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-50 transition group-hover:bg-blue-100"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500">
                        Total Kunjungan
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $totalKunjungan }}
                    </p>

                    <p class="mt-4 text-xs font-semibold text-emerald-600">
                        Lihat data
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
            </div>
        </a>

    </div>

    {{-- Chart + Popular Books --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        {{-- Chart Peminjaman --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 md:text-lg">
                        Statistik Peminjaman
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Perkembangan jumlah peminjaman selama 12 bulan terakhir.
                    </p>
                </div>

                <p class="text-xs font-semibold text-slate-500">
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
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
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
                    class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                >
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="w-14 border border-slate-200 px-4 py-3 text-left">
                                No
                            </th>

                            <th class="border border-slate-200 px-4 py-3 text-left">
                                Judul Buku
                            </th>

                            <th class="border border-slate-200 px-4 py-3 text-left">
                                Penulis
                            </th>

                            <th class="w-32 border border-slate-200 px-4 py-3 text-center">
                                Total
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @forelse($popularBooks as $i => $book)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="border border-slate-200 px-4 py-3 text-slate-600">
                                    {{ $i + 1 }}
                                </td>

                                <td class="border border-slate-200 px-4 py-3">
                                    <span class="block max-w-[260px] truncate font-semibold text-slate-900">
                                        {{ $book->judul }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-4 py-3">
                                    <span class="block max-w-[180px] truncate text-slate-500">
                                        {{ $book->penulis }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-4 py-3 text-center">
                                    <span class="font-bold text-emerald-700">
                                        {{ $book->total_pinjam }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        kali
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border border-slate-200 px-5 py-12 text-center">
                                    <p class="text-sm font-semibold text-slate-600">
                                        Belum ada data peminjaman
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
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
                class="inline-flex h-10 w-fit items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
            >
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="w-14 border border-slate-200 px-4 py-3 text-left">
                            No
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-left">
                            Nama Siswa
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-left">
                            Nomor Identitas
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-left">
                            Kelas
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-left">
                            Judul Buku
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-left">
                            Kode Buku
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-center">
                            Kondisi
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-center">
                            Denda
                        </th>

                        <th class="border border-slate-200 px-4 py-3 text-center">
                            Tgl Kembali
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse($unpaidDenda as $i => $return)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="border border-slate-200 px-4 py-3 text-slate-600">
                                {{ $i + 1 }}
                            </td>

                            <td class="border border-slate-200 px-4 py-3">
                                <span class="font-semibold text-slate-900">
                                    {{ $return->loan->user->name }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-4 py-3 text-slate-500">
                                {{ $return->loan->user->nomor_identitas }}
                            </td>

                            <td class="border border-slate-200 px-4 py-3 text-slate-500">
                                @if($return->loan->user->kelas)
                                    Kelas {{ $return->loan->user->kelas }} - {{ $return->loan->user->jurusan ?? '' }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border border-slate-200 px-4 py-3">
                                <span class="block max-w-[260px] truncate font-semibold text-slate-800">
                                    {{ $return->loan->bookItem->book->judul }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-4 py-3 text-slate-500">
                                {{ $return->loan->bookItem->kode_buku ?? '-' }}
                            </td>

                            <td class="border border-slate-200 px-4 py-3 text-center">
                                @if($return->kondisi == 'baik')
                                    <span class="font-semibold text-green-600">
                                        Baik
                                    </span>
                                @elseif($return->kondisi == 'rusak')
                                    <span class="font-semibold text-orange-600">
                                        Rusak
                                    </span>
                                @elseif($return->kondisi == 'hilang')
                                    <span class="font-semibold text-red-600">
                                        Hilang
                                    </span>
                                @else
                                    <span class="text-slate-400">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="border border-slate-200 px-4 py-3 text-center">
                                <span class="font-bold text-red-600">
                                    Rp {{ number_format($return->denda, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="border border-slate-200 px-4 py-3 text-center text-slate-500">
                                {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->translatedFormat('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-slate-200 px-5 py-14 text-center">
                                <p class="text-sm font-bold text-slate-700">
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