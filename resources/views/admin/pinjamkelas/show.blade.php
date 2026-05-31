@extends('layouts.admin')

@section('title', 'Input Peminjaman Kelas - ' . $kategori->nama_kategori)
@section('page-title', 'Input Peminjaman Kelas: ' . $kategori->nama_kategori)

@section('content')

@php
    $kelasData = $kategori->kelasData ?? null;

    $kelasLabel = $kelasData
        ? trim(($kelasData->jurusan ?? '') . ' ' . ($kelasData->nama_kelas ?? ''))
        : $kategori->kelas;
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-50">
                    Peminjaman Kelas
                </p>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                    Input Peminjaman Kelas
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                    Tambahkan data peminjaman buku kelas berdasarkan kategori dan siswa yang sudah terdaftar.
                </p>
            </div>

            <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">
                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Kelas
                    </p>

                    <p class="mt-1 truncate text-xl font-extrabold tracking-tight text-white">
                        {{ $kelasLabel }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                    <p class="text-xs font-semibold text-emerald-50">
                        Data Siswa
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                        {{ $siswas->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Form Card --}}
        <div class="xl:col-span-2">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                            Form Peminjaman Kelas
                        </h2>

                        <p class="text-sm text-slate-500">
                            Kategori:
                            <span class="font-semibold text-emerald-700">
                                {{ $kategori->nama_kategori }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="p-5 md:p-6">
                    <form action="{{ route('admin.pinjamkelas.kategori.proses') }}" method="POST" class="space-y-6">
                        @csrf

                        <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">
                        <input type="hidden" name="user_id" id="user_id" required>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <h4 class="text-sm font-bold text-slate-800">
                                Informasi Peminjaman
                            </h4>

                            <p class="mt-1 text-sm leading-relaxed text-slate-500">
                                Klik siswa dari daftar kanan atau pilih dari dropdown, lalu masukkan kode buku.
                            </p>
                        </div>

                        {{-- Pilih Siswa --}}
                        <div>
                            <label for="studentSearchBox" class="mb-2 block text-sm font-semibold text-slate-700">
                                Pilih Siswa <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <button
                                    type="button"
                                    id="studentDropdownButton"
                                    onclick="toggleStudentDropdown()"
                                    class="flex h-12 w-full items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 text-left text-sm text-slate-700 transition hover:bg-white focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                >
                                    <span class="min-w-0 flex-1">
                                        <span id="selectedStudentName" class="block truncate text-sm font-semibold text-slate-400">
                                            Pilih siswa
                                        </span>

                                        <span id="selectedStudentInfo" class="mt-0.5 block truncate text-xs text-slate-400">
                                            Belum ada siswa dipilih
                                        </span>
                                    </span>

                                    <span class="text-xs font-semibold text-slate-400">
                                        Pilih
                                    </span>
                                </button>

                                <div
                                    id="studentDropdown"
                                    class="absolute left-0 right-0 z-50 mt-2 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                                >
                                    <div class="border-b border-slate-100 p-3">
                                        <input
                                            type="text"
                                            id="studentSearchBox"
                                            oninput="filterStudentDropdown()"
                                            placeholder="Cari nama atau NISN..."
                                            class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                        >
                                    </div>

                                    <div class="max-h-72 overflow-y-auto p-2">
                                        @foreach($siswas as $siswa)
                                            <button
                                                type="button"
                                                class="student-option w-full rounded-lg px-3 py-2.5 text-left transition hover:bg-emerald-50"
                                                data-id="{{ $siswa->id }}"
                                                data-name="{{ strtolower($siswa->name) }}"
                                                data-nisn="{{ strtolower($siswa->nomor_identitas) }}"
                                                onclick='chooseStudent(
                                                    @json($siswa->id),
                                                    @json($siswa->name),
                                                    @json($siswa->nomor_identitas)
                                                )'
                                            >
                                                <p class="truncate text-sm font-bold text-slate-800">
                                                    {{ $siswa->name }}
                                                </p>

                                                <p class="mt-0.5 truncate text-xs text-slate-500">
                                                    {{ $siswa->nomor_identitas }}
                                                </p>
                                            </button>
                                        @endforeach

                                        <div id="studentDropdownEmpty" class="hidden px-4 py-8 text-center">
                                            <p class="text-sm font-bold text-slate-600">
                                                Siswa tidak ditemukan
                                            </p>

                                            <p class="mt-1 text-xs text-slate-400">
                                                Coba kata kunci lain.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($siswas->isEmpty())
                                <div class="mt-2 rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-600">
                                    Belum ada data siswa untuk kelas {{ $kelasLabel }}. Tambahkan user dengan role siswa terlebih dahulu.
                                </div>
                            @endif
                        </div>

                        {{-- Kode Buku --}}
                        <div>
                            <label for="kode_buku" class="mb-2 block text-sm font-semibold text-slate-700">
                                Kode Buku <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="kode_buku"
                                id="kode_buku"
                                class="h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-semibold uppercase tracking-wide text-slate-700 placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                placeholder="Masukkan kode buku, contoh: B001"
                                required
                                oninput="this.value = this.value.toUpperCase()"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Pastikan kode buku sesuai dengan label atau barcode buku.
                            </p>
                        </div>

                        {{-- Action Button --}}
                        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                            <a
                                href="{{ route('admin.pinjamkelas.kategori') }}"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Kembali
                            </a>

                            <button
                                type="submit"
                                id="submitButton"
                                disabled
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:bg-slate-100"
                            >
                                Simpan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="xl:col-span-1">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm xl:sticky xl:top-6">

                <div class="border-b border-slate-100 bg-white px-5 py-5">
                    <h3 class="text-base font-bold text-slate-900">
                        Siswa Kelas {{ $kelasLabel }}
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Klik siswa untuk otomatis masuk ke form.
                    </p>
                </div>

                <div class="max-h-[420px] overflow-y-auto p-4">
                    @if($siswas->isEmpty())
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center">
                            <p class="text-sm font-bold text-slate-700">
                                Belum ada siswa
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-slate-400">
                                Tambahkan data siswa sesuai kelas agar bisa melakukan input peminjaman.
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($siswas as $siswa)
                                <div
                                    id="student-card-{{ $siswa->id }}"
                                    onclick='chooseStudent(
                                        @json($siswa->id),
                                        @json($siswa->name),
                                        @json($siswa->nomor_identitas)
                                    )'
                                    class="student-card cursor-pointer rounded-lg border border-slate-200 bg-white p-3 transition hover:border-emerald-300 hover:bg-emerald-50"
                                >
                                    <p class="truncate text-sm font-bold text-slate-800">
                                        {{ $siswa->name }}
                                    </p>

                                    <p class="mt-0.5 truncate text-xs text-slate-500">
                                        {{ $siswa->nomor_identitas }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="border-t border-slate-100 bg-white px-5 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-500">
                            Total Siswa
                        </span>

                        <span class="text-xs font-bold text-slate-700">
                            {{ $siswas->count() }} siswa
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleStudentDropdown() {
        const dropdown = document.getElementById('studentDropdown');
        const searchBox = document.getElementById('studentSearchBox');

        if (!dropdown) {
            return;
        }

        dropdown.classList.toggle('hidden');

        if (!dropdown.classList.contains('hidden') && searchBox) {
            setTimeout(() => searchBox.focus(), 50);
        }
    }

    function closeStudentDropdown() {
        const dropdown = document.getElementById('studentDropdown');

        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }

    function chooseStudent(id, name, nisn) {
        const userInput = document.getElementById('user_id');
        const selectedName = document.getElementById('selectedStudentName');
        const selectedInfo = document.getElementById('selectedStudentInfo');
        const submitButton = document.getElementById('submitButton');
        const kodeBuku = document.getElementById('kode_buku');

        if (userInput) {
            userInput.value = id;
        }

        if (selectedName) {
            selectedName.textContent = name;
            selectedName.classList.remove('text-slate-400');
            selectedName.classList.add('text-slate-800');
        }

        if (selectedInfo) {
            selectedInfo.textContent = nisn;
            selectedInfo.classList.remove('text-slate-400');
            selectedInfo.classList.add('text-slate-500');
        }

        if (submitButton) {
            submitButton.disabled = false;
        }

        document.querySelectorAll('.student-card').forEach(card => {
            card.classList.remove(
                'border-emerald-400',
                'bg-emerald-50',
                'ring-2',
                'ring-emerald-100'
            );
        });

        const activeCard = document.getElementById('student-card-' + id);

        if (activeCard) {
            activeCard.classList.add(
                'border-emerald-400',
                'bg-emerald-50',
                'ring-2',
                'ring-emerald-100'
            );
        }

        closeStudentDropdown();

        if (kodeBuku) {
            kodeBuku.focus();
        }
    }

    function filterStudentDropdown() {
        const input = document.getElementById('studentSearchBox');
        const empty = document.getElementById('studentDropdownEmpty');
        const keyword = input ? input.value.toLowerCase().trim() : '';
        const options = document.querySelectorAll('.student-option');

        let visibleCount = 0;

        options.forEach(option => {
            const name = option.dataset.name || '';
            const nisn = option.dataset.nisn || '';

            if (!keyword || name.includes(keyword) || nisn.includes(keyword)) {
                option.classList.remove('hidden');
                visibleCount++;
            } else {
                option.classList.add('hidden');
            }
        });

        if (empty) {
            empty.classList.toggle('hidden', visibleCount > 0);
        }
    }

    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('studentDropdown');
        const button = document.getElementById('studentDropdownButton');

        if (!dropdown || !button) {
            return;
        }

        if (!dropdown.contains(event.target) && !button.contains(event.target)) {
            closeStudentDropdown();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeStudentDropdown();
        }
    });
</script>

@endsection