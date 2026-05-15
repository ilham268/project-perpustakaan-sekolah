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
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 md:px-7 md:py-6 shadow-md shadow-emerald-100/60">
            <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-emerald-50">
                        <a href="{{ route('books.index') }}" class="transition hover:text-white">
                            Kelola Buku
                        </a>
                        <i class="fas fa-chevron-right text-[10px] text-emerald-100"></i>
                        <span class="text-white">Tambah Buku</span>
                    </div>

                    <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                        Tambah Buku Baru
                    </h1>

                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                        Lengkapi informasi buku, kategori, lokasi rak, dan foto sampul agar data koleksi perpustakaan terlihat rapi.
                    </p>
                </div>

                <a
                    href="{{ route('books.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/15 px-4 py-3 text-sm font-bold text-white ring-1 ring-white/25 backdrop-blur-md transition hover:-translate-y-0.5 hover:bg-white/20"
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
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white/95 shadow-sm">

                        {{-- Card Header --}}
                        <div class="border-b border-slate-100 bg-white/80 p-5 md:p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-book-open text-lg"></i>
                                </div>

                                <div>
                                    <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                                        Informasi Buku
                                    </h2>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Isi data utama buku dengan lengkap dan benar.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Fields --}}
                        <div class="space-y-6 p-5 md:p-6">

                            {{-- Judul Buku --}}
                            <div>
                                <label for="judul" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <i class="fas fa-book text-xs text-slate-400"></i>
                                    Judul Buku
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="judul"
                                    id="judul"
                                    value="{{ old('judul') }}"
                                    class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                    <label for="category_id" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <i class="fas fa-tags text-xs text-slate-400"></i>
                                        Kategori
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select
                                        name="category_id"
                                        id="category_id"
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                    <label for="nomor_rak" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <i class="fas fa-location-dot text-xs text-slate-400"></i>
                                        Lokasi Rak
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="nomor_rak"
                                        id="nomor_rak"
                                        value="{{ old('nomor_rak') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                    <label for="penulis" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <i class="fas fa-user-pen text-xs text-slate-400"></i>
                                        Penulis
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="penulis"
                                        id="penulis"
                                        value="{{ old('penulis') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                        placeholder="Nama penulis"
                                        required
                                    >

                                    @error('penulis')
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="penerbit" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <i class="fas fa-building text-xs text-slate-400"></i>
                                        Penerbit
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="penerbit"
                                        id="penerbit"
                                        value="{{ old('penerbit') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                    <label for="tahun" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <i class="fas fa-calendar-alt text-xs text-slate-400"></i>
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
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                        required
                                    >

                                    @error('tahun')
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Sinopsis --}}
                            <div>
                                <label for="synopsis" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <i class="fas fa-align-left text-xs text-slate-400"></i>
                                    Sinopsis
                                    <span class="text-xs font-medium text-slate-400">(Opsional)</span>
                                </label>

                                <textarea
                                    name="synopsis"
                                    id="synopsis"
                                    rows="6"
                                    class="block w-full resize-none rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white/95 shadow-sm xl:sticky xl:top-6">

                        {{-- Upload Header --}}
                        <div class="border-b border-slate-100 bg-white/80 p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                    <i class="fas fa-image"></i>
                                </div>

                                <div>
                                    <h3 class="text-base font-bold text-slate-900">
                                        Foto Sampul
                                    </h3>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                        Upload gambar sampul buku. Format JPG, JPEG, atau PNG.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg" class="hidden">

                            {{-- Preview --}}
                            <div id="preview-container" class="hidden">
                                <div class="rounded-3xl border border-emerald-100 bg-emerald-50/60 p-4">
                                    <div class="relative mx-auto h-64 w-full max-w-[220px] overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-emerald-100">
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
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                                            <i class="fas fa-check-circle"></i>
                                            Foto berhasil dipilih
                                        </span>

                                        <p id="file-name" class="mx-auto mt-2 max-w-xs break-all text-xs text-slate-500"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Upload Area --}}
                            <div
                                id="upload-area"
                                class="group cursor-pointer rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/80 px-5 py-10 text-center transition hover:border-emerald-300 hover:bg-emerald-50/50"
                            >
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-white text-emerald-600 shadow-sm ring-1 ring-emerald-100 transition group-hover:scale-105">
                                    <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                </div>

                                <p class="mt-4 text-sm font-bold text-slate-700">
                                    Upload Foto Buku
                                </p>

                                <p class="mx-auto mt-1 max-w-xs text-xs leading-relaxed text-slate-400">
                                    Klik area ini atau seret file gambar ke sini.
                                </p>

                                <span class="mt-4 inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition group-hover:bg-emerald-700">
                                    Pilih File
                                </span>
                            </div>

                            @error('foto')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Info Box --}}
                            <div class="mt-5 rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                                        <i class="fas fa-circle-info text-xs"></i>
                                    </div>

                                    <div>
                                        <p class="text-sm font-bold text-slate-700">
                                            Catatan
                                        </p>
                                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                            Gunakan foto vertikal agar tampilan sampul buku terlihat lebih rapi di halaman daftar buku.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-slate-100 bg-slate-50/80 p-5">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-1">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-emerald-100 transition hover:-translate-y-0.5 hover:bg-emerald-700"
                                >
                                    <i class="fas fa-save text-xs"></i>
                                    Simpan Buku
                                </button>

                                <a
                                    href="{{ route('books.index') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
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