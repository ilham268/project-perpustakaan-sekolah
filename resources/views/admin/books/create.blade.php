@extends('layouts.admin')

@section('title', 'Tambah Buku')

@section('content')
    <!-- Flash Messages -->
    @if(session('error'))
        <x-flash-message type="error" message="{{ session('error') }}" />
    @endif

    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav class="flex items-center text-sm text-gray-700">
            <a href="{{ route('books.index') }}" class="font-semibold hover:text-cyan-600 transition-colors">Kelola Buku</a>
            <span class="mx-2 font-semibold">/</span>
            <span class="text-cyan-600 font-semibold">Tambah Buku</span>
        </nav>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Tambah Buku</h3>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Judul Buku -->
            <div>
                <label for="judul" class="block text-sm text-gray-700 mb-1.5">
                    Judul Buku <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="judul"
                    id="judul"
                    value="{{ old('judul') }}"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                    placeholder="Masukkan judul buku"
                    required
                >
                @error('judul')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori & Lokasi Rak -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="category_id" class="block text-sm text-gray-700 mb-1.5">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="category_id"
                        id="category_id"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
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
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nomor_rak" class="block text-sm text-gray-700 mb-1.5">
                        Lokasi Rak <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nomor_rak"
                        id="nomor_rak"
                        value="{{ old('nomor_rak') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                        placeholder="Contoh: A1, B2"
                        required
                    >
                    @error('nomor_rak')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Penulis & Penerbit -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="penulis" class="block text-sm text-gray-700 mb-1.5">
                        Penulis <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="penulis"
                        id="penulis"
                        value="{{ old('penulis') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                        placeholder="Nama penulis"
                        required
                    >
                    @error('penulis')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="penerbit" class="block text-sm text-gray-700 mb-1.5">
                        Penerbit <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="penerbit"
                        id="penerbit"
                        value="{{ old('penerbit') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                        placeholder="Nama penerbit"
                        required
                    >
                    @error('penerbit')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tahun Terbit -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="tahun" class="block text-sm text-gray-700 mb-1.5">
                        Tahun Terbit <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="tahun"
                            id="tahun"
                            value="{{ old('tahun', date('Y')) }}"
                            min="1900"
                            max="{{ date('Y') + 1 }}"
                            class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"
                            required
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                        </div>
                    </div>
                    @error('tahun')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sinopsis -->
            <div>
                <label for="synopsis" class="block text-sm text-gray-700 mb-1.5">
                    Sinopsis <span class="text-gray-400 text-xs">(Opsional)</span>
                </label>
                <textarea
                    name="synopsis"
                    id="synopsis"
                    rows="5"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all resize-none text-sm"
                    placeholder="Masukkan deskripsi atau sinopsis buku..."
                >{{ old('synopsis') }}</textarea>
                @error('synopsis')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto -->
            <div>
                <label class="block text-sm text-gray-700 mb-1.5">
                    Foto <span class="text-gray-400 text-xs">(Opsional)</span>
                </label>

                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg" class="hidden">

                <div id="preview-container" class="hidden mb-3">
                    <div class="flex items-start gap-4">
                        <div class="relative w-40 h-40 shrink-0">
                            <img id="preview-image" src="" alt="Preview" class="w-full h-full object-cover rounded-lg border border-gray-300">
                            <button type="button" id="btn-clear-image" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="flex flex-col gap-1.5 pt-1">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full">
                                <i class="fas fa-check-circle"></i>
                                Foto berhasil diunggah
                            </span>
                            <p id="file-name" class="text-xs text-gray-500 break-all max-w-xs"></p>
                        </div>
                    </div>
                </div>

                <div id="upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors hover:border-cyan-400">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-500 mb-3">Seret dan Lepas di sini atau</p>
                    <span class="px-5 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">Pilih File</span>
                </div>

                @error('foto')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-center gap-3 pt-4">
                <a
                    href="{{ route('books.index') }}"
                    class="px-8 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                >
                    Kembali
                </a>
                <button
                    type="submit"
                    class="px-8 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors text-sm font-medium"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script>
        const fileInput   = document.getElementById('foto');
        const uploadArea  = document.getElementById('upload-area');
        const previewCont = document.getElementById('preview-container');
        const previewImg  = document.getElementById('preview-image');
        const fileNameEl  = document.getElementById('file-name');

        function showPreview(file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src         = e.target.result;
                fileNameEl.textContent = file.name;
                previewCont.classList.remove('hidden');
                uploadArea.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        function clearImage() {
            fileInput.value        = '';
            previewImg.src         = '';
            fileNameEl.textContent = '';
            previewCont.classList.add('hidden');
            uploadArea.classList.remove('hidden');
        }

        // Klik area → buka dialog file
        uploadArea.addEventListener('click', function () {
            fileInput.click();
        });

        // File dipilih via dialog
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) showPreview(this.files[0]);
        });

        // Tombol hapus preview
        document.getElementById('btn-clear-image').addEventListener('click', clearImage);

        // Cegah browser buka file di tab baru saat di-drop ke halaman mana pun
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (evt) {
            document.addEventListener(evt, function (e) { e.preventDefault(); }, false);
        });

        // Highlight upload area saat drag masuk
        uploadArea.addEventListener('dragover', function () {
            uploadArea.classList.add('border-cyan-500', 'bg-cyan-50');
        });
        uploadArea.addEventListener('dragleave', function () {
            uploadArea.classList.remove('border-cyan-500', 'bg-cyan-50');
        });

        // Handle drop
        uploadArea.addEventListener('drop', function (e) {
            uploadArea.classList.remove('border-cyan-500', 'bg-cyan-50');
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
