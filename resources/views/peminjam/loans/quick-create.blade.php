@extends('layouts.peminjam')

@section('title', 'Input Peminjaman Cepat')
@section('page-title', 'Input Peminjaman Cepat')

@section('content')

@php
    $selectedSiswa = $siswas->firstWhere('id', (int) old('user_id'));
    $selectedBook = $books->firstWhere('id', (int) old('book_id', $selectedBookId));
@endphp

<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <span class="text-sm font-medium">{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">Peminjaman&nbsp;Mandiri</p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Input Peminjaman Cepat
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Cari nama siswa, pilih buku, lalu masukkan kode buku fisik yang tertera di buku. Peminjaman langsung tercatat di sistem perpustakaan.
                </p>
            </div>

            <a href="{{ route('peminjam.list-buku') }}" class="inline-flex h-10 w-fit shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"><i class="fas fa-arrow-left text-xs"></i> <span>Kembali ke Katalog</span></a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
                <div class="border-b border-[var(--hairline)] px-6 py-5">
                    <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                        Form Peminjaman
                    </h2>

                    <p class="mt-1 text-sm text-[var(--muted)]">
                        Pastikan nama siswa dan kode buku fisik sudah benar sebelum menyimpan.
                    </p>
                </div>

                <form action="{{ route('peminjam.loan.quick-store') }}" method="POST" class="space-y-5 p-6" id="form-quick-peminjaman">
                    @csrf

                    {{-- ===================== SISWA (searchable: nama & NISN) ===================== --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[var(--text)]">
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
                                    class="h-12 w-full rounded-xl border border-[var(--hairline)] bg-white px-4 pr-10 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                >
                                <i class="fas fa-magnifying-glass pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-[var(--muted)]"></i>
                            </div>

                            <input type="hidden" name="user_id" data-search-value value="{{ old('user_id') }}">

                            <div data-search-dropdown class="absolute z-20 mt-2 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-[var(--hairline)] bg-white p-1 shadow-lg">
                                @foreach($siswas as $siswa)
                                    <button
                                        type="button"
                                        data-search-option
                                        data-value="{{ $siswa->id }}"
                                        data-label="{{ $siswa->name }} - {{ $siswa->nomor_identitas ?? '-' }}"
                                        data-search="{{ strtolower($siswa->name.' '.($siswa->nomor_identitas ?? '')) }}"
                                        class="flex w-full flex-col rounded-lg px-3 py-2 text-left transition hover:bg-[var(--emerald-tint)] data-[active=true]:bg-[var(--emerald-tint)]"
                                    >
                                        <span class="text-sm font-semibold text-[var(--text)]">{{ $siswa->name }}</span>
                                        <span class="text-xs text-[var(--muted)]">NISN: {{ $siswa->nomor_identitas ?? '-' }}</span>
                                    </button>
                                @endforeach

                                <p data-search-empty class="hidden px-3 py-2 text-sm text-[var(--muted)]">
                                    Siswa tidak ditemukan
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== BUKU (searchable: judul, otomatis kepilih dari katalog) ===================== --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[var(--text)]">
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
                                    class="h-12 w-full rounded-xl border border-[var(--hairline)] bg-white px-4 pr-10 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                >
                                <i class="fas fa-magnifying-glass pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-[var(--muted)]"></i>
                            </div>

                            <input type="hidden" name="book_id" data-search-value value="{{ $selectedBook->id ?? old('book_id') }}">

                            <div data-search-dropdown class="absolute z-20 mt-2 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-[var(--hairline)] bg-white p-1 shadow-lg">
                                @foreach($books as $book)
                                    <button
                                        type="button"
                                        data-search-option
                                        data-value="{{ $book->id }}"
                                        data-label="{{ $book->judul }} - Stok tersedia: {{ $book->stok_tersedia ?? 0 }}"
                                        data-search="{{ strtolower($book->judul) }}"
                                        class="flex w-full flex-col rounded-lg px-3 py-2 text-left transition hover:bg-[var(--emerald-tint)] data-[active=true]:bg-[var(--emerald-tint)]"
                                    >
                                        <span class="text-sm font-semibold text-[var(--text)]">{{ $book->judul }}</span>
                                        <span class="text-xs text-[var(--muted)]">Stok tersedia: {{ $book->stok_tersedia ?? 0 }}</span>
                                    </button>
                                @endforeach

                                <p data-search-empty class="hidden px-3 py-2 text-sm text-[var(--muted)]">
                                    Buku tidak ditemukan
                                </p>
                            </div>
                        </div>

                        @if($selectedBook)
                            <p class="mt-2 text-xs text-[var(--emerald-deep)]">
                                <i class="fas fa-circle-check"></i> Buku otomatis terpilih dari katalog. Bisa diganti dengan mencari judul lain kalau perlu.
                            </p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Kode Buku Fisik <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode_buku"
                            value="{{ old('kode_buku') }}"
                            required
                            placeholder="Masukkan kode buku fisik"
                            class="h-12 w-full rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        >

                        <p class="mt-2 text-xs text-[var(--muted)]">
                            Kode buku harus sesuai dengan label yang ditempel pada buku fisik dan statusnya masih tersedia.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-[var(--hairline)] pt-5 sm:flex-row sm:justify-end">
                        <a href="{{ route('peminjam.list-buku') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-[var(--hairline)] bg-white px-5 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/50">Batal</a>

                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        >
                            <i class="fas fa-save text-xs"></i>
                            <span>Simpan Peminjaman</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--emerald-tint)] p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>
                        <h3 class="font-display text-sm font-semibold text-[var(--forest)]">
                            Cara Kerja
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--emerald-deep)]">
                            Peminjaman langsung berstatus disetujui begitu disimpan.
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--emerald-deep)]">
                            Tanggal kembali otomatis mengikuti setting lama pinjam default ({{ $lamaPinjamDefault }} hari).
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--emerald-deep)]">
                            Data langsung tercatat di sistem perpustakaan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-amber-600 ring-1 ring-amber-100">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-amber-800">
                            Catatan
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-amber-700">
                            Pastikan kode buku fisik sesuai dengan judul yang dipilih, dan hanya bisa dipakai jika buku masih tersedia.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    (function () {
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

        var form = document.getElementById('form-quick-peminjaman');
        if (form) {
            form.addEventListener('submit', function (e) {
                var missing = [];

                document.querySelectorAll('[data-searchable-select]').forEach(function (wrapper) {
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