@extends('layouts.admin')

@section('title', 'Input Peminjaman Buku')
@section('page-title', 'Input Peminjaman Buku')

@section('content')

@php
    $tanggalPinjamDefault = old('tanggal_pinjam', now()->format('Y-m-d'));
    $tanggalKembaliDefault = old('tanggal_kembali', now()->addDays($lamaPinjamDefault ?? 7)->format('Y-m-d'));

    $selectedSiswa = $siswas->firstWhere('id', (int) old('user_id'));
    $selectedBook = $books->firstWhere('id', (int) old('book_id'));
@endphp

<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ session('error') }}
            </span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">
                {{ $errors->first() }}
            </span>
        </div>
    @endif

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Peminjaman Manual
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Input Peminjaman Buku
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">
                    Admin dapat menginput peminjaman buku untuk siswa secara langsung tanpa menunggu siswa mengajukan.
                </p>
            </div>

            <a
                href="{{ route('admin.peminjaman.index') }}"
                class="inline-flex h-10 w-fit items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-white px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30"
            >
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-6 py-5">
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Form Input Peminjaman
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih siswa, pilih buku, masukkan kode buku fisik, lalu simpan peminjaman.
                    </p>
                </div>

                <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="space-y-5 p-6" id="form-peminjaman">
                    @csrf

                    {{-- ===================== SISWA (searchable: nama & NISN) ===================== --}}
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Siswa <span class="text-red-500">*</span>
                        </label>

                        <div class="relative" data-searchable-select>
                            <div class="relative">
                                <input
                                    type="text"
                                    data-search-input
                                    autocomplete="off"
                                    placeholder="Cari nama atau NISN siswa..."
                                    value="{{ $selectedSiswa ? $selectedSiswa->name.' - '.($selectedSiswa->nomor_identitas ?? '-') : '' }}"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 pr-10 text-sm font-medium text-slate-700 placeholder:text-slate-400 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                                >
                                <i class="fas fa-magnifying-glass pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            </div>

                            <input type="hidden" name="user_id" data-search-value value="{{ old('user_id') }}">

                            <div
                                data-search-dropdown
                                class="absolute z-20 mt-2 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-lg"
                            >
                                @foreach($siswas as $siswa)
                                    <button
                                        type="button"
                                        data-search-option
                                        data-value="{{ $siswa->id }}"
                                        data-label="{{ $siswa->name }} - {{ $siswa->nomor_identitas ?? '-' }}"
                                        data-search="{{ strtolower($siswa->name.' '.($siswa->nomor_identitas ?? '')) }}"
                                        class="flex w-full flex-col rounded-lg px-3 py-2 text-left transition hover:bg-emerald-50 data-[active=true]:bg-emerald-50"
                                    >
                                        <span class="text-sm font-semibold text-slate-700">{{ $siswa->name }}</span>
                                        <span class="text-xs text-slate-400">NISN: {{ $siswa->nomor_identitas ?? '-' }}</span>
                                    </button>
                                @endforeach

                                <p data-search-empty class="hidden px-3 py-2 text-sm text-slate-400">
                                    Siswa tidak ditemukan
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== BUKU (searchable: judul) ===================== --}}
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Buku Referensi <span class="text-red-500">*</span>
                        </label>

                        <div class="relative" data-searchable-select>
                            <div class="relative">
                                <input
                                    type="text"
                                    data-search-input
                                    autocomplete="off"
                                    placeholder="Cari judul buku..."
                                    value="{{ $selectedBook ? $selectedBook->judul.' - Stok tersedia: '.($selectedBook->stok_tersedia ?? 0) : '' }}"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 pr-10 text-sm font-medium text-slate-700 placeholder:text-slate-400 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                                >
                                <i class="fas fa-magnifying-glass pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            </div>

                            <input type="hidden" name="book_id" data-search-value value="{{ old('book_id') }}">

                            <div
                                data-search-dropdown
                                class="absolute z-20 mt-2 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-lg"
                            >
                                @foreach($books as $book)
                                    <button
                                        type="button"
                                        data-search-option
                                        data-value="{{ $book->id }}"
                                        data-label="{{ $book->judul }} - Stok tersedia: {{ $book->stok_tersedia ?? 0 }}"
                                        data-search="{{ strtolower($book->judul) }}"
                                        class="flex w-full flex-col rounded-lg px-3 py-2 text-left transition hover:bg-emerald-50 data-[active=true]:bg-emerald-50"
                                    >
                                        <span class="text-sm font-semibold text-slate-700">{{ $book->judul }}</span>
                                        <span class="text-xs text-slate-400">Stok tersedia: {{ $book->stok_tersedia ?? 0 }}</span>
                                    </button>
                                @endforeach

                                <p data-search-empty class="hidden px-3 py-2 text-sm text-slate-400">
                                    Buku tidak ditemukan
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Kode Buku Fisik <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode_buku"
                            value="{{ old('kode_buku') }}"
                            required
                            placeholder="Masukkan kode buku fisik"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 placeholder:text-slate-400 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                        >

                        <p class="mt-2 text-xs text-slate-400">
                            Kode buku harus sesuai dengan judul buku yang dipilih dan statusnya masih tersedia.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Tanggal Pinjam <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_pinjam"
                                id="tanggal_pinjam"
                                value="{{ $tanggalPinjamDefault }}"
                                required
                                class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Tanggal Kembali <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_kembali"
                                id="tanggal_kembali"
                                value="{{ $tanggalKembaliDefault }}"
                                required
                                class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                            >

                            <p class="mt-2 text-xs text-slate-400">
                                Otomatis {{ $lamaPinjamDefault ?? 7 }} hari dari tanggal pinjam, tapi tetap bisa diedit.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                        <a
                            href="{{ route('admin.peminjaman.index') }}"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                            <i class="fas fa-save text-xs"></i>
                            <span>Simpan Peminjaman</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="rounded-3xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-blue-800">
                            Cara Kerja
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-blue-700">
                            Peminjaman yang diinput admin akan langsung berstatus disetujui.
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-blue-700">
                            Kode buku yang dipilih otomatis berubah menjadi dipinjam.
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-blue-700">
                            Tanggal kembali mengikuti setting lama pinjam default.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4 rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-amber-800">
                            Catatan
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-amber-700">
                            Jika kode buku tidak cocok dengan judul buku, sistem akan menolak input peminjaman.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    (function () {
        // ---------- Tanggal kembali otomatis ----------
        var tanggalPinjam = document.getElementById('tanggal_pinjam');
        var tanggalKembali = document.getElementById('tanggal_kembali');
        var lamaPinjamDefault = {{ (int) ($lamaPinjamDefault ?? 7) }};

        if (tanggalPinjam && tanggalKembali) {
            tanggalPinjam.addEventListener('change', function () {
                if (!tanggalPinjam.value) {
                    return;
                }

                var date = new Date(tanggalPinjam.value);
                date.setDate(date.getDate() + lamaPinjamDefault);

                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');

                tanggalKembali.value = year + '-' + month + '-' + day;
            });
        }

        // ---------- Searchable select (siswa & buku) ----------
        var wrappers = document.querySelectorAll('[data-searchable-select]');

        wrappers.forEach(function (wrapper) {
            var input = wrapper.querySelector('[data-search-input]');
            var hidden = wrapper.querySelector('[data-search-value]');
            var dropdown = wrapper.querySelector('[data-search-dropdown]');
            var options = Array.prototype.slice.call(wrapper.querySelectorAll('[data-search-option]'));
            var emptyMsg = wrapper.querySelector('[data-search-empty]');
            var activeIndex = -1;

            function visibleOptions() {
                return options.filter(function (opt) {
                    return opt.style.display !== 'none';
                });
            }

            function setActive(index) {
                var visible = visibleOptions();
                visible.forEach(function (opt) { opt.removeAttribute('data-active'); });
                activeIndex = index;
                if (visible[index]) {
                    visible[index].setAttribute('data-active', 'true');
                    visible[index].scrollIntoView({ block: 'nearest' });
                }
            }

            function openDropdown() {
                dropdown.classList.remove('hidden');
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
                activeIndex = -1;
            }

            function filterOptions() {
                var query = input.value.trim().toLowerCase();
                var matches = 0;

                options.forEach(function (opt) {
                    var isMatch = opt.getAttribute('data-search').indexOf(query) !== -1;
                    opt.style.display = isMatch ? '' : 'none';
                    if (isMatch) matches++;
                });

                if (emptyMsg) {
                    emptyMsg.classList.toggle('hidden', matches !== 0);
                }

                setActive(-1);
            }

            function selectOption(opt) {
                hidden.value = opt.getAttribute('data-value');
                input.value = opt.getAttribute('data-label');
                closeDropdown();
            }

            input.addEventListener('focus', function () {
                filterOptions();
                openDropdown();
            });

            input.addEventListener('input', function () {
                // Mengetik ulang berarti pilihan sebelumnya dianggap belum final
                hidden.value = '';
                filterOptions();
                openDropdown();
            });

            input.addEventListener('keydown', function (e) {
                var visible = visibleOptions();

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    openDropdown();
                    setActive(Math.min(activeIndex + 1, visible.length - 1));
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    setActive(Math.max(activeIndex - 1, 0));
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeIndex >= 0 && visible[activeIndex]) {
                        selectOption(visible[activeIndex]);
                    }
                } else if (e.key === 'Escape') {
                    closeDropdown();
                }
            });

            options.forEach(function (opt) {
                opt.addEventListener('click', function () {
                    selectOption(opt);
                });
            });

            document.addEventListener('click', function (e) {
                if (!wrapper.contains(e.target)) {
                    closeDropdown();
                }
            });
        });

        // ---------- Validasi ringan sebelum submit ----------
        var form = document.getElementById('form-peminjaman');
        if (form) {
            form.addEventListener('submit', function (e) {
                var missing = [];

                wrappers.forEach(function (wrapper) {
                    var hidden = wrapper.querySelector('[data-search-value]');
                    var input = wrapper.querySelector('[data-search-input]');
                    if (!hidden.value) {
                        missing.push(input);
                        input.classList.add('border-red-400', 'ring-4', 'ring-red-100');
                    } else {
                        input.classList.remove('border-red-400', 'ring-4', 'ring-red-100');
                    }
                });

                if (missing.length > 0) {
                    e.preventDefault();
                    missing[0].focus();
                }
            });
        }
    })();
</script>

@endsection