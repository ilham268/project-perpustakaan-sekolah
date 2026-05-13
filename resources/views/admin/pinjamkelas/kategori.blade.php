@extends('layouts.admin')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="mb-4 flex justify-between items-center">
    <div class="relative w-64">
        <i class="fas fa-search text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-sm"></i>
        <input type="text" id="searchInput" placeholder="Cari kategori..." class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400">
    </div>
    <button onclick="openModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
        <i class="fas fa-plus text-xs"></i>
        <span>Tambah Kategori</span>
    </button>
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
                </tr>
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
                    <!-- KOLOM INPUT - TOMBOL MENUJU SHOW -->
                    <td class="px-5 py-3 text-center">
                        <a href="{{ route('admin.pinjamkelas.kategori.show', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-arrow-right text-xs"></i>
                            <span>Input</span>
                        </a>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <button onclick="editKategori({{ $item->id }}, '{{ $item->nama_kategori }}', '{{ $item->kelas }}')" class="text-blue-600 hover:text-blue-800 mx-1" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteKategori({{ $item->id }})" class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
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

<!-- Modal Tambah/Edit Kategori -->
<div id="kategoriModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="kategoriForm" method="POST">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-800" id="modalTitle">Tambah Kategori</h3>
                        <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="namaKategori" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400" placeholder="Masukkan nama kategori" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                            <select name="kelas" id="kelasSelect" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400">
                                <option value="X RPL 1">X RPL 1</option>
                                <option value="X RPL 2">X RPL 2</option>
                                <option value="XI RPL 1">XI RPL 1</option>
                                <option value="XI RPL 2">XI RPL 2</option>
                                <option value="XII RPL 1">XII RPL 1</option>
                                <option value="XII RPL 2">XII RPL 2</option>
                                <option value="X TKJ 1">X TKJ 1</option>
                                <option value="X TKJ 2">X TKJ 2</option>
                                <option value="XI TKJ 1">XI TKJ 1</option>
                                <option value="XI TKJ 2">XI TKJ 2</option>
                                <option value="XII TKJ 1">XII TKJ 1</option>
                                <option value="XII TKJ 2">XII TKJ 2</option>
                                <option value="X MM 1">X MM 1</option>
                                <option value="X MM 2">X MM 2</option>
                                <option value="XI MM 1">XI MM 1</option>
                                <option value="XI MM 2">XI MM 2</option>
                                <option value="XII MM 1">XII MM 1</option>
                                <option value="XII MM 2">XII MM 2</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row gap-3 justify-end border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('kategoriModal');
    const form = document.getElementById('kategoriForm');
    const modalTitle = document.getElementById('modalTitle');
    const namaKategoriInput = document.getElementById('namaKategori');
    const kelasSelect = document.getElementById('kelasSelect');
    let isEdit = false;
    let editId = null;

    function openModal() {
        isEdit = false;
        editId = null;
        modalTitle.innerText = 'Tambah Kategori';
        namaKategoriInput.value = '';
        kelasSelect.value = 'XII RPL 1';
        form.action = '{{ route("admin.pinjamkelas.kategori.store") }}';
        form.method = 'POST';
        
        let methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function editKategori(id, nama, kelas) {
        isEdit = true;
        editId = id;
        modalTitle.innerText = 'Edit Kategori';
        namaKategoriInput.value = nama;
        kelasSelect.value = kelas;
        form.action = '/admin/pinjamkelas/kategori/' + id;
        
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        modal.classList.remove('hidden');
    }

    function deleteKategori(id) {
        if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/pinjamkelas/kategori/' + id;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#kategoriTable tr');
        
        rows.forEach(row => {
            if (row.querySelector('td')) {
                const kategoriCell = row.querySelector('td:nth-child(2)');
                if (kategoriCell) {
                    const kategoriText = kategoriCell.textContent.toLowerCase();
                    if (kategoriText.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        });
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>

@endsection