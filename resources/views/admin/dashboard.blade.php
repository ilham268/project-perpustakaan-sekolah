@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

<!-- Stat Cards - Tanpa Background Icon -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <!-- Total Buku -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-all">
        <div class="flex items-start gap-3">
            <i class="fas fa-book text-emerald-600 text-2xl mt-1"></i>
            <div class="flex-1">
                <p class="text-sm text-slate-500">Total Buku</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalBooks }}</p>
                <a href="{{ route('books.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 transition inline-flex items-center gap-1 mt-1">
                    Lihat <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Anggota -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-all">
        <div class="flex items-start gap-3">
            <i class="fas fa-users text-emerald-600 text-2xl mt-1"></i>
            <div class="flex-1">
                <p class="text-sm text-slate-500">Total Anggota</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalAnggota }}</p>
                <a href="{{ route('users.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 transition inline-flex items-center gap-1 mt-1">
                    Lihat <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Denda Terkumpul -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-all">
        <div class="flex items-start gap-3">
            <i class="fas fa-money-bill-wave text-emerald-600 text-2xl mt-1"></i>
            <div class="flex-1">
                <p class="text-sm text-slate-500">Denda Terkumpul</p>
                <p class="text-xl font-bold text-slate-800">Rp {{ number_format($totalDendaSudahBayar, 0, ',', '.') }}</p>
                <a href="{{ route('admin.denda.index') }}?status=paid" class="text-xs text-emerald-600 hover:text-emerald-700 transition inline-flex items-center gap-1 mt-1">
                    Lihat <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Kunjungan -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-all">
        <div class="flex items-start gap-3">
            <i class="fas fa-door-open text-emerald-600 text-2xl mt-1"></i>
            <div class="flex-1">
                <p class="text-sm text-slate-500">Total Kunjungan</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalKunjungan }}</p>
                <a href="{{ route('admin.guest-book.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 transition inline-flex items-center gap-1 mt-1">
                    Lihat <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Middle Row: Chart + Popular Books -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    <!-- Chart Peminjaman -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-2">
            <h3 class="text-base font-semibold text-slate-800">Statistik Peminjaman</h3>
            <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">12 Bulan Terakhir</span>
        </div>
        <div class="h-64">
            <canvas id="loanChart"></canvas>
        </div>
    </div>

    <!-- Buku Terpopuler -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-slate-800">Buku Terpopuler</h3>
            <a href="{{ route('books.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 transition">Lihat Semua →</a>
        </div>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($popularBooks as $i => $book)
            @php
                $barMax = $popularBooks->first()->total_pinjam ?? 1;
                $barPct = $barMax > 0 ? round(($book->total_pinjam / $barMax) * 100) : 0;
            @endphp
            <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors">
                <!-- Cover Buku -->
                @if($book->foto)
                    <img src="{{ asset('storage/' . $book->foto) }}"
                         class="w-10 h-14 object-cover rounded-md border border-slate-200 shrink-0 shadow-sm">
                @else
                    <div class="w-10 h-14 bg-slate-100 rounded-md border border-slate-200 shrink-0 flex items-center justify-center">
                        <i class="fas fa-book text-slate-300 text-sm"></i>
                    </div>
                @endif
                
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $book->judul }}</p>
                    <p class="text-xs text-slate-400 truncate mb-1">{{ $book->penulis }}</p>
                    <div class="w-full bg-slate-100 rounded-full h-1">
                        <div class="h-1 rounded-full bg-emerald-500 transition-all" style="width: {{ $barPct }}%"></div>
                    </div>
                </div>
                
                <div class="shrink-0 text-center min-w-[45px]">
                    <span class="text-base font-bold text-emerald-600">{{ $book->total_pinjam }}</span>
                    <p class="text-[10px] text-slate-400">kali</p>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-slate-400">
                <i class="fas fa-book-open text-3xl mb-2 block"></i>
                <p class="text-sm">Belum ada data</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Denda Belum Dibayar -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 gap-2">
        <h3 class="text-base font-semibold text-slate-800">Denda Belum Dibayar</h3>
        <a href="{{ route('admin.denda.index') }}?status=pending" class="text-xs text-emerald-600 hover:text-emerald-700 transition">Lihat Semua →</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-[750px]">
            <thead>
                <tr class="bg-slate-50 text-xs text-slate-500 border-b border-slate-100">
                    <th class="px-5 py-3 text-left w-12">No</th>
                    <th class="px-5 py-3 text-left">Anggota</th>
                    <th class="px-5 py-3 text-left">Judul Buku</th>
                    <th class="px-5 py-3 text-center w-24">Kondisi</th>
                    <th class="px-5 py-3 text-center w-28">Denda</th>
                    <th class="px-5 py-3 text-center w-32">Tgl Kembali</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($unpaidDenda as $i => $return)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3 text-sm text-slate-400">{{ $i + 1 }}.76td
                    <td class="px-5 py-3">
                        <p class="text-sm font-medium text-slate-800">{{ $return->loan->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $return->loan->user->nomor_identitas }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-sm text-slate-600 max-w-xs truncate">{{ $return->loan->bookItem->book->judul }}</p>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($return->kondisi == 'baik')
                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Baik</span>
                        @elseif($return->kondisi == 'rusak')
                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700">Rusak</span>
                        @elseif($return->kondisi == 'hilang')
                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Hilang</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-sm font-semibold text-red-600">Rp {{ number_format($return->denda, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-5 py-3 text-center text-sm text-slate-500">
                        {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->translatedFormat('d M Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                        <i class="fas fa-check-circle text-4xl mb-2 block text-emerald-400"></i>
                        <p class="text-sm">Semua denda sudah dibayar</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('loanChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#10b981',
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
                    tooltip: { 
                        mode: 'index', 
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleColor: '#f1f5f9',
                        bodyColor: '#cbd5e1'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { color: '#94a3b8', font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>

@endsection