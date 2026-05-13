@extends('layouts.petugas')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

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
        <input type="text" id="searchInput" placeholder="Cari kategori..." class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400">
    </div>
    <!-- TOMBOL TAMBAH DIHAPUS ATAU DI-NONAKTIFKAN SEMENTARA -->
    <!-- <button class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
        <i class="fas fa-plus text-xs"></i>
        <span>Tambah Kategori</span>
    </button> -->
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-emerald-600 text-white text-sm">
                    <th class="px-5 py-3 text-left w-16">No</th>
                    <th class="px-5 py-3 text-left">Kategori</th>
                    <th class="px-5 py-3 text-center w-32">Kelas</th>
                    <th class="px-5 py-3 text-center w-24">Input</th>
                    <th class="px-5 py-3 text-center w-28">Aksi</th>
                <tr>
            </thead>
            <tbody id="kategoriTable">
                @forelse($kategoris as $index => $item)
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td class="px-5 py-3 text-sm text-slate-500">{{ $kategoris->firstItem() + $index }}</td>
                    <td class="px-5 py-3">
                        <span class="text-sm font-medium text-slate-800">{{ $item->nama_kategori }}</span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ $item->kelas }}</span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <a href="{{ route('petugas.pinjamkelas.create', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-arrow-right text-xs"></i>
                            <span>Input</span>
                        </a>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <button onclick="alert('Edit belum tersedia untuk petugas')" class="text-blue-600 hover:text-blue-800 mx-1">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="alert('Hapus belum tersedia untuk petugas')" class="text-red-600 hover:text-red-800 mx-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                        <i class="fas fa-folder-open text-4xl mb-2 block text-slate-300"></i>
                        Belum ada data kategori
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $kategoris->links() }}
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#kategoriTable tr');
        rows.forEach(row => {
            if (row.querySelector('td')) {
                const kategoriCell = row.querySelector('td:nth-child(2)');
                if (kategoriCell && !kategoriCell.textContent.toLowerCase().includes(searchValue)) {
                    row.style.display = 'none';
                } else if (kategoriCell) {
                    row.style.display = '';
                }
            }
        });
    });
</script>

@endsection