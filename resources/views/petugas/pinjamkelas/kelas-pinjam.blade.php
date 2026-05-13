@extends('layouts.petugas')

@section('title', 'Kelas Pinjam')
@section('page-title', 'Kelas Pinjam')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="mb-4 flex justify-between items-center">
    <div class="relative w-64">
        <i class="fas fa-search text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-sm"></i>
        <input type="text" id="searchInput" placeholder="Cari nama atau kode buku..." class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400">
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-emerald-600 text-white text-sm">
                    <th class="px-5 py-3 text-left w-16">No</th>
                    <th class="px-5 py-3 text-left">Nama Siswa</th>
                    <th class="px-5 py-3 text-left">Nomor Identitas</th>
                    <th class="px-5 py-3 text-left">Kelas</th>
                    <th class="px-5 py-3 text-left">Kategori</th>
                    <th class="px-5 py-3 text-left w-24">Kode Buku</th>
                    <th class="px-5 py-3 text-center w-24">Status</th>
                    <th class="px-5 py-3 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody id="kelasPinjamTable">
                @forelse($pinjamKelas as $index => $item)
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td class="px-5 py-3 text-sm text-slate-500">{{ $pinjamKelas->firstItem() + $index }}</td>
                    <td class="px-5 py-3">
                        <span class="text-sm font-medium text-slate-800">{{ $item->user->name ?? '-' }}</span>
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-600">
                        {{ $item->user->nomor_identitas ?? '-' }}
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-600">
                        {{ $item->user->kelas ?? '-' }}
                    </td>
                    <td class="px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $item->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-600">
                        {{ $item->kode_buku ?? '-' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @php
                            $statusClass = 'bg-yellow-100 text-yellow-700';
                            $statusText = 'Pending';
                            if ($item->status == 'disetujui') {
                                $statusClass = 'bg-green-100 text-green-700';
                                $statusText = 'Disetujui';
                            } elseif ($item->status == 'dikembalikan') {
                                $statusClass = 'bg-blue-100 text-blue-700';
                                $statusText = 'Dikembalikan';
                            }
                        @endphp
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($item->status == 'pending')
                        <a href="{{ route('petugas.pinjamkelas.approve', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs transition mr-1">
                            <i class="fas fa-check"></i> Setuju
                        </a>
                        <a href="{{ route('petugas.pinjamkelas.reject', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs transition" onclick="return confirm('Yakin menolak?')">
                            <i class="fas fa-times"></i> Tolak
                        </a>
                        @else
                        <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-10 text-center text-slate-500">
                        <i class="fas fa-book-open text-4xl mb-2 block text-slate-300"></i>
                        Belum ada data peminjaman kelas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $pinjamKelas->links() }}
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#kelasPinjamTable tr');
        
        rows.forEach(row => {
            if (row.querySelector('td')) {
                const namaCell = row.querySelector('td:nth-child(2)');
                const kategoriCell = row.querySelector('td:nth-child(5)');
                const kodeCell = row.querySelector('td:nth-child(6)');
                
                let match = false;
                if (namaCell && namaCell.textContent.toLowerCase().includes(searchValue)) {
                    match = true;
                }
                if (kategoriCell && kategoriCell.textContent.toLowerCase().includes(searchValue)) {
                    match = true;
                }
                if (kodeCell && kodeCell.textContent.toLowerCase().includes(searchValue)) {
                    match = true;
                }
                
                if (match) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
</script>

@endsection