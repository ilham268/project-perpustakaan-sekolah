@extends('layouts.admin')

@section('title', 'Detail Buku')

@section('content')
<div class="mb-4">
    <nav class="flex items-center text-sm">
        <a href="{{ route('books.index') }}" class="font-semibold text-[var(--text)]/70 transition-colors hover:text-[var(--emerald-deep)]">Kelola Buku</a>
        <span class="mx-2 font-semibold text-[var(--hairline)]">/</span>
        <span class="font-semibold text-[var(--emerald-deep)]">Detail Buku</span>
    </nav>
</div>

@php
    $total = $book->bookItems->count();
    $kodeTerisi = $book->bookItems->filter(fn($item) => !empty($item->kode_buku))->count();
    $kodeKosong = $total - $kodeTerisi;
    $available = $book->bookItems->where('status', 'available')->count();

    $sortedBookItems = $book->bookItems->sortBy('id')->values();
    $perPage = 20;
    $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

    $bookItemsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $sortedBookItems->forPage($currentPage, $perPage)->values(),
        $total,
        $perPage,
        $currentPage,
        [
            'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]
    );
@endphp

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h3 class="font-display text-2xl font-semibold text-[var(--forest)]">Detail Buku</h3>
        <p class="mt-1 text-sm text-[var(--muted)]">
            Lihat data buku hasil import Excel dan isi kode buku per eksemplar.
        </p>
    </div>

    <a href="{{ route('books.edit', $book->id) }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600"
    >
        <i class="fas fa-pen text-xs"></i>
        Edit Buku
    </a>
</div>

