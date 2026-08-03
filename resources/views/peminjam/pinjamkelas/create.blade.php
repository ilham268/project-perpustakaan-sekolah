@extends('layouts.peminjam')

@section('title', 'Input Buku Paket')
@section('page-title', 'Input Buku Paket')

@section('content')

@php
    $booksPaket = $booksPaket ?? collect();
    $selectedBook = $booksPaket->firstWhere('id', (int) old('book_id'));
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

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">Peminjaman&nbsp;Buku&nbsp;Paket</p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Input Buku Paket
                </h1>

                <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Pilih judul Buku Paket, lalu masukkan kode buku yang ada pada label buku fisik.
                </p>
            </div>

            <a href="{{ route('siswa.pinjamkelas.index') }}" class="inline-flex h-10 w-fit shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 text-sm font-semibold text-[var(--emerald-deep)] shadow-sm transition hover:bg-white/90 focus:outline-none focus:ring-4 focus:ring-white/30"><i class="fas fa-arrow-left text-xs"></i> <span>Kembali</span></a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
                <div class="border-b border-[var(--hairline)] px-6 py-5">
                    <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                        Form Peminjaman Buku Paket
                    </h2>

                    <p class="mt-1 text-sm text-[var(--muted)]">
                        Kode buku harus sesuai dengan label yang ditempel pada buku.
                    </p>
                </div>

                <div class="p-6">
                    <form action="{{ route('siswa.pinjamkelas.store') }}" method="POST" class="space-y-5" id="form-pinjam-buku-paket">
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[var(--text)]">
                                Pilih Buku Paket <span class="text-red-500">*</span>
                            </label>

                            @if($booksPaket->isNotEmpty())
                                <div class="relative" data-searchable-select>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            data-search-input
                                            autocomplete="off"
                                            placeholder="Ketik untuk mencari judul buku..."
                                            value="{{ $selectedBook->judul ?? '' }}"
                                            class="h-12 w-full rounded-xl border border-[var(--hairline)] bg-white px-4 pr-10 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                        >
                                        <i class="fas fa-magnifying-glass pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-[var(--muted)]"></i>
                                    </div>

                                    <input type="hidden" name="book_id" data-search-value value="{{ old('book_id') }}">

                                    <div data-search-dropdown class="absolute z-20 mt-2 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-[var(--hairline)] bg-white p-1 shadow-lg">
                                        @foreach($booksPaket as $book)
                                            <button
                                                type="button"
                                                data-search-option
                                                data-value="{{ $book->id }}"
                                                data-label="{{ $book->judul }}"
                                                data-search="{{ strtolower($book->judul) }}"
                                                class="flex w-full flex-col rounded-lg px-3 py-2 text-left transition hover:bg-[var(--emerald-tint)] data-[active=true]:bg-[var(--emerald-tint)]"
                                            >
                                                <span class="text-sm font-semibold text-[var(--text)]">{{ $book->judul }}</span>
                                            </button>
                                        @endforeach

                                        <p data-search-empty class="hidden px-3 py-2 text-sm text-[var(--muted)]">
                                            Buku tidak ditemukan
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-2 text-xs text-[var(--muted)]">
                                    Buku yang tampil hanya Buku Paket. Ketik untuk mencari judul.
                                </p>
                            @else
                                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-4">
                                    <p class="text-sm font-semibold text-red-700">
                                        Buku Paket belum tersedia
                                    </p>

                                    <p class="mt-1 text-sm leading-relaxed text-red-600">
                                        Belum ada data Buku Paket yang bisa dipinjam. Silakan hubungi petugas.
                                    </p>
                                </div>
                            @endif

                            @error('book_id')
                                <p class="mt-2 text-xs font-semibold text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[var(--text)]" for="kode_buku">
                                Kode Buku <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="kode_buku"
                                id="kode_buku"
                                value="{{ old('kode_buku') }}"
                                required
                                placeholder="Masukkan kode buku"
                                class="h-12 w-full rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold uppercase tracking-wide text-[var(--text)] placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-[var(--muted)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                oninput="this.value = this.value.toUpperCase()"
                            >

                            <p class="mt-2 text-xs text-[var(--muted)]">
                                Masukkan kode buku sesuai label yang ditempel pada buku fisik.
                            </p>

                            @error('kode_buku')
                                <p class="mt-2 text-xs font-semibold text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-3 border-t border-[var(--hairline)] pt-5 sm:flex-row sm:justify-end">
                            <a href="{{ route('siswa.pinjamkelas.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-[var(--hairline)] bg-white px-5 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/50">Batal</a>

                            <button
                                type="submit"
                                class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)] disabled:cursor-not-allowed disabled:bg-slate-300 disabled:hover:bg-slate-300"
                                {{ $booksPaket->isEmpty() ? 'disabled' : '' }}
                            >
                                <i class="fas fa-save text-xs"></i>
                                <span>Simpan Peminjaman</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--emerald-tint)] p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>
                        <h3 class="font-display text-sm font-semibold text-[var(--forest)]">
                            Cara Peminjaman
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--emerald-deep)]">
                            Ketik judul Buku Paket yang ingin dipinjam untuk mencari lebih cepat.
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--emerald-deep)]">
                            Masukkan kode buku sesuai label fisik.
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--emerald-deep)]">
                            Sistem otomatis mengecek apakah kode valid dan masih tersedia.
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
                            Jika kode buku tidak cocok dengan judul Buku Paket yang dipilih, sistem akan menolak input peminjaman.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    (function () {
        var wrapper = document.querySelector('[data-searchable-select]');

        if (!wrapper) {
            return;
        }

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

        // Validasi ringan sebelum submit
        var form = document.getElementById('form-pinjam-buku-paket');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!hidden.value) {
                    e.preventDefault();
                    input.classList.add('border-red-400', 'ring-4', 'ring-red-100');
                    input.focus();
                } else {
                    input.classList.remove('border-red-400', 'ring-4', 'ring-red-100');
                }
            });
        }
    })();
</script>

@endsection