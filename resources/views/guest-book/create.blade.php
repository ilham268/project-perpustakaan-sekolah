@extends('layouts.peminjam')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')

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

    <div
        class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7"
        style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);"
    >

        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                    Kunjungan&nbsp;Perpustakaan
                </p>

                <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                    Buku Tamu
                </h1>

                <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                    Silakan melakukan absensi menggunakan QR Code atau mengisi buku tamu secara manual.
                </p>
            </div>

            <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-white backdrop-blur-md md:flex">
                <i class="fas fa-book-open-reader text-2xl"></i>
            </div>

        </div>
    </div>

    <div class="mx-auto max-w-3xl">

        <div class="mb-4">
            <h2 class="font-display text-lg font-semibold text-[var(--forest)]">
                Pilih Metode Absensi
            </h2>

            <p class="mt-1 text-sm text-[var(--muted)]">
                Scan QR Code siswa untuk absensi cepat atau gunakan form manual jika diperlukan.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

            <button
                type="button"
                id="btnModeRfid"
                onclick="showRfidMode()"
                class="group rounded-2xl border-2 border-[var(--emerald)] bg-[var(--emerald-tint)] p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
            >

                <div class="flex items-start gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[var(--emerald-deep)] text-white">
                        <i class="fas fa-qrcode text-xl"></i>
                    </div>

                    <div>
                        <h3 class="font-display text-base font-semibold text-[var(--forest)]">
                            Absensi QR Code
                        </h3>

                        <p class="mt-1 text-xs leading-relaxed text-[var(--muted)]">
                            Pindai QR Code siswa menggunakan kamera untuk melakukan absensi.
                        </p>
                    </div>

                </div>

            </button>

            <button
                type="button"
                id="btnModeManual"
                onclick="showManualMode()"
                class="group rounded-2xl border border-[var(--hairline)] bg-white p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-[var(--emerald)] hover:shadow-md"
            >

                <div class="flex items-start gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[var(--paper)] text-[var(--forest)]">
                        <i class="fas fa-pen-to-square text-xl"></i>
                    </div>

                    <div>
                        <h3 class="font-display text-base font-semibold text-[var(--text)]">
                            Absensi Manual
                        </h3>

                        <p class="mt-1 text-xs leading-relaxed text-[var(--muted)]">
                            Isi form buku tamu secara manual.
                        </p>
                    </div>

                </div>

            </button>

        </div>

    </div>

    <div
        id="rfidPanel"
        class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm"
    >

        <div class="border-b border-[var(--hairline)] bg-[var(--emerald-tint)]/60 px-5 py-4">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--emerald-deep)] text-white">
                    <i class="fas fa-qrcode"></i>
                </div>

                <div>
                    <h3 class="font-display text-base font-semibold text-[var(--forest)]">
                        Absensi QR Code
                    </h3>

                    <p class="mt-1 text-xs text-[var(--muted)]">
                        Arahkan kamera ke QR Code siswa untuk absensi otomatis.
                    </p>
                </div>

            </div>

        </div>

        <div class="p-6">

            {{-- Keperluan Kunjungan --}}
            <div class="mb-5">
                <label
                    for="qrKeperluan"
                    class="mb-2 block text-sm font-semibold text-[var(--text)]"
                >
                    Keperluan Kunjungan
                </label>
                <div class="relative">
                    <select
                        id="qrKeperluan"
                        class="w-full appearance-none rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 pr-10 text-sm font-medium text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    >
                        <option value="Berkunjung atau keperluan lainnya" selected>Berkunjung atau keperluan lainnya</option>
                        <option value="Meminjam buku">Meminjam buku</option>
                        <option value="Mengembalikan buku">Mengembalikan buku</option>
                        <option value="Membaca buku di tempat">Membaca buku di tempat</option>
                        <option value="Belajar atau mengerjakan tugas">Belajar atau mengerjakan tugas</option>
                        <option value="Mencari referensi atau informasi">Mencari referensi atau informasi</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-[var(--muted)]">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Controls & Camera Status --}}
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="btnStartScan"
                        class="inline-flex items-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    >
                        <i class="fas fa-camera"></i>
                        <span>Mulai Scan</span>
                    </button>

                    <button
                        type="button"
                        id="btnStopScan"
                        class="hidden inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                    >
                        <i class="fas fa-stop"></i>
                        <span>Hentikan Kamera</span>
                    </button>
                </div>

                <div
                    id="cameraStatusBadge"
                    class="inline-flex items-center gap-2 rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-3.5 py-2 text-xs font-medium text-[var(--muted)]"
                >
                    <span id="cameraStatusDot" class="h-2 w-2 rounded-full bg-gray-400"></span>
                    <span id="cameraStatusText">Kamera nonaktif</span>
                </div>
            </div>

            {{-- QR Reader Box --}}
            <div class="relative mx-auto max-w-lg overflow-hidden rounded-2xl border-2 border-dashed border-[var(--emerald)]/40 bg-[var(--paper)] p-3">
                <div id="qr-reader" class="mx-auto overflow-hidden rounded-xl"></div>
                <div
                    id="scannerPlaceholder"
                    class="flex flex-col items-center justify-center py-12 text-center"
                >
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--emerald-tint)] text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/20">
                        <i class="fas fa-qrcode text-3xl"></i>
                    </div>
                    <p class="mt-4 text-base font-semibold text-[var(--forest)]">Kamera Belum Aktif</p>
                    <p class="mt-1 max-w-xs text-xs text-[var(--muted)]">
                        Klik tombol <strong>Mulai Scan</strong> di atas untuk mengaktifkan webcam dan memindai QR Code siswa.
                    </p>
                </div>
            </div>

            {{-- Panduan Pemindaian Kartu --}}
            <div class="mt-4 rounded-xl border border-[var(--hairline)] bg-[var(--paper)] p-3.5 text-xs text-[var(--muted)]">
                <div class="flex items-center gap-2 font-semibold text-[var(--text)]">
                    <i class="fas fa-lightbulb text-amber-500"></i>
                    <span>Panduan Pemindaian Kartu:</span>
                </div>
                <ul class="mt-2 grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                    <li class="flex items-start gap-1.5">
                        <i class="fas fa-arrows-left-right-to-line mt-0.5 text-[10px] text-[var(--emerald-deep)]"></i>
                        <span><strong>Jarak Ideal:</strong> 15 &ndash; 25 cm dari lensa kamera.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <i class="fas fa-sun mt-0.5 text-[10px] text-[var(--emerald-deep)]"></i>
                        <span><strong>Pencahayaan:</strong> Hindari pantulan kilau cahaya pada kartu.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <i class="fas fa-crop-simple mt-0.5 text-[10px] text-[var(--emerald-deep)]"></i>
                        <span><strong>Posisi:</strong> Pastikan QR berada di area pemindaian.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <i class="fas fa-magnifying-glass-plus mt-0.5 text-[10px] text-[var(--emerald-deep)]"></i>
                        <span><strong>Jika Terlalu Kecil:</strong> Dekatkan kartu perlahan ke kamera.</span>
                    </li>
                </ul>
            </div>

            {{-- Scanning Status / Alert message --}}
            <div
                id="scanStatusAlert"
                class="mt-4 hidden rounded-xl border px-4 py-3 text-sm transition-all"
            ></div>

            {{-- Success Panel --}}
            <div
                id="rfidSuccess"
                class="mt-5 hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm"
            >
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-circle-check text-xl"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700">
                                Absensi Berhasil
                            </span>
                            <span id="cooldownCountdownBadge" class="hidden rounded-md bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                Cooldown: <span id="cooldownSec">5</span>s
                            </span>
                        </div>

                        <h3
                            id="successNama"
                            class="mt-2 text-lg font-bold text-[var(--forest)]"
                        >
                            -
                        </h3>

                        <div class="mt-2 space-y-1 text-sm text-[var(--muted)]">
                            <p>
                                NISN/Identitas:
                                <strong id="successIdentitas" class="text-[var(--text)]">-</strong>
                            </p>
                            <p>
                                Kelas:
                                <strong id="successKelas" class="text-[var(--text)]">-</strong>
                            </p>

                            <p>
                                Jurusan:
                                <strong id="successJurusan" class="text-[var(--text)]">-</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dummy compatibility container for RFID fallback elements --}}
            <div id="rfidStatus" class="hidden">
                <span id="rfidIcon"></span>
                <span id="rfidTitle"></span>
                <span id="rfidMessage"></span>
            </div>

            <div
                id="rfidRegister"
                class="mt-5 hidden rounded-2xl border border-amber-200 bg-amber-50 p-5"
            >

                <div class="flex items-start gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <i class="fas fa-id-card text-xl"></i>
                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">
                            Kartu Belum Terdaftar
                        </p>

                        <h3 class="mt-1 text-lg font-bold text-[var(--forest)]">
                            Siapa Kamu?
                        </h3>

                        <p class="mt-1 text-sm leading-relaxed text-[var(--muted)]">
                            Kartu ini belum terhubung dengan data siswa.
                            Silakan pilih identitas kamu untuk mendaftarkan kartu.
                        </p>

                    </div>

                </div>

                <div class="mt-5">

                    <label
                        for="rfid_uid"
                        class="mb-2 block text-sm font-semibold text-[var(--text)]"
                    >
                        UID Kartu
                    </label>

                    <input
                        type="text"
                        id="rfid_uid"
                        readonly
                        placeholder="UID kartu akan muncul setelah kartu dibaca"
                        class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none"
                    >

                </div>

                <div class="mt-5">

                    <label
                        for="rfidSiswaSearch"
                        class="mb-2 block text-sm font-semibold text-[var(--text)]"
                    >
                        Cari Nama Siswa
                    </label>

                    <input
                        type="text"
                        id="rfidSiswaSearch"
                        placeholder="Ketik nama siswa..."
                        autocomplete="off"
                        class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    >

                </div>

                <div
                    id="rfidSiswaList"
                    class="mt-3 max-h-64 space-y-2 overflow-y-auto"
                >

                    @foreach($siswaList as $siswa)

                        <button
                            type="button"
                            class="rfid-siswa-item hidden w-full rounded-xl border border-[var(--hairline)] bg-white p-4 text-left transition hover:border-[var(--emerald)] hover:bg-[var(--emerald-tint)]"
                            data-id="{{ $siswa->id }}"
                            data-name="{{ strtolower($siswa->name) }}"
                            data-nama="{{ $siswa->name }}"
                            data-identitas="{{ $siswa->nomor_identitas }}"
                            data-kelas="{{ $siswa->kelas }}"
                            data-jurusan="{{ $siswa->jurusan }}"
                        >

                            <div class="flex items-center justify-between gap-3">

                                <div>

                                    <p class="font-semibold text-[var(--text)]">
                                        {{ $siswa->name }}
                                    </p>

                                    <p class="mt-1 text-xs text-[var(--muted)]">
                                        {{ \App\Models\Kelas::formatLabel($siswa->kelas, $siswa->jurusan) ?: '-' }}
                                    </p>

                                </div>

                                <i class="fas fa-chevron-right text-xs text-[var(--muted)]"></i>

                            </div>

                        </button>

                    @endforeach

                </div>

                <div
                    id="rfidSelectedStudent"
                    class="mt-4 hidden rounded-xl border border-[var(--emerald)] bg-white p-4"
                >

                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--muted)]">
                        Siswa Terpilih
                    </p>

                    <p
                        id="selectedStudentName"
                        class="mt-1 font-bold text-[var(--forest)]"
                    >
                        -
                    </p>

                    <p
                        id="selectedStudentDetail"
                        class="mt-1 text-xs text-[var(--muted)]"
                    >
                        -
                    </p>

                </div>

                <div class="mt-5">

                    <label
                        for="rfidNomorIdentitas"
                        class="mb-2 block text-sm font-semibold text-[var(--text)]"
                    >
                        Nomor Identitas (NIS/NISN)
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="rfidNomorIdentitas"
                        placeholder="Masukkan NIS/NISN untuk verifikasi"
                        autocomplete="off"
                        disabled
                        class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition disabled:cursor-not-allowed disabled:bg-gray-100 focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    >

                    <p class="mt-2 text-xs text-[var(--muted)]">
                        Nomor identitas digunakan untuk memastikan kartu didaftarkan kepada siswa yang benar.
                    </p>

                </div>

                <div
                    id="rfidRegisterMessage"
                    class="mt-4 hidden rounded-xl border px-4 py-3 text-sm"
                ></div>

                <div class="mt-5 flex justify-end">

                    <button
                        type="button"
                        id="btnRegisterRfid"
                        disabled
                        class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-gray-300 px-6 py-2.5 text-sm font-semibold text-white transition"
                    >
                        <i class="fas fa-link text-xs"></i>
                        <span>Daftarkan Kartu</span>
                    </button>

                </div>

            </div>

        </div>

    </div>

    <div
        id="manualPanel"
        class="mx-auto hidden max-w-3xl overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm"
    >

        <div class="border-b border-[var(--hairline)] bg-[var(--emerald-tint)]/60 px-5 py-4">

            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-display text-base font-semibold text-[var(--forest)]">
                        Form Buku Tamu
                    </h3>

                    <p class="mt-1 text-xs text-[var(--muted)]">
                        Isi data kunjungan perpustakaan secara manual.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="showRfidMode()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--hairline)] bg-white px-3.5 py-1.5 text-xs font-semibold text-[var(--text)] shadow-sm transition hover:bg-[var(--sand)]/60"
                >
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali</span>
                </button>
            </div>

        </div>

        <div class="p-5 md:p-6">

            <form action="{{ route('guest-book.store') }}" method="POST">

                @csrf

                <div class="space-y-5">

                    @if(auth()->check())

                        <div class="rounded-xl border border-[var(--hairline)] bg-[var(--paper)] p-4">

                            <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--muted)]">
                                Identitas Pengunjung
                            </label>

                            <p class="mt-1 text-base font-bold text-[var(--text)]">
                                {{ auth()->user()->name ?? auth()->user()->nama }}
                            </p>

                            <div class="mt-2 flex gap-4 text-xs font-medium text-[var(--muted)]">

                                <span>
                                    Kelas:
                                    <strong class="text-[var(--text)]">
                                        {{ \App\Models\Kelas::formatLabel(auth()->user()->kelas, auth()->user()->jurusan) ?: '-' }}
                                    </strong>
                                </span>

                                <span>
                                    Jurusan:
                                    <strong class="text-[var(--text)]">
                                        {{ auth()->user()->jurusan ?? '-' }}
                                    </strong>
                                </span>

                            </div>

                        </div>

                        <input
                            type="hidden"
                            name="nama"
                            value="{{ auth()->user()->name ?? auth()->user()->nama }}"
                        >

                        <input
                            type="hidden"
                            name="kelas"
                            value="{{ auth()->user()->kelas }}"
                        >

                        <input
                            type="hidden"
                            name="jurusan"
                            value="{{ auth()->user()->jurusan }}"
                        >

                    @else

                        <div>

                            <label
                                for="nama"
                                class="mb-2 block text-sm font-semibold text-[var(--text)]"
                            >
                                Nama Lengkap
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                list="siswa_list"
                                autocomplete="off"
                                value="{{ old('nama') }}"
                                placeholder="Ketik nama Anda di sini..."
                                required
                                class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('nama') ? 'border-red-500' : '' }}"
                            >

                            <datalist id="siswa_list">

                                @foreach($siswaList as $siswa)

                                    <option
                                        value="{{ $siswa->name }}"
                                        data-kelas="{{ $siswa->kelas }}"
                                        data-jurusan="{{ $siswa->jurusan }}"
                                    ></option>

                                @endforeach

                            </datalist>

                            @error('nama')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div>

                                <label
                                    for="kelas"
                                    class="mb-2 block text-sm font-semibold text-[var(--text)]"
                                >
                                    Kelas
                                </label>

                                <input
                                    type="text"
                                    id="kelas"
                                    name="kelas"
                                    value="{{ old('kelas') }}"
                                    placeholder="Contoh: XI TOI 2"
                                    class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                >

                            </div>

                            <div>

                                <label
                                    for="jurusan"
                                    class="mb-2 block text-sm font-semibold text-[var(--text)]"
                                >
                                    Jurusan
                                </label>

                                <input
                                    type="text"
                                    id="jurusan"
                                    name="jurusan"
                                    value="{{ old('jurusan') }}"
                                    placeholder="Contoh: Teknik Otomasi"
                                    class="w-full rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
                                >

                            </div>

                        </div>

                    @endif

                    @php
                        $oldKeperluan = old('keperluan');
                        $isOldCustom = $oldKeperluan && !in_array($oldKeperluan, [
                            'Meminjam buku',
                            'Mengembalikan buku',
                            'Membaca buku di tempat',
                            'Belajar atau mengerjakan tugas',
                            'Mencari referensi atau informasi',
                            'Berkunjung atau keperluan lainnya',
                        ]);
                        $initialKeperluan = $isOldCustom ? 'Lainnya' : ($oldKeperluan ?: 'Meminjam buku');
                        $customKeperluanVal = old('keperluan_custom', ($isOldCustom ? preg_replace('/^Lainnya:\s*/', '', $oldKeperluan) : ''));
                    @endphp

                    <div x-data="{ selectedKeperluan: '{{ $initialKeperluan }}' }">

                        <label
                            for="keperluan"
                            class="mb-2 block text-sm font-semibold text-[var(--text)]"
                        >
                            Keperluan
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select
                                id="keperluan"
                                name="keperluan"
                                x-model="selectedKeperluan"
                                required
                                class="w-full appearance-none rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 pr-10 text-sm font-medium text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('keperluan') ? 'border-red-500' : '' }}"
                            >
                                <option value="Meminjam buku">Meminjam buku</option>
                                <option value="Mengembalikan buku">Mengembalikan buku</option>
                                <option value="Membaca buku di tempat">Membaca buku di tempat</option>
                                <option value="Belajar atau mengerjakan tugas">Belajar atau mengerjakan tugas</option>
                                <option value="Mencari referensi atau informasi">Mencari referensi atau informasi</option>
                                <option value="Berkunjung atau keperluan lainnya">Berkunjung atau keperluan lainnya</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-[var(--muted)]">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>

                        {{-- Textarea jika Lainnya dipilih --}}
                        <div x-show="selectedKeperluan === 'Lainnya'" x-cloak class="mt-3">
                            <label
                                for="keperluan_custom"
                                class="mb-1.5 block text-xs font-semibold text-[var(--text)]/80"
                            >
                                Tuliskan Keperluan Lainnya <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="keperluan_custom"
                                name="keperluan_custom"
                                rows="3"
                                placeholder="Tuliskan keperluan kunjungan Anda..."
                                :required="selectedKeperluan === 'Lainnya'"
                                class="w-full resize-none rounded-xl border border-[var(--hairline)] bg-white px-4 py-2.5 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)] {{ $errors->has('keperluan_custom') ? 'border-red-500' : '' }}"
                            >{{ $customKeperluanVal }}</textarea>
                            @error('keperluan_custom')
                                <p class="mt-1.5 text-xs font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        @error('keperluan')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-[var(--hairline)] pt-5">

                        <button
                            type="button"
                            onclick="showRfidMode()"
                            class="inline-flex items-center justify-center rounded-xl border border-[var(--hairline)] bg-white px-5 py-2.5 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/60"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                        >
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Simpan Kunjungan</span>
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<style>
    #qr-reader {
        width: 100% !important;
        border: none !important;
    }
    #qr-reader video {
        border-radius: 0.85rem !important;
        width: 100% !important;
        max-height: 440px !important;
        object-fit: cover !important;
    }
    #qr-reader__scan_region {
        border-radius: 0.85rem !important;
        overflow: hidden !important;
    }
    #qr-reader__dashboard {
        display: none !important;
    }
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const rfidPanel = document.getElementById('rfidPanel');
    const manualPanel = document.getElementById('manualPanel');

    const btnModeRfid = document.getElementById('btnModeRfid');
    const btnModeManual = document.getElementById('btnModeManual');

    const rfidStatus = document.getElementById('rfidStatus');
    const rfidIcon = document.getElementById('rfidIcon');
    const rfidTitle = document.getElementById('rfidTitle');
    const rfidMessage = document.getElementById('rfidMessage');

    const rfidSuccess = document.getElementById('rfidSuccess');
    const rfidRegister = document.getElementById('rfidRegister');

    const rfidUid = document.getElementById('rfid_uid');

    const rfidSiswaSearch = document.getElementById('rfidSiswaSearch');
    const rfidSiswaItems = document.querySelectorAll('.rfid-siswa-item');

    const rfidSelectedStudent = document.getElementById('rfidSelectedStudent');
    const selectedStudentName = document.getElementById('selectedStudentName');
    const selectedStudentDetail = document.getElementById('selectedStudentDetail');

    const rfidNomorIdentitas = document.getElementById('rfidNomorIdentitas');

    const rfidRegisterMessage = document.getElementById('rfidRegisterMessage');

    const btnRegisterRfid = document.getElementById('btnRegisterRfid');

    // QR Scanner Elements
    const btnStartScan = document.getElementById('btnStartScan');
    const btnStopScan = document.getElementById('btnStopScan');
    const cameraStatusBadge = document.getElementById('cameraStatusBadge');
    const cameraStatusDot = document.getElementById('cameraStatusDot');
    const cameraStatusText = document.getElementById('cameraStatusText');
    const scannerPlaceholder = document.getElementById('scannerPlaceholder');
    const scanStatusAlert = document.getElementById('scanStatusAlert');
    const qrKeperluan = document.getElementById('qrKeperluan');
    const cooldownCountdownBadge = document.getElementById('cooldownCountdownBadge');
    const cooldownSec = document.getElementById('cooldownSec');

    let selectedStudentId = null;
    let html5QrCode = null;
    let isScanning = false;
    let isStartingCamera = false;
    let isProcessing = false;
    let isCoolingDown = false;
    let lastScannedCode = null;
    let lastScanTime = 0;
    const COOLDOWN_MS = 2500;
    let cooldownTimeout = null;
    let cooldownInterval = null;

    function showScanAlert(message, type = 'error') {
        if (!scanStatusAlert) return;
        scanStatusAlert.classList.remove(
            'hidden',
            'border-red-200', 'bg-red-50', 'text-red-700',
            'border-amber-200', 'bg-amber-50', 'text-amber-800',
            'border-emerald-200', 'bg-emerald-50', 'text-emerald-700',
            'border-blue-200', 'bg-blue-50', 'text-blue-700'
        );

        if (type === 'error') {
            scanStatusAlert.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
        } else if (type === 'warning') {
            scanStatusAlert.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-800');
        } else if (type === 'info') {
            scanStatusAlert.classList.add('border-blue-200', 'bg-blue-50', 'text-blue-700');
        } else {
            scanStatusAlert.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
        }

        scanStatusAlert.innerHTML = message;
    }

    function escapeQrText(value) {
        return String(value).replace(/[&<>"']/g, char => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[char]);
    }

    function showWaitingForQr() {
        if (!isScanning || isProcessing || isCoolingDown) return;
        updateCameraStatus(true, 'Kamera aktif - Mencari QR Code...');
        showScanAlert('<div class="font-semibold">Mencari QR Code...</div><div class="mt-1">Arahkan QR Code kartu siswa ke dalam area pemindaian.</div>', 'success');
    }

    function hideScanAlert() {
        if (scanStatusAlert) {
            scanStatusAlert.classList.add('hidden');
            scanStatusAlert.innerHTML = '';
        }
    }

    function updateCameraStatus(active, text = null, tone = null) {
        if (!cameraStatusBadge || !cameraStatusDot || !cameraStatusText) return;
        const color = tone || (active ? 'green' : 'gray');
        const styles = {
            gray: ['bg-gray-400', 'border-[var(--hairline)] bg-[var(--paper)] text-[var(--muted)]', 'font-medium'],
            green: ['bg-emerald-500 animate-pulse', 'border-emerald-200 bg-emerald-50 text-emerald-700', 'font-semibold'],
            blue: ['bg-blue-500 animate-pulse', 'border-blue-200 bg-blue-50 text-blue-700', 'font-semibold'],
            red: ['bg-red-500', 'border-red-200 bg-red-50 text-red-700', 'font-semibold']
        }[color] || ['bg-gray-400', 'border-[var(--hairline)] bg-[var(--paper)] text-[var(--muted)]', 'font-medium'];
        cameraStatusDot.className = 'h-2 w-2 rounded-full ' + styles[0];
        cameraStatusText.textContent = text || (active ? 'Kamera aktif - Siap scan' : 'Kamera nonaktif');
        cameraStatusBadge.className = 'inline-flex items-center gap-2 rounded-xl border px-3.5 py-2 text-xs ' + styles[1] + ' ' + styles[2];
    }

    function resetScannerUiButtons(isCameraActive) {
        if (btnStartScan) {
            if (isCameraActive) {
                btnStartScan.classList.add('hidden');
            } else {
                btnStartScan.classList.remove('hidden');
                btnStartScan.disabled = false;
                btnStartScan.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }
        if (btnStopScan) {
            if (isCameraActive) {
                btnStopScan.classList.remove('hidden');
                btnStopScan.disabled = false;
            } else {
                btnStopScan.classList.add('hidden');
                btnStopScan.disabled = false;
            }
        }
        if (scannerPlaceholder) {
            if (isCameraActive) {
                scannerPlaceholder.classList.add('hidden');
            } else {
                scannerPlaceholder.classList.remove('hidden');
            }
        }
    }

    async function stopScannerCameraTracks() {
        const video = document.querySelector('#qr-reader video');
        const stream = video && video.srcObject;
        if (stream && typeof stream.getTracks === 'function') {
            stream.getTracks().forEach(track => {
                try { track.stop(); } catch (err) { console.warn('[QR Scanner] Track camera gagal dihentikan:', err); }
            });
            video.srcObject = null;
        }
    }

    function pauseScannerSafely() {
        // Biarkan video camera tetap aktif tanpa freeze untuk UX yang mulus
    }

    function resumeScannerSafely() {
        // Tidak perlu resume manual karena decoding dikontrol oleh state boolean
    }

    window.startQrScanner = async function () {
        if (isScanning || isStartingCamera) {
            console.log("startQrScanner diabaikan: kamera sedang aktif atau sedang disiapkan.");
            return;
        }
        hideScanAlert();

        if (typeof Html5Qrcode === 'undefined') {
            console.error("Library Html5Qrcode belum siap dimuat.");
            updateCameraStatus(false, 'Library scanner QR tidak tersedia', 'red');
            showScanAlert('<div class="flex items-center gap-2"><i class="fas fa-triangle-exclamation"></i><span>Library pemindai QR belum selesai dimuat. Harap periksa koneksi internet Anda lalu coba lagi.</span></div>', 'error');
            return;
        }

        isStartingCamera = true;
        if (btnStartScan) {
            btnStartScan.disabled = true;
            btnStartScan.classList.add('opacity-60', 'cursor-not-allowed');
        }

        try {
            updateCameraStatus(true, 'Menghubungkan webcam...');

            // Bersihkan instance lama jika masih tersisa
            if (html5QrCode) {
                try {
                    if (isScanning) {
                        await html5QrCode.stop();
                    }
                    html5QrCode.clear();
                } catch (e) {
                    console.warn("Pembersihan scanner sebelumnya:", e);
                }
                html5QrCode = null;
            }

            if (cooldownTimeout) {
                clearTimeout(cooldownTimeout);
                cooldownTimeout = null;
            }
            if (cooldownInterval) {
                clearInterval(cooldownInterval);
                cooldownInterval = null;
            }
            isProcessing = false;
            isCoolingDown = false;
            lastScannedCode = null;
            lastScanTime = 0;

            const config = {
                fps: 12,
                qrbox: function (viewfinderWidth, viewfinderHeight) {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    const edge = Math.floor(minEdge * 0.85);
                    return {
                        width: Math.min(viewfinderWidth, Math.max(edge, 200)),
                        height: Math.min(viewfinderHeight, Math.max(edge, 200))
                    };
                },
                aspectRatio: 1.0,
                showTorchButtonIfSupported: true
            };

            // Buat instance scanner bersih pada elemen #qr-reader
            html5QrCode = new Html5Qrcode("qr-reader", {
                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
            });

            // Tentukan sumber webcam:
            // 1. Coba deteksi kamera dengan getCameras() untuk mendapatkan device ID string murni
            // 2. Jika tidak ada / enumerasi ditolak, gunakan fallback { facingMode: "user" }
            let cameraSource = { facingMode: "user" };
            try {
                const cameras = await Html5Qrcode.getCameras();
                if (cameras && cameras.length > 0) {
                    cameraSource = cameras[0].id;
                    console.log("Webcam terdeteksi:", cameras[0].label || cameras[0].id);
                }
            } catch (camErr) {
                console.warn("Info enumerasi webcam:", camErr);
            }

            // Setel sebelum start: callback decoder dapat terjadi segera setelah stream aktif.
            isScanning = true;
            try {
                await html5QrCode.start(
                    cameraSource,
                    config,
                    onScanSuccess,
                    onScanError
                );
            } catch (startErr) {
                console.warn("Start dengan cameraSource awal gagal, mencoba fallback instance { facingMode: 'user' }...", startErr);
                // Bersihkan instance sebelum mencoba kembali untuk mencegah dirty state
                try {
                    if (html5QrCode) {
                        try { await html5QrCode.stop(); } catch (_) {}
                        await stopScannerCameraTracks();
                        html5QrCode.clear();
                    }
                } catch (_) {}
                html5QrCode = new Html5Qrcode("qr-reader", {
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                });
                isScanning = true;
                await html5QrCode.start(
                    { facingMode: "user" },
                    config,
                    onScanSuccess,
                    onScanError
                );
            }

            resetScannerUiButtons(true);
            showWaitingForQr();
        } catch (err) {
            console.error("Gagal memulai kamera (Browser/Library Error):", err);
            if (err) {
                console.error("Detail Error Browser:", {
                    name: err.name,
                    message: err.message,
                    stack: err.stack,
                    raw: String(err)
                });
            }
            updateCameraStatus(false, 'Gagal mengaktifkan kamera', 'red');

            const errorMsg = 'Kamera tidak dapat digunakan. Periksa izin kamera browser.';
            showScanAlert('<div class="font-semibold">Gagal mengaktifkan kamera</div><div class="mt-1">' + errorMsg + '</div>', 'error');

            isScanning = false;
            if (html5QrCode) {
                try { await html5QrCode.stop(); } catch (_) {}
                await stopScannerCameraTracks();
                try { html5QrCode.clear(); } catch (_) {}
                html5QrCode = null;
            }
            resetScannerUiButtons(false);
        } finally {
            isStartingCamera = false;
            if (btnStartScan && !isScanning) {
                btnStartScan.disabled = false;
                btnStartScan.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }
    };

    window.stopQrScanner = async function () {
        if (cooldownInterval) {
            clearInterval(cooldownInterval);
            cooldownInterval = null;
        }
        if (cooldownTimeout) {
            clearTimeout(cooldownTimeout);
            cooldownTimeout = null;
        }
        isProcessing = false;
        isCoolingDown = false;
        lastScannedCode = null;
        lastScanTime = 0;

        if (cooldownCountdownBadge) {
            cooldownCountdownBadge.classList.add('hidden');
        }

        if (!html5QrCode) {
            isScanning = false;
            resetScannerUiButtons(false);
            updateCameraStatus(false, 'Kamera dinonaktifkan');
            return;
        }

        if (btnStopScan) {
            btnStopScan.disabled = true;
        }

        try {
            if (isScanning) {
                await html5QrCode.stop();
            }
        } catch (err) {
            console.warn("[QR Scanner] Info penghentian kamera:", err);
        } finally {
            await stopScannerCameraTracks();
            try { html5QrCode.clear(); } catch (err) { console.warn('[QR Scanner] Elemen scanner gagal dibersihkan:', err); }
            html5QrCode = null;
            isScanning = false;
            isStartingCamera = false;
            resetScannerUiButtons(false);
            updateCameraStatus(false, 'Kamera dinonaktifkan');
        }
    };

    function onScanSuccess(decodedText) {
        if (!isScanning) return;
        // Kunci sinkron: jangan kirim request baru jika request sebelumnya masih berjalan
        if (isProcessing || isCoolingDown) return;
        if (!decodedText || !decodedText.trim()) return;

        const code = decodedText;
        const now = Date.now();

        // Cegah spam frame kamera berulang untuk kartu yang sama dalam masa jeda 2 detik
        if (code === lastScannedCode && (now - lastScanTime) < COOLDOWN_MS) {
            return;
        }

        // Kunci status pemrosesan seketika saat kode terbaca pada frame pertama
        isProcessing = true;
        lastScannedCode = code;
        lastScanTime = now;

        console.log('QR terbaca:', code);
        console.log('[QR Scanner] Mengirim absensi ke server...');
        updateCameraStatus(true, 'QR berhasil dibaca - mencari siswa...', 'blue');
        showScanAlert('<div class="font-semibold">QR Code berhasil dibaca</div><div class="mt-1 font-mono break-all">Data QR: ' + escapeQrText(code) + '</div><div class="mt-1">Sedang mencari siswa di database...</div>', 'info');

        prosesQrScan(code);
    }

    function onScanError(errorMessage) {
        // Abaikan log frame per frame yang tidak menemukan QR
    }

    async function prosesQrScan(code) {
        const keperluanVal = qrKeperluan ? qrKeperluan.value : 'Berkunjung atau keperluan lainnya';

        try {
            const response = await fetch("{{ route('guest-book.qr-scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qr_content: code,
                    keperluan: keperluanVal
                })
            });

            const result = await response.json();
            console.log("[QR Scanner] Respons server:", result);

            if (result.success === true) {
                tampilkanAbsensiBerhasil(result.data);
                updateCameraStatus(true, 'Absensi berhasil', 'green');
                showScanAlert('<div class="font-semibold">Siswa ditemukan</div><div class="mt-1">' + escapeQrText(result.data?.nama || '') + '</div><div class="mt-1 font-mono break-all">Data QR: ' + escapeQrText(code) + '</div><div class="mt-1 font-semibold">Absensi berhasil untuk ' + escapeQrText(result.data?.nama || 'siswa') + '.</div>', 'success');
            } else {
                if (result.already_attended) {
                    if (result.data) {
                        tampilkanAbsensiBerhasil(result.data);
                    }
                    updateCameraStatus(true, 'QR terbaca - kunjungan sudah tercatat', 'blue');
                    showScanAlert('<div class="font-semibold">QR Code berhasil dibaca</div><div class="mt-1 font-mono break-all">Data QR: ' + escapeQrText(code) + '</div><div class="mt-1">Kunjungan siswa ini sudah tercatat hari ini.</div>', 'warning');
                } else if (result.ambiguous) {
                    updateCameraStatus(true, 'Identitas QR ambigu', 'red');
                    showScanAlert('<div class="font-semibold">QR Code terbaca, tetapi nama cocok dengan beberapa siswa.</div><div class="mt-1 font-mono break-all">QR Code: ' + escapeQrText(code) + '</div><div class="mt-1">Mohon verifikasi identitas siswa di database.</div>', 'error');
                } else if (result.decoded) {
                    updateCameraStatus(true, 'Siswa belum terdaftar', 'red');
                    showScanAlert('<div class="font-semibold">QR Code berhasil dibaca, tetapi siswa belum terdaftar.</div><div class="mt-1 font-mono break-all">QR Code: ' + escapeQrText(code) + '</div><div class="mt-1">Silakan tambahkan siswa terlebih dahulu melalui Kelola User.</div>', 'error');
                } else {
                    updateCameraStatus(true, 'Terjadi kesalahan saat mencari siswa', 'red');
                    showScanAlert('<div class="font-semibold">Terjadi kesalahan saat mencari siswa.</div><div class="mt-1 font-mono break-all">QR Code: ' + escapeQrText(code) + '</div>', 'error');
                }
            }
        } catch (err) {
            console.error("[QR Scanner] Kesalahan fetch QR scan:", err);
            updateCameraStatus(true, 'Terjadi kesalahan koneksi', 'red');
            showScanAlert('<div class="font-semibold">QR Code berhasil dibaca, tetapi terjadi gangguan koneksi.</div><div class="mt-1 font-mono break-all">QR Code: ' + escapeQrText(code) + '</div><div class="mt-1">Periksa koneksi lalu coba lagi.</div>', 'error');
        } finally {
            // PENTING: Buka kembali status isProcessing di dalam finally agar scanner tidak pernah terkunci
            isProcessing = false;
            isCoolingDown = true;
            console.log("[QR Scanner] Request selesai. isProcessing = false.");

            // Aktifkan jeda cooldown untuk kode yang sama
            startCooldown(COOLDOWN_MS);
        }
    }

    function startCooldown(durationMs) {
        if (cooldownTimeout) {
            clearTimeout(cooldownTimeout);
            cooldownTimeout = null;
        }

        if (cooldownInterval) {
            clearInterval(cooldownInterval);
            cooldownInterval = null;
        }

        let remainingSec = Math.ceil(durationMs / 1000);
        if (cooldownCountdownBadge && cooldownSec) {
            cooldownSec.textContent = remainingSec;
            cooldownCountdownBadge.classList.remove('hidden');
        }

        cooldownInterval = setInterval(() => {
            remainingSec--;
            if (cooldownSec && remainingSec > 0) {
                cooldownSec.textContent = remainingSec;
            } else {
                if (cooldownInterval) {
                    clearInterval(cooldownInterval);
                    cooldownInterval = null;
                }
            }
        }, 1000);

        cooldownTimeout = setTimeout(() => {
            isCoolingDown = false;
            lastScannedCode = null;
            lastScanTime = 0;

            if (cooldownInterval) {
                clearInterval(cooldownInterval);
                cooldownInterval = null;
            }

            if (cooldownCountdownBadge) {
                cooldownCountdownBadge.classList.add('hidden');
            }

            if (isScanning) showWaitingForQr();

            console.log("[QR Scanner] Cooldown selesai. Scanner siap memindai QR code berikutnya.");
        }, durationMs);
    }

    // Cleanup camera when leaving the page
    window.addEventListener('beforeunload', function () {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(stopScannerCameraTracks).catch(() => {});
        }
    });
    window.addEventListener('pagehide', function () {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(stopScannerCameraTracks).catch(() => {});
        }
    });

    window.showRfidMode = function () {

        rfidPanel.classList.remove('hidden');
        manualPanel.classList.add('hidden');

        btnModeRfid.classList.remove(
            'border-[var(--hairline)]',
            'bg-white'
        );

        btnModeRfid.classList.add(
            'border-2',
            'border-[var(--emerald)]',
            'bg-[var(--emerald-tint)]'
        );

        btnModeManual.classList.remove(
            'border-2',
            'border-[var(--emerald)]',
            'bg-[var(--emerald-tint)]'
        );

        btnModeManual.classList.add(
            'border-[var(--hairline)]',
            'bg-white'
        );

        resetRfidDisplay();
    };

    window.showManualMode = function () {

        if (isScanning) {
            stopQrScanner();
        }

        rfidPanel.classList.add('hidden');
        manualPanel.classList.remove('hidden');

        btnModeManual.classList.remove(
            'border-[var(--hairline)]',
            'bg-white'
        );

        btnModeManual.classList.add(
            'border-2',
            'border-[var(--emerald)]',
            'bg-[var(--emerald-tint)]'
        );

        btnModeRfid.classList.remove(
            'border-2',
            'border-[var(--emerald)]',
            'bg-[var(--emerald-tint)]'
        );

        btnModeRfid.classList.add(
            'border-[var(--hairline)]',
            'bg-white'
        );

    };

    function resetRfidDisplay() {

        rfidSuccess.classList.add('hidden');
        if (rfidRegister) rfidRegister.classList.add('hidden');

        if (rfidTitle) rfidTitle.textContent = 'Silakan Pindai QR Code';
        if (rfidMessage) rfidMessage.textContent = 'Arahkan QR Code siswa ke kamera reader.';
        if (rfidIcon) rfidIcon.innerHTML = '<i class="fas fa-qrcode text-3xl"></i>';

        if (rfidUid) rfidUid.value = '';

        selectedStudentId = null;

        if (rfidSelectedStudent) rfidSelectedStudent.classList.add('hidden');

        if (rfidNomorIdentitas) {
            rfidNomorIdentitas.value = '';
            rfidNomorIdentitas.disabled = true;
        }

        if (rfidRegisterMessage) {
            rfidRegisterMessage.classList.add('hidden');
            rfidRegisterMessage.textContent = '';
        }

        if (btnRegisterRfid) {
            btnRegisterRfid.disabled = true;
            btnRegisterRfid.classList.remove(
                'bg-[var(--emerald-deep)]',
                'hover:bg-[var(--forest)]',
                'cursor-pointer'
            );
            btnRegisterRfid.classList.add(
                'bg-gray-300',
                'cursor-not-allowed'
            );
            const spanText = btnRegisterRfid.querySelector('span');
            if (spanText) spanText.textContent = 'Daftarkan Kartu';
        }

        if (rfidSiswaSearch) rfidSiswaSearch.value = '';

        if (rfidSiswaItems) {
            rfidSiswaItems.forEach(function (item) {
                item.classList.add('hidden');
            });
        }
    }

    function tampilkanAbsensiBerhasil(data) {
        if (!data) return;

        if (rfidRegister) rfidRegister.classList.add('hidden');

        rfidSuccess.classList.remove('hidden');

        if (rfidTitle) rfidTitle.textContent = 'Absensi Berhasil';
        if (rfidMessage) rfidMessage.textContent = 'Absensi berhasil untuk ' + (data.nama ?? 'siswa') + '.';
        if (rfidIcon) rfidIcon.innerHTML = '<i class="fas fa-check text-3xl"></i>';

        const elNama = document.getElementById('successNama');
        const elIdentitas = document.getElementById('successIdentitas');
        const elKelas = document.getElementById('successKelas');
        const elJurusan = document.getElementById('successJurusan');

        if (elNama) elNama.textContent = data.nama ?? '-';
        if (elIdentitas) elIdentitas.textContent = data.nomor_identitas ?? '-';
        if (elKelas) elKelas.textContent = data.kelas ?? '-';
        if (elJurusan) elJurusan.textContent = data.jurusan ?? '-';
    }

    function tampilkanPendaftaran(uid) {

        rfidSuccess.classList.add('hidden');

        rfidRegister.classList.remove('hidden');

        rfidTitle.textContent = 'Kartu Belum Terdaftar';

        rfidMessage.textContent =
            'Silakan pilih identitas siswa untuk mendaftarkan kartu ini.';

        rfidIcon.innerHTML =
            '<i class="fas fa-id-card text-3xl"></i>';

        rfidUid.value = uid;

        rfidSiswaSearch.value = '';

        rfidNomorIdentitas.value = '';
        rfidNomorIdentitas.disabled = true;

        rfidRegisterMessage.classList.add('hidden');
        rfidRegisterMessage.textContent = '';

        selectedStudentId = null;

        rfidSelectedStudent.classList.add('hidden');

        btnRegisterRfid.disabled = true;

        btnRegisterRfid.classList.remove(
            'bg-[var(--emerald-deep)]',
            'hover:bg-[var(--forest)]',
            'cursor-pointer'
        );

        btnRegisterRfid.classList.add(
            'bg-gray-300',
            'cursor-not-allowed'
        );

        rfidSiswaItems.forEach(function (item) {
            item.classList.add('hidden');
        });
    }

    function tampilkanPesanPendaftaran(message, type = 'error') {

        rfidRegisterMessage.classList.remove('hidden');

        rfidRegisterMessage.textContent = message;

        rfidRegisterMessage.classList.remove(
            'border-red-200',
            'bg-red-50',
            'text-red-700',
            'border-emerald-200',
            'bg-emerald-50',
            'text-emerald-700'
        );

        if (type === 'success') {

            rfidRegisterMessage.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700'
            );

        } else {

            rfidRegisterMessage.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );
        }
    }

    function updateRegisterButton() {

        const uid = rfidUid.value.trim();

        const nomorIdentitas =
            rfidNomorIdentitas.value.trim();

        const siap =
            uid !== '' &&
            selectedStudentId !== null &&
            nomorIdentitas !== '';

        btnRegisterRfid.disabled = !siap;

        if (siap) {

            btnRegisterRfid.classList.remove(
                'bg-gray-300',
                'cursor-not-allowed'
            );

            btnRegisterRfid.classList.add(
                'bg-[var(--emerald-deep)]',
                'hover:bg-[var(--forest)]',
                'cursor-pointer'
            );

        } else {

            btnRegisterRfid.classList.remove(
                'bg-[var(--emerald-deep)]',
                'hover:bg-[var(--forest)]',
                'cursor-pointer'
            );

            btnRegisterRfid.classList.add(
                'bg-gray-300',
                'cursor-not-allowed'
            );
        }
    }

    if (rfidSiswaSearch) {

        rfidSiswaSearch.addEventListener('input', function () {

            const keyword = this.value
                .toLowerCase()
                .trim();

            rfidSiswaItems.forEach(function (item) {

                const nama = item.dataset.name || '';

                if (
                    keyword !== '' &&
                    nama.includes(keyword)
                ) {

                    item.classList.remove('hidden');

                } else {

                    item.classList.add('hidden');
                }

            });

        });

    }

    rfidSiswaItems.forEach(function (item) {

        item.addEventListener('click', function () {

            selectedStudentId = this.dataset.id;

            const nama = this.dataset.nama || '-';
            const identitas = this.dataset.identitas || '';
            const kelas = this.dataset.kelas || '-';
            const jurusan = this.dataset.jurusan || '-';

            selectedStudentName.textContent = nama;

            selectedStudentDetail.textContent =
                'Kelas ' + kelas +
                (jurusan ? ' • ' + jurusan : '');

            rfidSelectedStudent.classList.remove('hidden');

            rfidNomorIdentitas.disabled = false;

            rfidNomorIdentitas.value = '';

            rfidNomorIdentitas.focus();

            rfidRegisterMessage.classList.add('hidden');
            rfidRegisterMessage.textContent = '';

            updateRegisterButton();

        });

    });

    if (rfidNomorIdentitas) {

        rfidNomorIdentitas.addEventListener('input', function () {

            rfidRegisterMessage.classList.add('hidden');

            updateRegisterButton();

        });

    }

    if (btnRegisterRfid) {

        btnRegisterRfid.addEventListener('click', async function () {

            const uid = rfidUid.value.trim();

            const nomorIdentitas =
                rfidNomorIdentitas.value.trim();

            if (!uid) {

                tampilkanPesanPendaftaran(
                    'UID kartu belum terbaca.'
                );

                return;
            }

            if (!selectedStudentId) {

                tampilkanPesanPendaftaran(
                    'Silakan pilih siswa terlebih dahulu.'
                );

                return;
            }

            if (!nomorIdentitas) {

                tampilkanPesanPendaftaran(
                    'Nomor identitas wajib diisi.'
                );

                rfidNomorIdentitas.focus();

                return;
            }

            btnRegisterRfid.disabled = true;

            btnRegisterRfid.classList.remove(
                'bg-[var(--emerald-deep)]',
                'hover:bg-[var(--forest)]',
                'cursor-pointer'
            );

            btnRegisterRfid.classList.add(
                'bg-gray-400',
                'cursor-wait'
            );

            btnRegisterRfid.querySelector('span').textContent =
                'Memproses...';

            rfidRegisterMessage.classList.add('hidden');

            try {

                const response = await fetch(
                    "{{ route('guest-book.rfid.register') }}",
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },

                        body: JSON.stringify({

                            rfid_uid: uid,

                            user_id: selectedStudentId,

                            nomor_identitas: nomorIdentitas,

                            keperluan: (document.getElementById('qrKeperluan') ? document.getElementById('qrKeperluan').value : 'Berkunjung atau keperluan lainnya')

                        })
                    }
                );

                const result = await response.json();

                if (
                    response.ok &&
                    result.success === true
                ) {

                    tampilkanAbsensiBerhasil(
                        result.data
                    );

                    return;
                }

                let message =
                    result.message ||
                    'Kartu RFID gagal didaftarkan.';

                if (
                    result.errors &&
                    typeof result.errors === 'object'
                ) {

                    const firstError =
                        Object.values(result.errors)[0];

                    if (
                        Array.isArray(firstError) &&
                        firstError.length > 0
                    ) {

                        message = firstError[0];
                    }
                }

                tampilkanPesanPendaftaran(
                    message
                );

            } catch (error) {

                console.error(error);

                tampilkanPesanPendaftaran(
                    'Terjadi kesalahan koneksi. Silakan coba lagi.'
                );

            } finally {

                btnRegisterRfid.disabled = false;

                btnRegisterRfid.classList.remove(
                    'bg-gray-400',
                    'cursor-wait'
                );

                btnRegisterRfid.classList.add(
                    'bg-[var(--emerald-deep)]',
                    'hover:bg-[var(--forest)]',
                    'cursor-pointer'
                );

                btnRegisterRfid.querySelector('span').textContent =
                    'Daftarkan Kartu';

                updateRegisterButton();
            }

        });

    }

    window.prosesRFID = async function (uid) {

        if (!uid) {
            return;
        }

        uid = uid.trim();

        if (!uid) {
            return;
        }

        rfidTitle.textContent = 'Membaca Kartu...';

        rfidMessage.textContent =
            'Sedang memeriksa kartu RFID Anda.';

        rfidIcon.innerHTML =
            '<i class="fas fa-spinner fa-spin text-3xl"></i>';

        rfidSuccess.classList.add('hidden');
        rfidRegister.classList.add('hidden');

        const qrKeperluanElem = document.getElementById('qrKeperluan');
        const keperluanVal = qrKeperluanElem ? qrKeperluanElem.value : 'Berkunjung atau keperluan lainnya';

        try {

            const response = await fetch(
                "{{ route('guest-book.rfid') }}",
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },

                    body: JSON.stringify({
                        rfid_uid: uid,
                        keperluan: keperluanVal
                    })
                }
            );

            const result = await response.json();

            if (
                result.success === true &&
                result.registered === true
            ) {

                tampilkanAbsensiBerhasil(
                    result.data
                );

                return;
            }

            if (
                result.success === false &&
                result.registered === false
            ) {

                tampilkanPendaftaran(uid);

                return;
            }

            throw new Error(
                result.message ||
                'Terjadi kesalahan saat membaca kartu.'
            );

        } catch (error) {

            console.error(error);

            rfidTitle.textContent =
                'Koneksi Bermasalah';

            rfidMessage.textContent =
                'Kartu belum dapat diproses. Silakan coba lagi.';

            rfidIcon.innerHTML =
                '<i class="fas fa-triangle-exclamation text-3xl"></i>';
        }

    };

    // Event listeners tombol mode & scan
    if (btnModeRfid) {
        btnModeRfid.addEventListener('click', window.showRfidMode);
    }
    if (btnModeManual) {
        btnModeManual.addEventListener('click', window.showManualMode);
    }
    if (btnStartScan) {
        btnStartScan.addEventListener('click', window.startQrScanner);
    }
    if (btnStopScan) {
        btnStopScan.addEventListener('click', window.stopQrScanner);
    }

    // Default tampilan awal: Absensi QR Code
    showRfidMode();

    const namaInput = document.getElementById('nama');
    const kelasInput = document.getElementById('kelas');
    const jurusanInput = document.getElementById('jurusan');
    const datalistOptions = document.querySelectorAll('#siswa_list option');

    if (namaInput) {
        namaInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const keperluanSelect = document.getElementById('keperluan');
                if (keperluanSelect) {
                    keperluanSelect.focus();
                }
            }
        });

        namaInput.addEventListener('input', function () {
            const val = this.value.trim();

            datalistOptions.forEach(function (opt) {
                if (opt.value === val) {
                    if (kelasInput) {
                        kelasInput.value = opt.getAttribute('data-kelas') || '';
                    }
                    if (jurusanInput) {
                        jurusanInput.value = opt.getAttribute('data-jurusan') || '';
                    }
                }
            });
        });
    }

});

</script>

@endsection
