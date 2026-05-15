@extends('layouts.admin')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')

@php
    $kelasList = $kelasList ?? collect();
@endphp

<div class="space-y-6">

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-check"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-red-600 ring-1 ring-red-100">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Page Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 md:px-7 md:py-6 shadow-md shadow-emerald-100/60">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Kategori Buku
                </h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Kelola kategori buku untuk peminjaman kelas agar data lebih tertata dan mudah ditemukan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 w-full lg:w-[360px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Total Kategori
                            </p>
                            <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                {{ $kategoris->total() }}
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas fa-layer-group text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-emerald-50">
                                Data Kelas
                            </p>
                            <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                {{ $kelasList->count() }}
                            </p>
                        </div>

                        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                            <i class="fas fa-school text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                        <i class="fas fa-folder-tree"></i>
                    </div>

                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-slate-900">
                            Daftar Kategori
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Cari, filter kelas, tambah, edit, dan hapus kategori buku peminjaman kelas.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    onclick="openModal()"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-100 transition hover:bg-emerald-700 hover:-translate-y-0.5"
                >
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Kategori</span>
                </button>
            </div>

            {{-- Search + Filter Kelas --}}
            <div class="mt-5 grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="relative md:col-span-5">
                    <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Cari kategori atau kelas..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 py-3 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 transition focus:bg-white focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                </div>

                <div class="relative md:col-span-4">
                    <i class="fas fa-school text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>

                    <select
                        id="kelasFilter"
                        class="w-full appearance-none rounded-2xl border border-slate-200 bg-slate-100/80 py-3 pl-10 pr-10 text-sm text-slate-700 transition focus:bg-white focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                        <option value="">Semua Kelas</option>

                        @foreach($kelasList as $kelas)
                            <option value="{{ strtolower($kelas->nama_kelas) }}">
                                {{ $kelas->nama_kelas }} - {{ $kelas->jurusan }}
                            </option>
                        @endforeach
                    </select>

                    <i class="fas fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                </div>

                <div class="md:col-span-3">
                    <button
                        type="button"
                        id="resetFilter"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-200/70 hover:text-slate-800"
                    >
                        <i class="fas fa-rotate-left text-xs"></i>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white/90">
            <table class="w-full min-w-[760px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-100/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4 text-left w-16">No</th>
                        <th class="px-5 py-4 text-left">Kategori</th>
                        <th class="px-5 py-4 text-center w-40">Kelas</th>
                        <th class="px-5 py-4 text-center w-28">Input</th>
                        <th class="px-5 py-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>

                <tbody id="kategoriTable" class="divide-y divide-slate-100">
                    @forelse($kategoris as $index => $item)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
                                    {{ $kategoris->firstItem() + $index }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                        <i class="fas fa-book-open text-sm"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">
                                            {{ $item->nama_kategori }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-400">
                                            Kategori peminjaman kelas
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100"
                                    data-kelas="{{ strtolower($item->kelas) }}"
                                >
                                    {{ $item->kelas }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <a
                                    href="{{ route('admin.pinjamkelas.kategori.show', $item->id) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100 transition hover:bg-emerald-100"
                                >
                                    <span>Input</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        onclick='editKategori({{ $item->id }}, @json($item->nama_kategori), @json($item->kelas))'
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                        title="Edit"
                                    >
                                        <i class="fas fa-pen text-sm"></i>
                                    </button>

                                    <button
                                        type="button"
                                        onclick="deleteKategori({{ $item->id }})"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                        title="Hapus"
                                    >
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-folder-open text-2xl"></i>
                                </div>
                                <p class="mt-4 text-base font-bold text-slate-700">
                                    Belum ada data kategori
                                </p>
                                <p class="mt-1 text-sm text-slate-400">
                                    Klik tombol "Tambah Kategori" untuk menambahkan data baru.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Empty Result karena filter --}}
            <div id="emptyFilterResult" class="hidden px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                    <i class="fas fa-magnifying-glass text-2xl"></i>
                </div>
                <p class="mt-4 text-base font-bold text-slate-700">
                    Data tidak ditemukan
                </p>
                <p class="mt-1 text-sm text-slate-400">
                    Coba gunakan kata kunci atau filter kelas yang berbeda.
                </p>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-slate-100 bg-white/80 px-5 py-4">
            {{ $kategoris->links() }}
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit Kategori --}}
<div
    id="kategoriModal"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
            onclick="closeModal()"
        ></div>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block w-full transform overflow-hidden rounded-3xl bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:max-w-lg sm:align-middle">
            <form id="kategoriForm" method="POST">
                @csrf

                <div class="border-b border-slate-100 bg-white px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                <i class="fas fa-layer-group"></i>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-slate-900" id="modalTitle">
                                    Tambah Kategori
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    Lengkapi nama kategori dan kelas tujuan.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            onclick="closeModal()"
                            class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-5 px-6 py-5">
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <i class="fas fa-book-open text-xs text-slate-400"></i>
                            Nama Kategori
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama_kategori"
                            id="namaKategori"
                            class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            placeholder="Masukkan nama kategori"
                            required
                        >
                    </div>

                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <i class="fas fa-school text-xs text-slate-400"></i>
                            Kelas
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="kelas"
                            id="kelasSelect"
                            class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            required
                        >
                            <option value="">Pilih Kelas</option>

                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->nama_kelas }}">
                                    {{ $kelas->nama_kelas }} - {{ $kelas->jurusan }}
                                </option>
                            @endforeach
                        </select>

                        @if($kelasList->isEmpty())
                            <div class="mt-2 flex items-start gap-2 rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600 ring-1 ring-red-100">
                                <i class="fas fa-circle-exclamation mt-0.5 text-xs"></i>
                                <span>Data kelas belum ada. Tambahkan kelas dulu di menu Kelola User bagian Kelola Kelas.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        <i class="fas fa-save text-xs"></i>
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

    function openModal() {
        modalTitle.innerText = 'Tambah Kategori';
        namaKategoriInput.value = '';
        kelasSelect.value = '';

        form.action = '{{ route("admin.pinjamkelas.kategori.store") }}';
        form.method = 'POST';

        let methodInput = form.querySelector('input[name="_method"]');

        if (methodInput) {
            methodInput.remove();
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function editKategori(id, nama, kelas) {
        modalTitle.innerText = 'Edit Kategori';
        namaKategoriInput.value = nama;
        kelasSelect.value = kelas;

        form.action = '{{ url("/admin/pinjamkelas/kategori") }}/' + id;
        form.method = 'POST';

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
            const deleteForm = document.createElement('form');
            deleteForm.method = 'POST';
            deleteForm.action = '{{ url("/admin/pinjamkelas/kategori") }}/' + id;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            deleteForm.appendChild(csrf);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            deleteForm.appendChild(method);

            document.body.appendChild(deleteForm);
            deleteForm.submit();
        }
    }

    const searchInput = document.getElementById('searchInput');
    const kelasFilter = document.getElementById('kelasFilter');
    const resetFilter = document.getElementById('resetFilter');
    const emptyFilterResult = document.getElementById('emptyFilterResult');

    function applyKategoriFilter() {
        const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const kelasValue = kelasFilter ? kelasFilter.value.toLowerCase().trim() : '';
        const rows = document.querySelectorAll('#kategoriTable tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (!row.querySelector('td')) {
                return;
            }

            const kategoriCell = row.querySelector('td:nth-child(2)');
            const kelasCell = row.querySelector('td:nth-child(3)');

            const kategoriText = kategoriCell ? kategoriCell.textContent.toLowerCase() : '';
            const kelasText = kelasCell ? kelasCell.textContent.toLowerCase() : '';

            const matchSearch = !searchValue || kategoriText.includes(searchValue) || kelasText.includes(searchValue);
            const matchKelas = !kelasValue || kelasText.includes(kelasValue);

            if (matchSearch && matchKelas) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyFilterResult) {
            emptyFilterResult.classList.toggle('hidden', visibleCount > 0);
        }
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', applyKategoriFilter);
    }

    if (kelasFilter) {
        kelasFilter.addEventListener('change', applyKategoriFilter);
    }

    if (resetFilter) {
        resetFilter.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
            }

            if (kelasFilter) {
                kelasFilter.value = '';
            }

            applyKategoriFilter();
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>

@endsection