<div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm sm:p-6">

    <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--paper)] p-4">
            <p class="catalog-eyebrow uppercase text-[var(--muted)]">Total Eksemplar</p>
            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--text)]">{{ $total }}</p>
        </div>

        <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--emerald-tint)] p-4">
            <p class="catalog-eyebrow uppercase text-[var(--emerald-deep)]">Kode Terisi</p>
            <p class="font-mono-stat mt-2 text-2xl font-semibold text-[var(--emerald-deep)]">{{ $kodeTerisi }}</p>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <p class="catalog-eyebrow uppercase text-amber-600">Belum Diisi</p>
            <p class="font-mono-stat mt-2 text-2xl font-semibold text-amber-700">{{ $kodeKosong }}</p>
        </div>

        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
            <p class="catalog-eyebrow uppercase text-sky-600">Tersedia</p>
            <p class="font-mono-stat mt-2 text-2xl font-semibold text-sky-700">{{ $available }}</p>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <p class="catalog-eyebrow mb-0.5 uppercase text-[var(--muted)]">Judul Buku</p>
            <p class="font-display text-lg font-semibold leading-snug text-[var(--text)]">{{ $book->judul }}</p>
        </div>

        <hr class="border-[var(--hairline)]">

        <div class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <p class="catalog-eyebrow mb-0.5 uppercase text-[var(--muted)]">Nomor Klasifikasi</p>
                <p class="text-sm font-medium text-[var(--text)]">{{ $book->nomor_klasifikasi ?? '-' }}</p>
            </div>
            <div>
                <p class="catalog-eyebrow mb-0.5 uppercase text-[var(--muted)]">Pengarang</p>
                <p class="text-sm font-medium text-[var(--text)]">{{ $book->penulis ?? '-' }}</p>
            </div>
            <div>
                <p class="catalog-eyebrow mb-0.5 uppercase text-[var(--muted)]">Penerbit</p>
                <p class="text-sm font-medium text-[var(--text)]">{{ $book->penerbit ?? '-' }}</p>
            </div>
            <div>
                <p class="catalog-eyebrow mb-0.5 uppercase text-[var(--muted)]">Tahun Terbit</p>
                <p class="text-sm font-medium text-[var(--text)]">{{ $book->tahun ?? '-' }}</p>
            </div>
            <div>
                <p class="catalog-eyebrow mb-0.5 uppercase text-[var(--muted)]">Sumber Buku</p>
                <p class="text-sm font-medium text-[var(--text)]">{{ $book->sumber_buku ?? '-' }}</p>
            </div>
            <div>
                <p class="catalog-eyebrow mb-0.5 uppercase text-[var(--muted)]">Jenis Koleksi</p>
                <p class="text-sm font-medium text-[var(--text)]">{{ $book->jenis_koleksi ?? '-' }}</p>
            </div>
        </div>
    </div>

    <hr class="mt-6 border-[var(--hairline)]">

    <div class="mt-5">
        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <p class="font-display text-sm font-semibold text-[var(--forest)]">Daftar Item Buku</p>
                <span class="font-mono-stat inline-flex items-center rounded-full bg-[var(--sand)] px-2.5 py-0.5 text-xs font-medium text-[var(--text)]/70">
                    {{ $total }} item
                </span>
            </div>

            <button
                type="button"
                id="open-add-item-modal"
                class="flex items-center justify-center gap-1.5 rounded-lg bg-[var(--emerald-deep)] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] {{ $kodeKosong <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ $kodeKosong <= 0 ? 'disabled' : '' }}
            >
                <i class="fas fa-barcode"></i>
                <span>Isi Kode Buku</span>
            </button>
        </div>

        @if($total > 0)
            <div class="overflow-x-auto rounded-2xl border border-[var(--hairline)]">
                <table class="w-full min-w-[820px] border-collapse text-sm">
                    <thead>
                        <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                            <th class="w-16 border border-[var(--hairline)] px-4 py-3 text-left font-semibold">No</th>
                            <th class="border border-[var(--hairline)] px-4 py-3 text-left font-semibold">Kode Buku</th>
                            <th class="w-40 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">Status</th>
                            <th class="w-28 border border-[var(--hairline)] px-4 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach($bookItemsPaginated as $item)
                            <tr class="transition-colors hover:bg-[var(--sand)]/30">
                                <td class="border border-[var(--hairline)] px-4 py-3 text-[var(--muted)]">
                                    {{ $bookItemsPaginated->firstItem() + $loop->index }}
                                </td>

                                <td class="border border-[var(--hairline)] px-4 py-3">
                                    @if($item->kode_buku)
                                        <span class="font-semibold text-[var(--text)]">
                                            {{ $item->kode_buku }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                                            Belum diisi
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-[var(--hairline)] px-4 py-3 text-center">
                                    <span class="text-xs font-semibold
                                        @if($item->status === 'available') text-[var(--emerald-deep)]
                                        @elseif($item->status === 'borrowed') text-amber-600
                                        @elseif($item->status === 'damaged') text-orange-600
                                        @else text-red-600
                                        @endif">
                                        @if($item->status === 'available') Tersedia
                                        @elseif($item->status === 'borrowed') Dipinjam
                                        @elseif($item->status === 'damaged') Rusak
                                        @else Hilang
                                        @endif
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        class="open-edit-item-modal inline-flex items-center justify-center gap-1 rounded-lg border border-[var(--hairline)] bg-white px-2.5 py-1.5 text-xs font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)] hover:text-[var(--emerald-deep)]"
                                        data-item-id="{{ $item->id }}"
                                        data-kode-buku="{{ $item->kode_buku }}"
                                        data-status="{{ $item->status }}"
                                        title="Edit Kode Buku"
                                    >
                                        <i class="fas fa-pen text-[10px]"></i>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($bookItemsPaginated->hasPages())
                <div class="mt-4 rounded-2xl border border-[var(--hairline)] bg-white px-4 py-3 shadow-sm">
                    {{ $bookItemsPaginated->links() }}
                </div>
            @endif
        @else
            <div class="rounded-xl border border-dashed border-[var(--hairline)] bg-[var(--paper)] px-4 py-8 text-center">
                <i class="fas fa-barcode mb-2 text-2xl text-[var(--hairline)]"></i>
                <p class="text-sm font-medium text-[var(--muted)]">Belum ada item buku</p>
            </div>
        @endif
    </div>
</div>

{{-- Modal: Isi Kode Buku (bulk, untuk item yang masih kosong) --}}
<div id="add-item-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-[var(--ink)]/50" id="add-item-modal-overlay"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-xl overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-[var(--hairline)] px-5 py-4">
                <div>
                    <h4 class="font-display text-base font-semibold text-[var(--forest)]">Isi Kode Buku</h4>
                    <p class="mt-1 text-xs text-[var(--muted)]">
                        Masukkan kode buku satu per baris. Item kosong tersedia: {{ $kodeKosong }}.
                    </p>
                </div>

                <button type="button" id="close-add-item-modal" class="flex h-8 w-8 items-center justify-center rounded-lg text-[var(--muted)] transition hover:bg-[var(--sand)]/60">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="bulk-book-item-form" class="space-y-4 p-5">
                @csrf
                <input type="hidden" name="book_id" value="{{ $book->id }}">

                <div>
                    <label for="kode_buku_text" class="mb-2 block text-sm font-semibold text-[var(--text)]">Kode Buku</label>
                    <textarea
                        name="kode_buku_text"
                        id="kode_buku_text"
                        rows="8"
                        class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        placeholder="Contoh:&#10;KB-001&#10;KB-002&#10;KB-003"
                        required
                    ></textarea>
                </div>

                <div id="bulk-form-error" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>

                <div class="flex items-center justify-end gap-2 border-t border-[var(--hairline)] pt-4">
                    <button type="button" id="cancel-add-item" class="rounded-xl border border-[var(--hairline)] bg-white px-4 py-2 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/50">Batal</button>
                    <button type="submit" id="submit-add-item" class="rounded-xl bg-[var(--emerald-deep)] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[var(--forest)]">Simpan Kode</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Kode Buku (per item) --}}
