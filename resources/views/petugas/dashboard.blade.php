@extends('layouts.petugas')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard')

@section('content')

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-2xl font-bold text-gray-900">Dashboard</h3>
    </div>

    @if($totalPending > 0)
    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3 text-sm font-medium">
            <i class="fas fa-bell text-amber-500 text-base"></i>
            <span>Ada {{ $totalPending }} pengajuan peminjaman yang menunggu persetujuan.</span>
        </div>
        <a href="{{ route('peminjaman.index') }}?status=pending" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">
            Proses sekarang
        </a>
    </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Menunggu Persetujuan -->
        <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
            <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
            <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
            <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>
            <div class="relative z-10 flex h-full items-center justify-between gap-5">
                <div class="pr-4">
                    <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Menunggu Persetujuan</p>
                    <p class="text-[18px] font-bold leading-tight text-white">{{ $totalPending }} data</p>
                    <a href="{{ route('peminjaman.index') }}?status=pending" class="text-xs text-white/80 hover:text-white mt-1 inline-block">Lihat &rarr;</a>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                    <i class="fas fa-clock text-[18px]" style="color: #1799c9;"></i>
                </div>
            </div>
        </div>
        <!-- Sedang Dipinjam -->
        <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
            <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
            <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
            <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>
            <div class="relative z-10 flex h-full items-center justify-between gap-5">
                <div class="pr-4">
                    <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Sedang Dipinjam</p>
                    <p class="text-[18px] font-bold leading-tight text-white">{{ $activeLoan }} data</p>
                    <a href="{{ route('peminjaman.index') }}?status=disetujui" class="text-xs text-white/80 hover:text-white mt-1 inline-block">Lihat &rarr;</a>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                    <i class="fas fa-book text-[18px]" style="color: #1799c9;"></i>
                </div>
            </div>
        </div>
        <!-- Pengembalian Hari Ini -->
        <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
            <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
            <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
            <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>
            <div class="relative z-10 flex h-full items-center justify-between gap-5">
                <div class="pr-4">
                    <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Pengembalian Hari Ini</p>
                    <p class="text-[18px] font-bold leading-tight text-white">{{ $todayReturns }} data</p>
                    <a href="{{ route('peminjaman.riwayat') }}" class="text-xs text-white/80 hover:text-white mt-1 inline-block">Lihat &rarr;</a>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                    <i class="fas fa-undo-alt text-[18px]" style="color: #1799c9;"></i>
                </div>
            </div>
        </div>
        <!-- Denda Belum Bayar -->
        <div class="relative overflow-hidden px-7 py-5 text-white shadow-sm" style="border-radius: 22px; min-height: 96px; background: linear-gradient(90deg, #1fa5d5 0%, #25b3e1 58%, #58c5ed 100%);">
            <div class="absolute rounded-full" style="right: -56px; top: -60px; width: 216px; height: 216px; background: rgba(176, 235, 251, 0.24);"></div>
            <div class="absolute rounded-full" style="right: -30px; top: -34px; width: 164px; height: 164px; background: rgba(193, 240, 252, 0.28);"></div>
            <div class="absolute rounded-full" style="right: 4px; top: 0; width: 96px; height: 96px; background: rgba(214, 246, 254, 0.38);"></div>
            <div class="relative z-10 flex h-full items-center justify-between gap-5">
                <div class="pr-4">
                    <p class="mb-1 text-[14px] font-medium leading-none text-white/95">Denda Belum Bayar</p>
                    <p class="text-[18px] font-bold leading-tight text-white">{{ $unpaidDendaCount }} data</p>
                    <a href="{{ route('denda.index') }}" class="text-xs text-white/80 hover:text-white mt-1 inline-block">Lihat &rarr;</a>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" style="background: rgba(244, 252, 255, 0.88);">
                    <i class="fas fa-money-bill-wave text-[18px]" style="color: #1799c9;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Row: Chart + Pending Panel -->
    <div class="grid grid-cols-3 gap-5">
        <!-- Chart -->
        <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Statistik Peminjaman</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">12 Bulan Terakhir</span>
            </div>
            <div style="height: 240px;">
                <canvas id="loanChart"></canvas>
            </div>
        </div>

        <!-- Pengajuan Menunggu Persetujuan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Perlu Diproses</h3>
                <a href="{{ route('peminjaman.index') }}" class="text-xs text-cyan-600 hover:text-cyan-700">Lihat Semua &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($pendingLoans as $loan)
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 hover:bg-cyan-50 transition-colors">
                    {{-- Cover --}}
                    @if($loan->bookItem->book->foto)
                        <img src="{{ asset('storage/' . $loan->bookItem->book->foto) }}"
                             class="w-9 h-12 object-cover rounded-md border border-gray-100 shrink-0 shadow-sm">
                    @else
                        <div class="w-9 h-12 bg-gray-100 rounded-md border border-gray-100 shrink-0 flex items-center justify-center shadow-sm">
                            <i class="fas fa-book text-gray-300 text-sm"></i>
                        </div>
                    @endif
                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $loan->bookItem->book->judul }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $loan->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</p>
                    </div>
                    {{-- Actions --}}
                    <div class="flex flex-col gap-1 shrink-0">
                        <form method="POST" action="{{ route('peminjaman.approve', $loan->id) }}">
                            @csrf
                            <button type="submit" title="Setujui"
                                    class="w-7 h-7 rounded-lg bg-green-100 hover:bg-green-200 text-green-600 flex items-center justify-center transition-colors">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('peminjaman.reject', $loan->id) }}">
                            @csrf
                            <button type="submit" title="Tolak"
                                    class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-500 flex items-center justify-center transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-check-circle text-3xl mb-2 block text-green-400"></i>
                    <p class="text-sm">Tidak ada pengajuan pending</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('loanChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($chartData) !!},
                    borderColor: 'rgb(6, 182, 212)',
                    backgroundColor: 'rgba(6, 182, 212, 0.08)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(6, 182, 212)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>

@endsection
