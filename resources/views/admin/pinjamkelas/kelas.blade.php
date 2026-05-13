@extends('layouts.admin')

@section('title', 'Kelas Pinjam')
@section('page-title', 'Kelas Pinjam')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fas fa-times-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<form method="GET" action="{{ url()->current() }}" class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
    <div class="relative w-full md:w-64">
        <i class="fas fa-search text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-sm"></i>
        <input 
            type="text" 
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama atau kode buku..." 
            class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
        >
    </div>

    <div class="w-full md:w-56">
        <select 
            name="kelas"
            onchange="this.form.submit()"
            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
        >
            <option value="">Semua Kelas</option>

            @foreach($kelasList as $kelas)
                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                    {{ $kelas }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-2">
        <button 
            type="submit"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700"
        >
            Filter
        </button>

        <a 
            href="{{ url()->current() }}"
            class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-300"
        >
            Reset
        </a>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead>
                <tr class="bg-emerald-600 text-white text-sm">
                    <th class="px-5 py-3 text-left w-16">No</th>
                    <th class="px-5 py-3 text-left">Nama Siswa</th>
                    <th class="px-5 py-3 text-left">Nomor Identitas</th>
                    <th class="px-5 py-3 text-left">Kelas</th>
                    <th class="px-5 py-3 text-left">Judul Buku</th>
                    <th class="px-5 py-3 text-left w-24">Kode Buku</th>
                    <th class="px-5 py-3 text-center w-28">Status</th>
                    <th class="px-5 py-3 text-center w-36">Aksi</th>
                </tr>
            </thead>

            <tbody id="kelasPinjamTable">
                @forelse($pinjamKelas as $index => $item)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-sm text-slate-500">
                            {{ $pinjamKelas->firstItem() + $index }}
                        </td>

                        <td class="px-5 py-3">
                            <span class="text-sm font-medium text-slate-800">
                                {{ $item->user->name ?? '-' }}
                            </span>
                        </td>

                        <td class="px-5 py-3 text-sm text-slate-600">
                            {{ $item->user->nomor_identitas ?? '-' }}
                        </td>

                        <td class="px-5 py-3 text-sm text-slate-600">
                            {{ $item->user->kelas ?? '-' }}
                        </td>

                        <td class="px-5 py-3">
                            <p class="text-sm font-medium text-slate-800">
                                {{ $item->kategori->nama_kategori ?? '-' }}
                            </p>
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
                                } elseif ($item->status == 'denda') {
                                    $statusClass = 'bg-red-100 text-red-700';
                                    $statusText = 'Denda';
                                }
                            @endphp

                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>

                            @if($item->status == 'denda' && isset($item->denda) && $item->denda > 0)
                                <div class="text-xs text-red-600 font-semibold mt-1">
                                    Rp {{ number_format($item->denda, 0, ',', '.') }}
                                </div>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($item->status == 'pending')
                                    <form action="{{ route('admin.pinjamkelas.kelas.setujui', $item->id) }}" method="POST">
                                        @csrf
                                        <button 
                                            type="submit"
                                            onclick="return confirm('Setujui peminjaman kelas ini?')"
                                            class="w-8 h-8 inline-flex items-center justify-center bg-green-100 hover:bg-green-200 text-green-600 rounded-lg transition"
                                            title="Setujui"
                                        >
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>

                                    <a 
                                        href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                        class="w-8 h-8 inline-flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition"
                                        title="Denda"
                                    >
                                        <i class="fas fa-money-bill-wave text-xs"></i>
                                    </a>
                                @elseif($item->status == 'disetujui')
                                    <a 
                                        href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                        class="w-8 h-8 inline-flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition"
                                        title="Denda"
                                    >
                                        <i class="fas fa-money-bill-wave text-xs"></i>
                                    </a>
                                @elseif($item->status == 'denda')
                                    <a 
                                        href="{{ route('admin.pinjamkelas.kelas.denda', $item->id) }}"
                                        class="w-8 h-8 inline-flex items-center justify-center bg-orange-100 hover:bg-orange-200 text-orange-600 rounded-lg transition"
                                        title="Lihat / Edit Denda"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </div>
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

@endsection