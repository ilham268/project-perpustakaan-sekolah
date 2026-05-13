@extends('layouts.admin')

@section('title', 'Detail Buku')

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav class="flex items-center text-sm text-gray-700">
            <a href="{{ route('books.index') }}" class="font-semibold hover:text-cyan-600 transition-colors">Kelola Buku</a>
            <span class="mx-2 font-semibold">/</span>
            <span class="text-cyan-600 font-semibold">Detail Buku</span>
        </nav>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Detail Buku</h3>
    </div>

    @php
        $available = $book->bookItems->where('status', 'available')->count();
        $total     = $book->bookItems->count();
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

            {{-- Kolom Kiri: Cover --}}
            <div class="shrink-0 w-full lg:w-44">
                @if($book->foto)
                    <img src="{{ Storage::url($book->foto) }}" alt="{{ $book->judul }}"
                        class="w-44 h-60 object-cover rounded-lg border border-gray-200 mx-auto lg:mx-0">
                @else
                    <div class="w-44 h-60 bg-gray-100 rounded-lg border border-gray-200 flex flex-col items-center justify-center gap-2 mx-auto lg:mx-0">
                        <i class="fas fa-book text-3xl text-gray-300"></i>
                        <span class="text-xs text-gray-400">Tidak ada foto</span>
                    </div>
                @endif

                {{-- Badge stok --}}
                <div class="mt-3 flex justify-center">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1 rounded-full {{ $available > 0 ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                        <i class="fas fa-circle text-[8px]"></i>
                        {{ $available }}/{{ $total }} tersedia
                    </span>
                </div>
            </div>

            {{-- Kolom Kanan: Info --}}
            <div class="flex-1 min-w-0 space-y-4">

                {{-- Judul --}}
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Judul Buku</p>
                    <p class="text-lg font-bold text-gray-900 leading-snug">{{ $book->judul }}</p>
                </div>

                <hr class="border-gray-100">

                {{-- Grid info utama --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Penulis</p>
                        <p class="text-sm font-medium text-gray-900">{{ $book->penulis }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Penerbit</p>
                        <p class="text-sm font-medium text-gray-900">{{ $book->penerbit }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Kategori</p>
                        <p class="text-sm font-medium text-gray-900">{{ $book->category->nama_kategori }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Tahun Terbit</p>
                        <p class="text-sm font-medium text-gray-900">{{ $book->tahun }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Lokasi Rak</p>
                        <p class="text-sm font-medium text-gray-900">{{ $book->nomor_rak }}</p>
                    </div>
                </div>

                @if($book->synopsis)
                    <hr class="border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Sinopsis</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $book->synopsis }}</p>
                    </div>
                @endif
            </div>
        </div>

        <hr class="border-gray-100 mt-6">

        <div class="mt-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-900">Daftar Item Buku</p>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ $total }} item</span>
                </div>
                <div class="flex items-center">
                    <button
                        type="button"
                        id="open-add-item-modal"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors font-medium"
                    >
                        <i class="fas fa-plus"></i>
                        <span>Tambah Kode Buku</span>
                    </button>
                </div>
            </div>

            @if($total > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($book->bookItems as $item)
                        <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-barcode text-cyan-500 text-sm"></i>
                                <span class="text-sm font-medium text-gray-900">{{ $item->kode_buku }}</span>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full
                                @if($item->status === 'available') bg-green-100 text-green-700
                                @elseif($item->status === 'borrowed') bg-yellow-100 text-yellow-700
                                @elseif($item->status === 'damaged') bg-orange-100 text-orange-700
                                @else bg-red-100 text-red-700
                                @endif">
                                @if($item->status === 'available') Tersedia
                                @elseif($item->status === 'borrowed') Dipinjam
                                @elseif($item->status === 'damaged') Rusak
                                @else Hilang
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center">
                    <i class="fas fa-barcode text-2xl text-gray-300 mb-2"></i>
                    <p class="text-sm font-medium text-gray-600">Belum ada item buku</p>
                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Kode Buku" untuk menambahkan item pertama.</p>
                </div>
            @endif
        </div>
    </div>

    <div id="add-item-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/45" id="add-item-modal-overlay"></div>
        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-xl bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">Tambah Kode Buku</h4>
                    </div>
                    <button type="button" id="close-add-item-modal" class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="bulk-book-item-form" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">

                    <div id="kode-buku-rows" class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                        <div class="flex items-center gap-2 kode-row">
                            <input
                                type="text"
                                name="kode_buku[]"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 text-sm"
                                placeholder="Masukkan kode buku #1"
                                required
                            >
                            <button
                                type="button"
                                class="remove-row w-10 h-10 rounded-lg border border-gray-300 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200"
                                disabled
                                title="Hapus"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button
                            type="button"
                            id="add-row"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 hover:bg-cyan-100 text-sm font-medium"
                        >
                            <i class="fas fa-plus"></i>
                            Tambah Baris
                        </button>
                        <p id="row-counter" class="text-xs text-gray-400">1 baris</p>
                    </div>

                    <div id="bulk-form-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button
                            type="button"
                            id="cancel-add-item"
                            class="px-4 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            id="submit-add-item"
                            class="px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium"
                        >
                            Simpan Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="flex justify-center mt-6">
        <a
            href="{{ route('books.index') }}"
            class="px-8 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors text-sm font-medium"
        >
            Kembali
        </a>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('add-item-modal');
            var openBtn = document.getElementById('open-add-item-modal');
            var closeBtn = document.getElementById('close-add-item-modal');
            var cancelBtn = document.getElementById('cancel-add-item');
            var overlay = document.getElementById('add-item-modal-overlay');
            var rowsContainer = document.getElementById('kode-buku-rows');
            var addRowBtn = document.getElementById('add-row');
            var form = document.getElementById('bulk-book-item-form');
            var submitBtn = document.getElementById('submit-add-item');
            var errorBox = document.getElementById('bulk-form-error');
            var rowCounter = document.getElementById('row-counter');

            function toggleModal(show) {
                modal.classList.toggle('hidden', !show);
                document.body.classList.toggle('overflow-hidden', show);
                if (show) {
                    setTimeout(function () {
                        var firstInput = rowsContainer.querySelector('input[name="kode_buku[]"]');
                        if (firstInput) firstInput.focus();
                    }, 10);
                }
            }

            function updateRemoveButtons() {
                var rows = rowsContainer.querySelectorAll('.kode-row');
                rows.forEach(function (row, index) {
                    var removeBtn = row.querySelector('.remove-row');
                    removeBtn.disabled = rows.length === 1;
                    removeBtn.classList.toggle('opacity-50', rows.length === 1);
                    removeBtn.classList.toggle('cursor-not-allowed', rows.length === 1);
                    removeBtn.title = rows.length === 1 ? 'Minimal satu baris' : 'Hapus baris';
                    var input = row.querySelector('input[name="kode_buku[]"]');
                    input.placeholder = 'Masukkan kode buku #' + (index + 1);
                });

                rowCounter.textContent = rows.length + (rows.length > 1 ? ' baris' : ' baris');
            }

            function createRow(value) {
                var row = document.createElement('div');
                row.className = 'flex items-center gap-2 kode-row';
                row.innerHTML = '' +
                    '<input type="text" name="kode_buku[]" class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 text-sm" placeholder="Masukkan kode buku" required>' +
                    '<button type="button" class="remove-row w-10 h-10 rounded-lg border border-gray-300 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200" title="Hapus">' +
                        '<i class="fas fa-trash"></i>' +
                    '</button>';
                row.querySelector('input[name="kode_buku[]"]').value = value || '';
                rowsContainer.appendChild(row);
                updateRemoveButtons();
                return row;
            }

            function resetForm() {
                errorBox.classList.add('hidden');
                errorBox.textContent = '';
                rowsContainer.innerHTML = '';
                createRow('');
            }

            openBtn.addEventListener('click', function () {
                resetForm();
                toggleModal(true);
            });

            [closeBtn, cancelBtn, overlay].forEach(function (el) {
                el.addEventListener('click', function () {
                    toggleModal(false);
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    toggleModal(false);
                }
            });

            addRowBtn.addEventListener('click', function () {
                var row = createRow('');
                row.querySelector('input[name="kode_buku[]"]').focus();
            });

            rowsContainer.addEventListener('click', function (e) {
                var removeBtn = e.target.closest('.remove-row');
                if (!removeBtn) return;

                var rows = rowsContainer.querySelectorAll('.kode-row');
                if (rows.length === 1) return;

                removeBtn.closest('.kode-row').remove();
                updateRemoveButtons();
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                errorBox.classList.add('hidden');
                errorBox.textContent = '';

                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-60', 'cursor-not-allowed');

                try {
                    var formData = new FormData(form);
                    var response = await fetch("{{ route('book-items.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    var result = await response.json();

                    if (!response.ok || !result.success) {
                        var errorMessage = result.message || 'Terjadi kesalahan saat menyimpan data.';
                        if (result.errors) {
                            var messages = [];
                            Object.values(result.errors).forEach(function (errorList) {
                                if (Array.isArray(errorList)) {
                                    messages = messages.concat(errorList);
                                }
                            });
                            if (messages.length) {
                                errorMessage = messages.join(' ');
                            }
                        }

                        errorBox.textContent = errorMessage;
                        errorBox.classList.remove('hidden');
                        return;
                    }

                    window.location.reload();
                } catch (error) {
                    errorBox.textContent = 'Terjadi kesalahan jaringan. Coba lagi.';
                    errorBox.classList.remove('hidden');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                }
            });

            updateRemoveButtons();
        })();
    </script>
@endsection