<div id="edit-item-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-[var(--ink)]/50" id="edit-item-modal-overlay"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-[var(--hairline)] px-5 py-4">
                <div>
                    <h4 class="font-display text-base font-semibold text-[var(--forest)]">Edit Kode Buku</h4>
                    <p class="mt-1 text-xs text-[var(--muted)]">Ubah kode buku untuk item ini.</p>
                </div>

                <button type="button" id="close-edit-item-modal" class="flex h-8 w-8 items-center justify-center rounded-lg text-[var(--muted)] transition hover:bg-[var(--sand)]/60">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="edit-book-item-form" class="space-y-4 p-5">
                @csrf
                <input type="hidden" name="item_id" id="edit_item_id" value="">
                
                {{-- WAJIB ADA: Input status tersembunyi agar lolos validasi controller --}}
                <input type="hidden" name="status" id="edit_status" value="">

                <div>
                    <label for="edit_kode_buku" class="mb-2 block text-sm font-semibold text-[var(--text)]">Kode Buku</label>
                    <input
                        type="text"
                        name="kode_buku"
                        id="edit_kode_buku"
                        class="w-full rounded-xl border border-[var(--hairline)] px-3 py-2 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        placeholder="Contoh: KB-001"
                        required
                    >
                </div>

                <div id="edit-form-error" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>

                <div class="flex items-center justify-end gap-2 border-t border-[var(--hairline)] pt-4">
                    <button type="button" id="cancel-edit-item" class="rounded-xl border border-[var(--hairline)] bg-white px-4 py-2 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/50">Batal</button>
                    <button type="submit" id="submit-edit-item" class="rounded-xl bg-[var(--emerald-deep)] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[var(--forest)]">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-6 flex justify-center">
    <a href="{{ route('books.index') }}" class="rounded-xl border border-[var(--hairline)] bg-white px-8 py-2.5 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/50">
        Kembali
    </a>
</div>

