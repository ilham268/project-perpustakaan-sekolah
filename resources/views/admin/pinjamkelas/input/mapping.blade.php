@extends('layouts.admin')

@section('title', 'Petakan Mapel ke Buku Paket')
@section('page-title', 'Petakan Mapel ke Buku Paket')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10">
            <p class="catalog-eyebrow font-semibold uppercase text-white/70">Import&nbsp;Excel</p>

            <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                Petakan Mapel ke Buku Paket
            </h1>

            <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                Ditemukan {{ count($unmapped) }} mapel di file Excel yang perlu dipetakan ke Buku Paket. Ketik untuk mencari judul, lalu pilih buku yang sesuai.
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
        <div class="border-b border-[var(--hairline)] px-6 py-5">
            <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                Mapel Belum Dipetakan
            </h2>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Kosongkan pilihan buku kalau mapel ini memang mau dilewati dulu (barisnya akan gagal, bisa diimport ulang nanti).
            </p>
        </div>

        <form action="{{ route('admin.pinjamkelas.import.confirm') }}" method="POST" class="p-6 space-y-5" id="form-mapping">
            @csrf
            <input type="hidden" name="temp_file" value="{{ $tempFile }}">

            @foreach($unmapped as $i => $item)
                <div class="rounded-xl border border-[var(--hairline)] p-4">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3 md:items-start">
                        <div>
                            <p class="text-sm font-semibold text-[var(--text)]">{{ $item['label'] }}</p>
                            <p class="text-xs text-[var(--muted)]">Kelas {{ $item['kelas'] ?? '-' }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <input type="hidden" name="keys[{{ $i }}]" value="{{ $item['key'] }}">

                            <div class="relative" data-searchable-select>
                                <div class="relative">
                                    <input
                                        type="text"
                                        data-search-input
                                        autocomplete="off"
                                        placeholder="Ketik untuk mencari judul buku, atau kosongkan untuk lewati..."
                                        class="h-11 w-full rounded-xl border border-[var(--hairline)] bg-white px-4 pr-10 text-sm font-medium text-[var(--text)] placeholder:text-[var(--muted)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                    >
                                    <i class="fas fa-magnifying-glass pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-[var(--muted)]"></i>
                                </div>

                                <input type="hidden" name="book_ids[{{ $i }}]" data-search-value value="">

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
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end gap-3 border-t border-[var(--hairline)] pt-5">
                <a href="{{ route('admin.pinjamkelas.input-peminjaman') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-[var(--hairline)] bg-white px-5 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/50">Batal</a>

                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]">
                    <i class="fas fa-check text-xs"></i>
                    <span>Simpan Mapping & Lanjutkan Import</span>
                </button>
            </div>
        </form>
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
    })();
</script>

@endsection