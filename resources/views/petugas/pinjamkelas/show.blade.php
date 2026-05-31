@extends('layouts.petugas')

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
                    Input peminjaman buku paket untuk siswa berdasarkan kategori dan kode buku fisik.
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

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Form --}}
        <div class="xl:col-span-2">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-5 py-5 md:px-6">
                    <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                        Form Peminjaman Kelas
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Kategori:
                        <span class="font-semibold text-emerald-700">
                            {{ $kategori->nama_kategori }}
                        </span>
                    </p>
                </div>

                <div class="p-5 md:p-6">
                    <form action="{{ route('petugas.pinjamkelas.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-sm font-bold text-slate-800">
                                Informasi Input
                            </p>

                            <p class="mt-1 text-sm leading-relaxed text-slate-500">
                                Pilih siswa dari dropdown atau klik nama siswa pada daftar kanan, lalu masukkan kode buku.
                            </p>
                        </div>

                        {{-- Pilih Siswa --}}
                        <div>
                            <label for="user_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                Pilih Siswa <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="user_id"
                                id="user_id"
                                onchange="syncSelectedStudentFromSelect()"
                                class="h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                required
                            >
                                <option value="">Pilih Siswa</option>

                                @foreach($siswas as $siswa)
                                    <option
                                        value="{{ $siswa->id }}"
                                        data-name="{{ $siswa->name }}"
                                        data-nisn="{{ $siswa->nomor_identitas }}"
                                    >
                                        {{ $siswa->name }} - {{ $siswa->nomor_identitas }}
                                    </option>
                                @endforeach
                            </select>

                            @if($siswas->isEmpty())
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    Belum ada data siswa untuk kelas {{ $kelasLabel }}.
                                </p>
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
                                Pastikan kode buku sesuai dengan label atau barcode pada buku.
                            </p>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                            <a
                                href="{{ route('petugas.pinjamkelas.kategori') }}"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Kembali
                            </a>

                            <button
                                type="submit"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                                Simpan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Siswa --}}
        <div class="xl:col-span-1">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm xl:sticky xl:top-6">
                <div class="border-b border-slate-100 bg-white px-5 py-5">
                    <h3 class="text-base font-bold text-slate-900">
                        Daftar Siswa Kelas {{ $kelasLabel }}
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Klik nama siswa untuk otomatis masuk ke form.
                    </p>
                </div>

                <div class="max-h-[430px] overflow-y-auto p-4">
                    @if($siswas->isEmpty())
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center">
                            <p class="text-sm font-bold text-slate-700">
                                Belum ada siswa
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-slate-400">
                                Data siswa akan muncul setelah tersedia pada kelas ini.
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($siswas as $siswa)
                                <button
                                    type="button"
                                    id="student-card-{{ $siswa->id }}"
                                    onclick="selectStudentFromCard({{ $siswa->id }})"
                                    class="student-card rounded-lg border border-slate-200 bg-white px-4 py-3 text-left transition hover:border-emerald-300 hover:bg-emerald-50"
                                >
                                    <p class="truncate text-sm font-bold text-slate-800">
                                        {{ $siswa->name }}
                                    </p>

                                    <p class="mt-0.5 truncate text-xs text-slate-500">
                                        {{ $siswa->nomor_identitas }}
                                    </p>
                                </button>
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
    function selectStudentFromCard(studentId) {
        const select = document.getElementById('user_id');
        const kodeBuku = document.getElementById('kode_buku');

        if (!select) {
            return;
        }

        select.value = studentId;
        select.dispatchEvent(new Event('change'));

        document.querySelectorAll('.student-card').forEach(card => {
            card.classList.remove(
                'bg-emerald-50',
                'border-emerald-400',
                'ring-2',
                'ring-emerald-100'
            );
        });

        const activeCard = document.getElementById('student-card-' + studentId);

        if (activeCard) {
            activeCard.classList.add(
                'bg-emerald-50',
                'border-emerald-400',
                'ring-2',
                'ring-emerald-100'
            );
        }

        if (kodeBuku) {
            kodeBuku.focus();
        }
    }

    function syncSelectedStudentFromSelect() {
        const select = document.getElementById('user_id');

        if (!select) {
            return;
        }

        document.querySelectorAll('.student-card').forEach(card => {
            card.classList.remove(
                'bg-emerald-50',
                'border-emerald-400',
                'ring-2',
                'ring-emerald-100'
            );
        });

        if (!select.value) {
            return;
        }

        const activeCard = document.getElementById('student-card-' + select.value);

        if (activeCard) {
            activeCard.classList.add(
                'bg-emerald-50',
                'border-emerald-400',
                'ring-2',
                'ring-emerald-100'
            );
        }
    }
</script>

@endsection