@extends('layouts.peminjam')

@section('title', 'Input Buku')
@section('page-title', 'Input Buku - Peminjaman Kelas')

@section('content')

<div class="mx-auto max-w-2xl space-y-5">

    {{-- Page Header --}}
    <div class="flex flex-col gap-1">
        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Input Buku
        </h3>

        <p class="text-sm text-slate-500">
            Ajukan peminjaman buku kelas berdasarkan kategori dan kode buku.
        </p>
    </div>

    {{-- Form Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Card Header --}}
        <div class="border-b border-slate-200 bg-gradient-to-r from-emerald-50 to-white px-5 py-4">
            <h3 class="text-base font-bold text-slate-900">
                Form Input Buku
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Kelas Anda:
                <span class="font-bold text-emerald-600">
                    {{ Auth::user()->kelas ?? 'Belum diatur' }}
                </span>
            </p>
        </div>

        {{-- Card Body --}}
        <div class="p-5 md:p-6">
            <form action="{{ route('siswa.pinjamkelas.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    {{-- Kategori --}}
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Pilih Kategori
                            <span class="text-red-500">*</span>
                        </label>

                        @if($kategoris->isNotEmpty())
                            <select
                                name="kategori_id"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                            >
                                <option value="">-- Pilih Kategori --</option>

                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-2 text-xs text-slate-400">
                                Kategori yang tampil sesuai dengan kelas Anda.
                            </p>
                        @else
                            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>

                                    <div>
                                        <p class="text-sm font-bold text-red-700">
                                            Kategori belum tersedia
                                        </p>

                                        <p class="mt-1 text-sm leading-relaxed text-red-600">
                                            Belum ada kategori untuk kelas
                                            <strong>{{ Auth::user()->kelas }}</strong>.
                                            Silakan hubungi petugas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Kode Buku --}}
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Kode Buku
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode_buku"
                            required
                            placeholder="Masukkan kode buku"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm uppercase text-slate-700 outline-none transition placeholder:normal-case placeholder:text-slate-400 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100"
                        >

                        <p class="mt-2 text-xs text-slate-400">
                            Masukkan kode buku sesuai label yang ada pada buku.
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                        <a
                            href="{{ route('siswa.pinjamkelas.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-60"
                            {{ $kategoris->isEmpty() ? 'disabled' : '' }}
                        >
                            <i class="fas fa-save text-xs"></i>
                            <span>Simpan Peminjaman</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>

</div>

@endsection