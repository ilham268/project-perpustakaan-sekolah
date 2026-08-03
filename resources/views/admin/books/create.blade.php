@extends('layouts.admin')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@section('content')
    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if(session('error'))
            <x-flash-message type="error" message="{{ session('error') }}" />
        @endif

        {{-- Page Hero --}}
        <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
            <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
            <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

            <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="catalog-eyebrow mb-3 flex items-center gap-2 uppercase text-white/70">
                        <a href="{{ route('books.index') }}" class="transition hover:text-white">
                            Kelola Buku
                        </a>
                        <i class="fas fa-chevron-right text-[9px] text-white/50"></i>
                        <span class="text-white">Tambah Buku</span>
                    </div>

                    <h1 class="font-display text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                        Tambah Buku Baru
                    </h1>

                    <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                        Lengkapi informasi buku, kategori, lokasi rak, dan foto sampul agar data koleksi perpustakaan terlihat rapi.
                    </p>
                </div>

                <a
                    href="{{ route('books.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/15 px-4 py-3 text-sm font-semibold text-white backdrop-blur-md transition hover:-translate-y-0.5 hover:bg-white/20"
                >
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        {{-- Main Form --}}
        <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                {{-- Left Form --}}
                <div class="xl:col-span-2">
                    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">

                        {{-- Card Header --}}
                        <div class="border-b border-[var(--hairline)] p-5 md:p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                                    <i class="fas fa-book-open text-lg"></i>
                                </div>

                                <div>
                                    <h2 class="font-display text-lg font-semibold text-[var(--forest)] md:text-xl">
                                        Informasi Buku
                                    </h2>
                                    <p class="mt-1 text-sm text-[var(--muted)]">
                                        Isi data utama buku dengan lengkap dan benar.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Fields --}}
                        <div class="space-y-6 p-5 md:p-6">

                            {{-- Judul Buku --}}
                            <div>
                                <label for="judul" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                                    <i class="fas fa-book text-xs text-[var(--muted)]"></i>
                                    Judul Buku
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="judul"
                                    id="judul"
                                    value="{{ old('judul') }}"
                                    class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    placeholder="Masukkan judul buku"
                                    required
                                >

                                @error('judul')
                                    <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kategori & Lokasi Rak --}}
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="category_id" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                                        <i class="fas fa-tags text-xs text-[var(--muted)]"></i>
                                        Kategori
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select
                                        name="category_id"
                                        id="category_id"
                                        class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] shadow-sm transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        required
                                    >
                                        <option value="">Pilih Kategori</option>

                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('category_id')
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nomor_rak" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                                        <i class="fas fa-location-dot text-xs text-[var(--muted)]"></i>
                                        Lokasi Rak
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="nomor_rak"
                                        id="nomor_rak"
                                        value="{{ old('nomor_rak') }}"
                                        class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        placeholder="Contoh: A1, B2"
                                        required
                                    >

                                    @error('nomor_rak')
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Penulis & Penerbit --}}
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="penulis" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                                        <i class="fas fa-user-pen text-xs text-[var(--muted)]"></i>
                                        Penulis
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="penulis"
                                        id="penulis"
                                        value="{{ old('penulis') }}"
                                        class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        placeholder="Nama penulis"
                                        required
                                    >

                                    @error('penulis')
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="penerbit" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                                        <i class="fas fa-building text-xs text-[var(--muted)]"></i>
                                        Penerbit
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="penerbit"
                                        id="penerbit"
                                        value="{{ old('penerbit') }}"
                                        class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        placeholder="Nama penerbit"
                                        required
                                    >

                                    @error('penerbit')
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Tahun --}}
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="tahun" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                                        <i class="fas fa-calendar-alt text-xs text-[var(--muted)]"></i>
                                        Tahun Terbit
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        name="tahun"
                                        id="tahun"
                                        value="{{ old('tahun', date('Y')) }}"
                                        min="1900"
                                        max="{{ date('Y') + 1 }}"
                                        class="block w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] shadow-sm transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        required
                                    >

                                    @error('tahun')
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Sinopsis --}}
                            <div>
                                <label for="synopsis" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                                    <i class="fas fa-align-left text-xs text-[var(--muted)]"></i>
                                    Sinopsis
                                    <span class="text-xs font-medium text-[var(--muted)]">(Opsional)</span>
                                </label>

                                <textarea
                                    name="synopsis"
                                    id="synopsis"
                                    rows="6"
                                    class="block w-full resize-none rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    placeholder="Masukkan deskripsi atau sinopsis buku..."
                                >{{ old('synopsis') }}</textarea>

                                @error('synopsis')
                                    <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Upload Card --}}
                <div class="xl:col-span-1">
                    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm xl:sticky xl:top-6">

                        {{-- Upload Header --}}
                        <div class="border-b border-[var(--hairline)] p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                                    <i class="fas fa-image"></i>
                                </div>

                                <div>
                                    <h3 class="font-display text-base font-semibold text-[var(--forest)]">
                                        Foto Sampul
                                    </h3>
                                    <p class="mt-1 text-xs leading-relaxed text-[var(--muted)]">
                                        Upload gambar sampul buku. Format JPG, JPEG, atau PNG.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg" class="hidden">

                            {{-- Preview --}}
                            <div id="preview-container" class="hidden">
                                <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--emerald-tint)]/60 p-4">
                                    <div class="relative mx-auto h-64 w-full max-w-[220px] overflow-hidden rounded-xl border border-[var(--hairline)] bg-white shadow-sm">
                                        <img
                                            id="preview-image"
                                            src=""
                                            alt="Preview"
                                            class="h-full w-full object-cover"
                                        >

                                        <button
                                            type="button"
                                            id="btn-clear-image"
                                            class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-md transition hover:bg-red-600"
                                        >
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[var(--hairline)] bg-white px-3 py-1.5 text-xs font-semibold text-[var(--emerald-deep)]">
                                            <i class="fas fa-check-circle"></i>
                                            Foto berhasil dipilih
                                        </span>

                                        <p id="file-name" class="mx-auto mt-2 max-w-xs break-all text-xs text-[var(--muted)]"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Upload Area --}}
                            <div
                                id="upload-area"
                                class="group cursor-pointer rounded-2xl border-2 border-dashed border-[var(--hairline)] bg-[var(--paper)] px-5 py-10 text-center transition hover:border-[var(--emerald)]/40 hover:bg-[var(--emerald-tint)]/40"
                            >
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-[var(--hairline)] bg-white text-[var(--emerald-deep)] shadow-sm transition group-hover:scale-105">
                                    <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                </div>

                                <p class="mt-4 text-sm font-semibold text-[var(--text)]">
                                    Upload Foto Buku
                                </p>

                                <p class="mx-auto mt-1 max-w-xs text-xs leading-relaxed text-[var(--muted)]">
                                    Klik area ini atau seret file gambar ke sini.
                                </p>

                                <span class="mt-4 inline-flex items-center justify-center rounded-xl bg-[var(--emerald-deep)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition group-hover:bg-[var(--forest)]">
                                    Pilih File
                                </span>
                            </div>

                            @error('foto')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Info Box --}}
                            <div class="mt-5 rounded-xl border border-[var(--hairline)] bg-[var(--paper)] p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-[var(--hairline)] bg-white text-[var(--emerald-deep)]">
                                        <i class="fas fa-circle-info text-xs"></i>
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-[var(--text)]">
                                            Catatan
                                        </p>
                                        <p class="mt-1 text-xs leading-relaxed text-[var(--muted)]">
                                            Gunakan foto vertikal agar tampilan sampul buku terlihat lebih rapi di halaman daftar buku.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-[var(--hairline)] bg-[var(--paper)] p-5">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-1">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[var(--forest)]"
                                >
                                    <i class="fas fa-save text-xs"></i>
                                    Simpan Buku
                                </button>

                                <a
                                    href="{{ route('books.index') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-[var(--hairline)] bg-white px-5 py-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/50"
                                >
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const fileInput   = document.getElementById('foto');
        const uploadArea  = document.getElementById('upload-area');
        const previewCont = document.getElementById('preview-container');
        const previewImg  = document.getElementById('preview-image');
        const fileNameEl  = document.getElementById('file-name');
        const clearBtn    = document.getElementById('btn-clear-image');

        function showPreview(file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImg.src = e.target.result;
                fileNameEl.textContent = file.name;
                previewCont.classList.remove('hidden');
                uploadArea.classList.add('hidden');
            };

            reader.readAsDataURL(file);
        }

        function clearImage() {
            fileInput.value = '';
            previewImg.src = '';
            fileNameEl.textContent = '';
            previewCont.classList.add('hidden');
            uploadArea.classList.remove('hidden');
        }

        uploadArea.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                showPreview(this.files[0]);
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', clearImage);
        }

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (evt) {
            document.addEventListener(evt, function (e) {
                e.preventDefault();
            }, false);
        });

        uploadArea.addEventListener('dragover', function () {
            uploadArea.classList.add('border-emerald-400', 'bg-emerald-50');
        });

        uploadArea.addEventListener('dragleave', function () {
            uploadArea.classList.remove('border-emerald-400', 'bg-emerald-50');
        });

        uploadArea.addEventListener('drop', function (e) {
            uploadArea.classList.remove('border-emerald-400', 'bg-emerald-50');

            const files = e.dataTransfer.files;

            if (files.length > 0) {
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                fileInput.files = dt.files;
                showPreview(files[0]);
            }
        });
    </script>
@endsection