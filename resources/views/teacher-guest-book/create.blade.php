@extends('layouts.peminjam')

@section('title', 'Buku Tamu Guru')
@section('page-title', 'Buku Tamu Guru')

@section('content')

<div class="space-y-5">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-medium">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Kunjungan Guru / Pendidik
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Buku Tamu Guru
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Cari nama guru yang terdaftar, pilih nama, lalu isi keperluan kunjungan.
                </p>
            </div>

            <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-white backdrop-blur-md md:flex">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">

        <div class="border-b border-[var(--hairline)] bg-[var(--emerald-tint)]/60 px-5 py-4">
            <h3 class="font-display text-base font-semibold text-[var(--forest)]">
                Form Buku Tamu Guru
            </h3>
            <p class="mt-1 text-xs text-[var(--muted)]">
                Cari dan pilih nama guru dari data yang tersedia.
            </p>
        </div>

        <div class="p-5 md:p-6">
            <form id="teacherGuestBookForm" action="{{ route('teacher-guest-book.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    <input type="hidden" id="user_id" name="user_id" value="{{ $selectedGuru?->id ?? '' }}">

                    <div>
                        <label for="guruSearch" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Nama Lengkap Guru <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="guruSearch"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="guruSuggestions"
                            autocomplete="off"
                            value="{{ $selectedGuru?->name ?? '' }}"
                            placeholder="Cari nama guru..."
                            class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('user_id') ? 'border-red-500' : '' }}"
                        >

                        <div id="guruSuggestions" role="listbox" class="mt-1 hidden max-h-60 overflow-y-auto rounded-xl border border-[var(--hairline)] bg-white shadow-lg"></div>
                        <p id="guruSearchHint" class="mt-1.5 text-xs text-[var(--muted)]">Ketik minimal 2 karakter untuk mencari guru.</p>

                        @error('user_id')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $oldKeperluan = old('keperluan');
                        $teacherPurposes = [
                            'Berkunjung atau keperluan lainnya', 'Rapat', 'Konsultasi', 'Mengajar',
                            'Mengurus administrasi', 'Bertemu guru/staf', 'Bertemu kepala sekolah',
                            'Mengantar dokumen', 'Keperluan perpustakaan', 'Lainnya',
                        ];
                        $oldIsCustom = $oldKeperluan && !in_array($oldKeperluan, $teacherPurposes, true);
                        $initialKeperluan = $oldIsCustom ? 'Lainnya' : ($oldKeperluan ?: 'Berkunjung atau keperluan lainnya');
                        $customKeperluanVal = old('keperluan_custom', $oldIsCustom ? preg_replace('/^Lainnya:\s*/', '', $oldKeperluan) : '');
                    @endphp

                    <div x-data="{ selectedKeperluan: '{{ $initialKeperluan }}' }">
                        <label for="keperluan" class="mb-2 block text-sm font-semibold text-[var(--text)]">
                            Keperluan <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select
                                id="keperluan"
                                name="keperluan"
                                x-model="selectedKeperluan"
                                required
                                class="w-full appearance-none rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 pr-10 text-sm font-medium text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('keperluan') ? 'border-red-500' : '' }}"
                            >
                                @foreach($teacherPurposes as $purpose)
                                    <option value="{{ $purpose }}" {{ $initialKeperluan === $purpose ? 'selected' : '' }}>{{ $purpose }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-[var(--muted)]">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>

                        {{-- Textarea jika Lainnya dipilih --}}
                        <div x-show="selectedKeperluan === 'Lainnya'" x-cloak class="mt-3">
                            <label for="keperluan_custom" class="mb-1.5 block text-xs font-semibold text-[var(--text)]/80">
                                Tuliskan Keperluan Lainnya <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="keperluan_custom"
                                name="keperluan_custom"
                                rows="3"
                                placeholder="Tuliskan keperluan kunjungan Bapak/Ibu Guru..."
                                :required="selectedKeperluan === 'Lainnya'"
                                class="w-full resize-none rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('keperluan_custom') ? 'border-red-500' : '' }}"
                            >{{ $customKeperluanVal }}</textarea>
                            @error('keperluan_custom')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @error('keperluan')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end border-t border-[var(--hairline)] pt-5">
                        <button
                            type="submit"
                            id="teacherGuestBookSubmit"
                            disabled
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)] disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Kirim Data Kunjungan</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('guruSearch');
    const userIdInput = document.getElementById('user_id');
    const suggestions = document.getElementById('guruSuggestions');
    const hint = document.getElementById('guruSearchHint');
    const submitButton = document.getElementById('teacherGuestBookSubmit');
    const form = document.getElementById('teacherGuestBookForm');
    const searchUrl = @json(route('teacher-guest-book.create'));
    let matches = [];
    let activeIndex = -1;
    let timer = null;
    let requestController = null;

    function setSuggestionsOpen(open) {
        suggestions.classList.toggle('hidden', !open);
        searchInput.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function syncSubmitState() {
        submitButton.disabled = !userIdInput.value;
    }

    function renderSuggestions(items, keepActive = false) {
        const previousActive = activeIndex;
        suggestions.replaceChildren();
        matches = items;
        activeIndex = items.length ? (keepActive ? Math.min(Math.max(previousActive, 0), items.length - 1) : 0) : -1;

        items.forEach((teacher, index) => {
            const option = document.createElement('button');
            option.type = 'button';
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', index === activeIndex ? 'true' : 'false');
            option.className = 'block w-full border-b border-[var(--hairline)] px-4 py-3 text-left text-sm last:border-b-0 hover:bg-[var(--emerald-tint)] focus:bg-[var(--emerald-tint)]';

            const name = document.createElement('span');
            name.className = 'block font-semibold text-[var(--text)]';
            name.textContent = teacher.name;
            option.appendChild(name);

            option.addEventListener('click', () => selectTeacher(index));
            suggestions.appendChild(option);
        });

        if (!items.length) {
            const empty = document.createElement('p');
            empty.className = 'px-4 py-3 text-sm text-[var(--muted)]';
            empty.textContent = 'Guru tidak ditemukan.';
            suggestions.appendChild(empty);
        }
        setSuggestionsOpen(true);
    }

    function selectTeacher(index) {
        const teacher = matches[index];
        if (!teacher) return;
        userIdInput.value = teacher.id;
        searchInput.value = teacher.name;
        hint.textContent = 'Guru terpilih.';
        hint.classList.remove('text-red-600');
        setSuggestionsOpen(false);
        syncSubmitState();
    }

    async function searchTeachers(term) {
        if (requestController) requestController.abort();
        requestController = new AbortController();
        try {
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('q', term);
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: requestController.signal
            });
            if (!response.ok) throw new Error('Pencarian guru gagal. Coba lagi.');
            const data = await response.json();
            if (!Array.isArray(data)) throw new Error('Respons pencarian guru tidak valid.');
            renderSuggestions(data);
            hint.textContent = data.length ? 'Pilih nama guru dari daftar.' : 'Guru tidak ditemukan.';
        } catch (error) {
            if (error.name === 'AbortError') return;
            renderSuggestions([]);
            hint.textContent = error.message || 'Pencarian guru gagal. Periksa koneksi lalu coba lagi.';
            hint.classList.add('text-red-600');
        }
    }

    searchInput.addEventListener('input', function () {
        userIdInput.value = '';
        syncSubmitState();
        hint.classList.remove('text-red-600');
        const term = searchInput.value.trim();
        if (requestController) requestController.abort();
        clearTimeout(timer);
        if (term.length < 2) {
            matches = [];
            setSuggestionsOpen(false);
            hint.textContent = 'Ketik minimal 2 karakter untuk mencari guru.';
            return;
        }
        hint.textContent = 'Mencari guru...';
        timer = setTimeout(() => searchTeachers(term), 250);
    });

    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            event.stopPropagation();
            if (!suggestions.classList.contains('hidden') && activeIndex >= 0) selectTeacher(activeIndex);
            return;
        }
        if (event.key === 'ArrowDown' && matches.length) {
            event.preventDefault();
            activeIndex = (activeIndex + 1) % matches.length;
            renderSuggestions(matches, true);
            suggestions.querySelectorAll('[role="option"]')[activeIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (event.key === 'ArrowUp' && matches.length) {
            event.preventDefault();
            activeIndex = activeIndex <= 0 ? matches.length - 1 : activeIndex - 1;
            renderSuggestions(matches, true);
            suggestions.querySelectorAll('[role="option"]')[activeIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (event.key === 'Escape') {
            setSuggestionsOpen(false);
        }
    });

    form.addEventListener('submit', function (event) {
        if (!userIdInput.value) {
            event.preventDefault();
            hint.textContent = 'Guru belum dipilih.';
            hint.classList.add('text-red-600');
            searchInput.focus();
        }
    });

    syncSubmitState();
});
</script>

@endsection
