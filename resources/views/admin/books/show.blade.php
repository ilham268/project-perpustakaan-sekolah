@extends('layouts.admin')

@section('title', 'Detail Buku')

@section('content')
<div class="mb-4">
    <nav class="flex items-center text-sm text-gray-700">
        <a href="{{ route('books.index') }}" class="font-semibold hover:text-cyan-600 transition-colors">Kelola Buku</a>
        <span class="mx-2 font-semibold">/</span>
        <span class="text-cyan-600 font-semibold">Detail Buku</span>
    </nav>
</div>

@php
    $total = $book->bookItems->count();
    $kodeTerisi = $book->bookItems->filter(fn($item) => !empty($item->kode_buku))->count();
    $kodeKosong = $total - $kodeTerisi;
    $available = $book->bookItems->where('status', 'available')->count();
@endphp

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h3 class="text-2xl font-bold text-gray-900">Detail Buku</h3>
        <p class="mt-1 text-sm text-gray-500">
            Lihat data buku hasil import Excel dan isi kode buku per eksemplar.
        </p>
    </div>

    <a
        href="{{ route('books.edit', $book->id) }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600"
    >
        <i class="fas fa-pen text-xs"></i>
        Edit Buku
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sm:p-6">

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Eksemplar</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $total }}</p>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Kode Terisi</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-700">{{ $kodeTerisi }}</p>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Belum Diisi</p>
            <p class="mt-2 text-2xl font-extrabold text-amber-700">{{ $kodeKosong }}</p>
        </div>

        <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600">Tersedia</p>
            <p class="mt-2 text-2xl font-extrabold text-cyan-700">{{ $available }}</p>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Judul Buku</p>
            <p class="text-lg font-bold text-gray-900 leading-snug">{{ $book->judul }}</p>
        </div>

        <hr class="border-gray-100">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Nomor Klasifikasi</p>
                <p class="text-sm font-medium text-gray-900">{{ $book->nomor_klasifikasi ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Pengarang</p>
                <p class="text-sm font-medium text-gray-900">{{ $book->penulis ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Penerbit</p>
                <p class="text-sm font-medium text-gray-900">{{ $book->penerbit ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Tahun Terbit</p>
                <p class="text-sm font-medium text-gray-900">{{ $book->tahun ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Sumber Buku</p>
                <p class="text-sm font-medium text-gray-900">{{ $book->sumber_buku ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Jenis Koleksi</p>
                <p class="text-sm font-medium text-gray-900">{{ $book->jenis_koleksi ?? '-' }}</p>
            </div>
        </div>
    </div>

    <hr class="border-gray-100 mt-6">

    <div class="mt-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-3">
            <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-gray-900">Daftar Item Buku</p>
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                    {{ $total }} item
                </span>
            </div>

            <button
                type="button"
                id="open-add-item-modal"
                class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors font-medium {{ $kodeKosong <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ $kodeKosong <= 0 ? 'disabled' : '' }}
            >
                <i class="fas fa-barcode"></i>
                <span>Isi Kode Buku</span>
            </button>
        </div>

        @if($total > 0)
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full min-w-[700px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="w-16 border border-slate-200 px-4 py-3 text-left">No</th>
                            <th class="border border-slate-200 px-4 py-3 text-left">Kode Buku</th>
                            <th class="w-40 border border-slate-200 px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($book->bookItems as $index => $item)
                            <tr class="hover:bg-slate-50">
                                <td class="border border-slate-200 px-4 py-3 text-slate-500">
                                    {{ $index + 1 }}
                                </td>

                                <td class="border border-slate-200 px-4 py-3">
                                    @if($item->kode_buku)
                                        <span class="font-semibold text-slate-800">
                                            {{ $item->kode_buku }}
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">
                                            Belum diisi
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-slate-200 px-4 py-3 text-center">
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center">
                <i class="fas fa-barcode text-2xl text-gray-300 mb-2"></i>
                <p class="text-sm font-medium text-gray-600">Belum ada item buku</p>
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
                    <h4 class="text-base font-semibold text-gray-900">Isi Kode Buku</h4>
                    <p class="mt-1 text-xs text-gray-500">
                        Masukkan kode buku satu per baris. Item kosong tersedia: {{ $kodeKosong }}.
                    </p>
                </div>

                <button type="button" id="close-add-item-modal" class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="bulk-book-item-form" class="p-5 space-y-4">
                @csrf

                <input type="hidden" name="book_id" value="{{ $book->id }}">

                <div>
                    <label for="kode_buku_text" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Buku
                    </label>

                    <textarea
                        name="kode_buku_text"
                        id="kode_buku_text"
                        rows="8"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 text-sm"
                        placeholder="Contoh:
KB-001
KB-002
KB-003"
                        required
                    ></textarea>
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
                        Simpan Kode
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
        var form = document.getElementById('bulk-book-item-form');
        var submitBtn = document.getElementById('submit-add-item');
        var errorBox = document.getElementById('bulk-form-error');
        var textarea = document.getElementById('kode_buku_text');

        function toggleModal(show) {
            modal.classList.toggle('hidden', !show);
            document.body.classList.toggle('overflow-hidden', show);

            if (show) {
                setTimeout(function () {
                    textarea.focus();
                }, 10);
            }
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
            if (!el) return;

            el.addEventListener('click', function () {
                toggleModal(false);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                toggleModal(false);
            }
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
                            if (Array.isArray(errorList)) {
                                messages = messages.concat(errorList);
                            }
                        });

                        if (messages.length) {
                            errorMessage = messages.join(' ');
                        }
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
</script>
@endsection