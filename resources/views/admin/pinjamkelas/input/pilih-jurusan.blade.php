@extends('layouts.admin')

@section('title', 'Pilih Jurusan untuk Import')
@section('page-title', 'Pilih Jurusan untuk Import')

@section('content')

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

        <div class="relative z-10">
            <p class="catalog-eyebrow font-semibold uppercase text-white/70">Import&nbsp;Excel</p>

            <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                Pilih Jurusan untuk Diproses
            </h1>

            <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                Proses satu jurusan dalam satu waktu supaya lebih terkontrol. Setelah satu jurusan selesai, lanjutkan ke jurusan berikutnya.
            </p>
        </div>
    </div>

    @php
        $totalSheets = count($sheets);
        $totalDone = count($doneSheets);
    @endphp

    <div class="rounded-2xl border border-[var(--hairline)] bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-[var(--text)]">Progress</p>
                <p class="mt-1 text-xs text-[var(--muted)]">{{ $totalDone }} dari {{ $totalSheets }} jurusan sudah diproses</p>
            </div>

            @if($totalDone > 0)
                <form action="{{ route('admin.pinjamkelas.import.finish') }}" method="POST">
                    @csrf
                    <input type="hidden" name="temp_file" value="{{ $tempFile }}">
                    <button type="submit" onclick="return confirm('Selesaikan import? File sementara akan dihapus.')" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)]">
                        <i class="fas fa-check text-xs"></i>
                        <span>Selesai Import</span>
                    </button>
                </form>
            @endif
        </div>

        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-[var(--sand)]">
            <div class="h-full bg-[var(--emerald-deep)] transition-all" style="width: {{ $totalSheets > 0 ? ($totalDone / $totalSheets * 100) : 0 }}%"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($sheets as $sheet)
            @php
                $isDone = in_array($sheet['sheet_name'], $doneSheets);
            @endphp

            <div class="rounded-2xl border {{ $isDone ? 'border-[var(--emerald)]/40 bg-[var(--emerald-tint)]' : 'border-[var(--hairline)] bg-white' }} p-5 shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-display text-base font-semibold text-[var(--text)]">{{ $sheet['sheet_name'] }}</p>

                        <p class="mt-1 text-xs text-[var(--muted)]">
                            Kelas {{ $sheet['kelas'] ?? '-' }}
                            @if($sheet['jurusan'])
                                &middot; {{ $sheet['jurusan'] }}
                            @endif
                        </p>
                    </div>

                    @if($isDone)
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[var(--emerald-deep)] text-white">
                            <i class="fas fa-check text-xs"></i>
                        </span>
                    @endif
                </div>

                <div class="mt-3 flex items-center gap-3 text-xs text-[var(--muted)]">
                    <span><i class="fas fa-user-graduate mr-1"></i>{{ $sheet['jumlah_siswa'] }} siswa</span>
                    <span><i class="fas fa-book mr-1"></i>{{ $sheet['jumlah_mapel'] }} mapel</span>
                </div>

                <form action="{{ route('admin.pinjamkelas.import.select-sheet') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="temp_file" value="{{ $tempFile }}">
                    <input type="hidden" name="sheet_name" value="{{ $sheet['sheet_name'] }}">
                    <input type="hidden" name="done_sheets" value="{{ implode(',', $doneSheets) }}">

                    <button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl {{ $isDone ? 'border border-[var(--hairline)] bg-white text-[var(--text)]/80 hover:bg-[var(--sand)]/50' : 'bg-[var(--emerald-deep)] text-white hover:bg-[var(--forest)]' }} text-sm font-semibold shadow-sm transition">
                        <i class="fas fa-{{ $isDone ? 'rotate-right' : 'arrow-right' }} text-xs"></i>
                        <span>{{ $isDone ? 'Proses Ulang' : 'Proses Jurusan Ini' }}</span>
                    </button>
                </form>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-[var(--hairline)] bg-white px-6 py-16 text-center">
                <p class="text-sm font-medium text-[var(--muted)]">Tidak ada sheet yang terbaca dari file ini.</p>
            </div>
        @endforelse
    </div>

</div>

@endsection