<script>
    // ---------- Tambah kode buku (Bulk) ----------
    (function () {
        var modal = document.getElementById('add-item-modal');
        var openBtn = document.getElementById('open-add-item-modal');
        var closeBtn = document.getElementById('close-add-item-modal');
        var cancelBtn = document.getElementById('cancel-add-item');
        var overlay = document.getElementById('add-item-modal-overlay');
        var form = document.getElementById('bulk-book-item-form');
        var submitBtn = document.getElementById('submit-add-item');
        var errorBox = document.getElementById('bulk-form-error');
        var textarea = document.getElementById('kode_buku_text');

        function toggleModal(show) {
            modal.classList.toggle('hidden', !show);
            document.body.classList.toggle('overflow-hidden', show);
            if (show) setTimeout(function () { textarea.focus(); }, 10);
        }

        function showError(message) {
            errorBox.textContent = message;
            errorBox.classList.remove('hidden');
        }

        function clearError() {
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
        }

        if (openBtn) {
            openBtn.addEventListener('click', function () {
                clearError();
                textarea.value = '';
                toggleModal(true);
            });
        }

        [closeBtn, cancelBtn, overlay].forEach(function (el) {
            if (el) el.addEventListener('click', function () { toggleModal(false); });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) toggleModal(false);
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearError();

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
                            if (Array.isArray(errorList)) messages = messages.concat(errorList);
                        });
                        if (messages.length) errorMessage = messages.join(' ');
                    }
                    showError(errorMessage);
                    return;
                }
                window.location.reload();
            } catch (error) {
                showError('Terjadi kesalahan jaringan. Coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        });
    })();

    // ---------- Edit kode buku per item ----------
    (function () {
        var editModal = document.getElementById('edit-item-modal');
        var openEditBtns = document.querySelectorAll('.open-edit-item-modal');
        var closeEditBtn = document.getElementById('close-edit-item-modal');
        var cancelEditBtn = document.getElementById('cancel-edit-item');
        var editOverlay = document.getElementById('edit-item-modal-overlay');
        var editForm = document.getElementById('edit-book-item-form');
        var editSubmitBtn = document.getElementById('submit-edit-item');
        var editErrorBox = document.getElementById('edit-form-error');
        var editItemIdInput = document.getElementById('edit_item_id');
        var editKodeInput = document.getElementById('edit_kode_buku');
        var editStatusInput = document.getElementById('edit_status');

        if (!editModal) return;

        function toggleEditModal(show) {
            editModal.classList.toggle('hidden', !show);
            document.body.classList.toggle('overflow-hidden', show);
            if (show) setTimeout(function () { editKodeInput.focus(); }, 10);
        }

        function showEditError(message) {
            editErrorBox.textContent = message;
            editErrorBox.classList.remove('hidden');
        }

        function clearEditError() {
            editErrorBox.textContent = '';
            editErrorBox.classList.add('hidden');
        }

        openEditBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                clearEditError();
                // Ambil data dari tombol yang diklik
                editItemIdInput.value = btn.getAttribute('data-item-id');
                editKodeInput.value = btn.getAttribute('data-kode-buku') || '';
                editStatusInput.value = btn.getAttribute('data-status') || 'available';
                
                toggleEditModal(true);
            });
        });

        [closeEditBtn, cancelEditBtn, editOverlay].forEach(function (el) {
            if (el) el.addEventListener('click', function () { toggleEditModal(false); });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !editModal.classList.contains('hidden')) {
                toggleEditModal(false);
            }
        });

        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearEditError();

            editSubmitBtn.disabled = true;
            editSubmitBtn.classList.add('opacity-60', 'cursor-not-allowed');

            try {
                var itemId = editItemIdInput.value;
                var formData = new FormData(editForm);
                formData.append('_method', 'PUT');

                // PERBAIKAN: Gunakan helper route() dari Laravel agar prefix admin otomatis terbaca
                var updateUrl = "{{ route('book-items.update', ':id') }}".replace(':id', itemId);

                var response = await fetch(updateUrl, {
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
                            if (Array.isArray(errorList)) messages = messages.concat(errorList);
                        });
                        if (messages.length) errorMessage = messages.join(' ');
                    }
                    showEditError(errorMessage);
                    return;
                }

                window.location.reload();
            } catch (error) {
                showEditError('Terjadi kesalahan jaringan. Coba lagi.');
            } finally {
                editSubmitBtn.disabled = false;
                editSubmitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        });
    })();
</script>
@endsection