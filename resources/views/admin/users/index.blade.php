@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
    @php
        $users = $users ?? collect();
        $kelasList = $kelasList ?? collect();
        $kelasFilterList = $kelasFilterList ?? collect();
        $jurusanList = $jurusanList ?? collect();
        $kelasRombelList = $kelasRombelList ?? collect();

        $isPaginator = method_exists($users, 'total') && method_exists($users, 'firstItem');
        $isShowAll = request('show') === 'all';

        $totalUsers = $isPaginator ? $users->total() : $users->count();
        $firstItem = $isPaginator ? ($users->firstItem() ?? 0) : ($totalUsers > 0 ? 1 : 0);
        $lastItem = $isPaginator ? ($users->lastItem() ?? 0) : $totalUsers;
        $currentPage = $isPaginator ? $users->currentPage() : 1;
        $lastPage = $isPaginator ? $users->lastPage() : 1;

        $showAllUrl = route('users.index', array_merge(request()->except(['page', 'show']), ['show' => 'all']));
        $showPagedUrl = route('users.index', request()->except(['page', 'show']));
    @endphp

    <div class="space-y-6">

        @if(session('success') || request()->query('created') == '1')
            <x-flash-message type="success" />
        @endif

        @if(session('deleted'))
            <x-flash-message type="deleted" />
        @endif

        @if(session('updated') || request()->query('updated') == '1')
            <x-flash-message type="updated" />
        @endif

        @if(session('error'))
            <x-flash-message type="error" message="{{ session('error') }}" />
        @endif

        {{-- Header --}}
        <div class="relative overflow-hidden rounded-[28px] px-5 py-6 shadow-lg shadow-[var(--forest)]/10 sm:px-7 sm:py-7" style="background-image: linear-gradient(135deg, var(--forest) 0%, var(--emerald-deep) 48%, var(--emerald) 100%);">
            <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/[0.06]"></div>
            <div class="pointer-events-none absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-white/[0.05]"></div>

            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="catalog-eyebrow font-semibold uppercase text-white/70">
                        Manajemen&nbsp;Akun
                    </p>

                    <h1 class="font-display mt-3 text-[26px] font-semibold leading-tight tracking-tight text-white sm:text-3xl md:text-[32px]">
                        Kelola User
                    </h1>

                    <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-white/80 sm:text-sm">
                        Atur data admin, petugas, siswa, kelas, dan jurusan perpustakaan dengan lebih mudah.
                    </p>
                </div>

                <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">
                    <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="catalog-eyebrow uppercase text-white/70">
                                    Data User
                                </p>

                                <p class="font-mono-stat mt-1 text-2xl font-semibold leading-none text-white">
                                    {{ $totalUsers }}
                                </p>
                            </div>

                            <div class="hidden h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-white/20 sm:flex">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3.5 backdrop-blur-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="catalog-eyebrow uppercase text-white/70">
                                    Kelas User
                                </p>

                                <p class="font-mono-stat mt-1 text-2xl font-semibold leading-none text-white">
                                    {{ $kelasFilterList->count() }}
                                </p>
                            </div>

                            <div class="hidden h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-white/20 sm:flex">
                                <i class="fas fa-school text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card --}}
        <div class="overflow-hidden rounded-2xl border border-[var(--hairline)] bg-white shadow-sm">
            <div class="border-b border-[var(--hairline)] p-5 md:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <h2 class="font-display text-lg font-semibold text-[var(--forest)] md:text-xl">
                            Daftar User
                        </h2>

                        <p class="mt-1 max-w-lg text-sm leading-relaxed text-[var(--muted)]">
                            Kelola akun pengguna berdasarkan role, kelas, dan jurusan.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 xl:items-end">
                        <div class="flex flex-wrap items-center gap-2 xl:justify-end">

                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'promote-classes')"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl border border-[var(--gold)]/40 bg-white px-4 text-sm font-semibold text-[var(--gold)] shadow-sm transition hover:bg-[#F6EEE0] focus:outline-none focus:ring-4 focus:ring-[#F6EEE0]"
                            >
                                Naik Kelas Rombel
                            </button>

                            <button
                                type="submit"
                                form="promote-selected-form"
                                id="promote-selected-button"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-[var(--emerald)]/40 hover:bg-[var(--sand)]/50 hover:text-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--sand)] disabled:cursor-not-allowed disabled:border-[var(--hairline)] disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:border-[var(--hairline)] disabled:hover:bg-slate-100 disabled:hover:text-slate-400"
                                disabled
                            >
                                Naikkan Dipilih
                            </button>

                            <button
                                type="submit"
                                form="bulk-delete-form"
                                id="bulk-delete-button"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl border border-[var(--hairline)] bg-white px-4 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:border-[var(--hairline)] disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:border-[var(--hairline)] disabled:hover:bg-slate-100 disabled:hover:text-slate-400"
                                disabled
                            >
                                Hapus Dipilih
                            </button>

                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'import-user-excel')"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl border border-sky-200 bg-white px-4 text-sm font-semibold text-sky-700 shadow-sm transition hover:border-sky-300 hover:bg-sky-50 focus:outline-none focus:ring-4 focus:ring-sky-100"
                            >
                                Import Excel
                            </button>

                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'create-user')"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-xl bg-[var(--emerald-deep)] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                            >
                                Tambah User
                            </button>

                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('users.index') }}" id="filter-form" class="mt-5">
                    @if($isShowAll)
                        <input type="hidden" name="show" value="all">
                    @endif

                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                        <div class="relative lg:col-span-5">
                            <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]"></i>

                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request('search') }}"
                                placeholder="Cari nama atau nomor identitas..."
                                autocomplete="off"
                                class="w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] py-3 pl-10 pr-4 text-sm text-[var(--text)] placeholder:text-[var(--muted)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                            >
                        </div>

                        <div class="lg:col-span-2">
                            <select
                                name="role"
                                id="role-select"
                                class="w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                            >
                                <option value="">Semua Role</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                                <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <select
                                name="kelas"
                                id="kelas-select"
                                class="w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                            >
                                <option value="">Semua Kelas</option>

                                @foreach($kelasFilterList as $namaKelas)
                                    <option value="{{ $namaKelas }}" {{ request('kelas') == $namaKelas ? 'selected' : '' }}>
                                        {{ $namaKelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <select
                                name="jurusan"
                                id="jurusan-select"
                                class="w-full rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3 text-sm text-[var(--text)] transition focus:border-[var(--emerald)] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                            >
                                <option value="">Semua Jurusan</option>

                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>
                                        {{ $jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-1">
                            <a
                                href="{{ route('users.index') }}"
                                title="Reset Filter"
                                class="flex h-full min-h-[46px] items-center justify-center rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 text-sm font-semibold text-[var(--muted)] transition hover:bg-[var(--sand)] hover:text-[var(--forest)]"
                            >
                                <i class="fas fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <form
                id="bulk-delete-form"
                action="{{ route('users.bulk-delete') }}"
                method="POST"
            >
                @csrf
            </form>

            <form
                id="promote-selected-form"
                action="{{ route('users.promote-selected') }}"
                method="POST"
            >
                @csrf
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1040px] border-collapse text-sm">
                    <thead>
                        <tr class="catalog-eyebrow bg-[var(--sand)]/40 uppercase text-[var(--muted)]">
                            <th class="w-12 border border-[var(--hairline)] px-4 py-4 text-center">
                                <input
                                    type="checkbox"
                                    id="select-all-users"
                                    class="h-4 w-4 rounded border-[var(--hairline)] text-[var(--emerald)] focus:ring-[var(--emerald)]"
                                >
                            </th>

                            <th class="w-16 border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                No
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Nama
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Nomor Identitas
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Kelas
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Jurusan
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-left font-semibold">
                                Role
                            </th>

                            <th class="border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Tanggal Daftar
                            </th>

                            <th class="w-32 border border-[var(--hairline)] px-5 py-4 text-center font-semibold">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @forelse($users as $index => $user)
                            <tr class="transition-colors hover:bg-[var(--sand)]/30">
                                <td class="border border-[var(--hairline)] px-4 py-4 text-center">
                                    <input
                                        type="checkbox"
                                        value="{{ $user->id }}"
                                        class="user-checkbox h-4 w-4 rounded border-[var(--hairline)] text-[var(--emerald)] focus:ring-[var(--emerald)]"
                                        {{ auth()->id() === $user->id ? 'disabled' : '' }}
                                    >
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 font-medium text-[var(--muted)]">
                                    {{ $isPaginator ? ($users->firstItem() + $index) : ($index + 1) }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
                                    <span class="font-semibold text-[var(--text)]">
                                        {{ $user->name }}
                                    </span>
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                    {{ $user->nomor_identitas ?? '-' }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                    {{ $user->kelas ?? '-' }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-[var(--muted)]">
                                    {{ $user->jurusan ?? '-' }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
                                    @if($user->role == 'admin')
                                        <span class="font-semibold text-red-600">
                                            Admin
                                        </span>
                                    @elseif($user->role == 'petugas')
                                        <span class="font-semibold text-[var(--emerald-deep)]">
                                            Petugas
                                        </span>
                                    @elseif($user->role == 'siswa' || $user->role == 'peminjam')
                                        <span class="font-semibold text-sky-600">
                                            Siswa
                                        </span>
                                    @else
                                        <span class="font-semibold text-[var(--text)]/70">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4 text-center text-[var(--muted)]">
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                                </td>

                                <td class="border border-[var(--hairline)] px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'update-user-{{ $user->id }}')"
                                            title="Edit User"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                        >
                                            <i class="fas fa-pen text-sm"></i>
                                        </button>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-delete', { url: '{{ route('users.destroy', $user->id) }}' })"
                                            title="Hapus User"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <x-modal name="update-user-{{ $user->id }}" title="Edit User" maxWidth="md">
                                @include('admin.users.partials.update-form', [
                                    'user' => $user,
                                    'kelasList' => $kelasList
                                ])
                            </x-modal>
                        @empty
                            <tr>
                                <td colspan="9" class="border border-[var(--hairline)] px-6 py-16 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--sand)]/60 text-[var(--muted)]">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>

                                    <p class="font-display mt-4 text-base font-semibold text-[var(--text)]">
                                        Tidak ada data user
                                    </p>

                                    <p class="mt-1 text-sm text-[var(--muted)]">
                                        Klik tombol "Tambah User" untuk menambahkan user baru.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[var(--hairline)] px-5 py-4">
                <div class="flex flex-col items-center justify-between gap-3 lg:flex-row">
                    <p class="text-sm text-[var(--muted)]">
                        Menampilkan
                        <span class="font-semibold text-[var(--text)]">{{ $firstItem }}</span>
                        &ndash;
                        <span class="font-semibold text-[var(--text)]">{{ $lastItem }}</span>
                        dari
                        <span class="font-semibold text-[var(--text)]">{{ $totalUsers }}</span>
                        data
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @if($isShowAll)
                            <a
                                href="{{ $showPagedUrl }}"
                                class="inline-flex min-h-[40px] items-center justify-center rounded-xl border border-[var(--hairline)] bg-white px-4 py-2 text-sm font-semibold text-[var(--text)]/80 shadow-sm transition hover:bg-[var(--sand)]/50 hover:text-[var(--forest)]"
                            >
                                Tampilkan Ringkas
                            </a>
                        @else
                            <a
                                href="{{ $showAllUrl }}"
                                class="inline-flex min-h-[40px] items-center justify-center rounded-xl bg-[var(--emerald-deep)] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--forest)]"
                            >
                                Lihat Semua Data
                            </a>
                        @endif

                        @if($isPaginator && !$isShowAll && $lastPage > 1)
                            <div class="flex flex-wrap items-center justify-center gap-1 rounded-xl border border-[var(--hairline)] bg-white px-2 py-1 shadow-sm">
                                @php
                                    $start = max(1, $currentPage - 1);
                                    $end = min($lastPage, $currentPage + 1);
                                @endphp

                                @if($start > 1)
                                    <a href="{{ $users->url(1) }}" class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60">
                                        1
                                    </a>

                                    @if($start > 2)
                                        <span class="flex h-9 min-w-9 items-center justify-center px-2 text-sm text-[var(--muted)]">
                                            ...
                                        </span>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $currentPage)
                                        <span class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg bg-[var(--emerald-deep)] px-3 text-sm font-semibold text-white shadow-sm">
                                            {{ $i }}
                                        </span>
                                    @else
                                        <a href="{{ $users->url($i) }}" class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60">
                                            {{ $i }}
                                        </a>
                                    @endif
                                @endfor

                                @if($end < $lastPage)
                                    @if($end < $lastPage - 1)
                                        <span class="flex h-9 min-w-9 items-center justify-center px-2 text-sm text-[var(--muted)]">
                                            ...
                                        </span>
                                    @endif

                                    <a href="{{ $users->url($lastPage) }}" class="font-mono-stat flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-semibold text-[var(--text)]/80 transition hover:bg-[var(--sand)]/60">
                                        {{ $lastPage }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <x-modal name="import-user-excel" title="Import User dari Excel" maxWidth="md">
            <form action="{{ route('users.import-excel') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-semibold text-[var(--text)]">
                        Kelas <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="kelas"
                        required
                        class="block w-full rounded-xl border border-[var(--hairline)] px-4 py-3 text-sm shadow-sm focus:border-[var(--emerald)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    >
                        <option value="">Pilih Kelas</option>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-[var(--text)]">
                        File Excel <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="file"
                        name="file"
                        accept=".xlsx,.xls"
                        required
                        class="block w-full rounded-xl border border-[var(--hairline)] px-4 py-3 text-sm shadow-sm focus:border-[var(--emerald)] focus:outline-none focus:ring-4 focus:ring-[var(--emerald-tint)]"
                    >
                </div>

                <div class="rounded-xl border border-sky-100 bg-sky-50 px-4 py-3">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sky-600 ring-1 ring-sky-100">
                            <i class="fas fa-circle-info text-xs"></i>
                        </div>

                        <p class="text-xs leading-relaxed text-[var(--text)]/70">
                            Upload Excel asli daftar absen. Sistem akan mencari header <strong>NISN</strong> dan <strong>NAMA</strong> otomatis di setiap sheet. Nama sheet akan dijadikan jurusan atau rombel.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                    >
                        <i class="fas fa-upload text-xs"></i>
                        Import
                    </button>
                </div>
            </form>
        </x-modal>

        <x-modal name="promote-classes" title="Naik Kelas Rombel" maxWidth="lg">
            <form action="{{ route('users.promote-classes') }}" method="POST" class="space-y-5">
                @csrf

                <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-red-600 ring-1 ring-red-100">
                            <i class="fas fa-triangle-exclamation text-sm"></i>
                        </div>

                        <div class="text-sm leading-relaxed text-[var(--text)]/80">
                            <p class="font-semibold text-[var(--text)]">
                                Pilih kelas/rombel yang ingin dinaikkan.
                            </p>

                            <p class="mt-2">
                                Yang diproses hanya kelas/rombel yang dicentang.
                                Kalau dalam satu rombel ada siswa tidak naik, gunakan tombol <strong>Naikkan Dipilih</strong> dari tabel user.
                            </p>

                            <p class="mt-2 text-xs text-[var(--muted)]">
                                Aturan: X naik ke XI, XI naik ke XII, XII dihapus karena dianggap lulus.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-semibold text-[var(--text)]">
                        Pilih Kelas/Rombel <span class="text-red-500">*</span>
                    </label>

                    <div class="max-h-72 space-y-4 overflow-y-auto rounded-xl border border-[var(--hairline)] bg-[var(--paper)] p-4">
                        @forelse($kelasRombelList->groupBy('nama_kelas') as $namaKelas => $rombels)
                            <div>
                                <p class="catalog-eyebrow mb-2 uppercase text-[var(--muted)]">
                                    Kelas {{ $namaKelas }}
                                </p>

                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach($rombels as $rombel)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-[var(--hairline)] bg-white px-3 py-2 text-sm text-[var(--text)]/80 transition hover:border-[var(--gold)]/40 hover:bg-[#F6EEE0]/50">
                                            <input
                                                type="checkbox"
                                                name="rombels[]"
                                                value="{{ $rombel->nama_kelas }}|{{ $rombel->jurusan }}"
                                                class="h-4 w-4 rounded border-[var(--hairline)] text-[var(--gold)] focus:ring-[var(--gold)]"
                                            >

                                            <span class="font-medium">
                                                {{ $rombel->nama_kelas }} - {{ $rombel->jurusan }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg bg-white px-4 py-6 text-center text-sm text-[var(--muted)]">
                                Belum ada data kelas/rombel.
                            </div>
                        @endforelse
                    </div>
                </div>

                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[var(--hairline)] bg-[var(--paper)] px-4 py-3">
                    <input
                        type="checkbox"
                        name="confirm"
                        value="1"
                        required
                        class="mt-1 h-4 w-4 rounded border-[var(--hairline)] text-[var(--gold)] focus:ring-[var(--gold)]"
                    >

                    <span class="text-sm leading-relaxed text-[var(--text)]/70">
                        Saya yakin ingin menjalankan proses naik kelas untuk kelas/rombel yang dipilih.
                    </span>
                </label>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:brightness-95"
                        style="background-color: var(--gold);"
                    >
                        <i class="fas fa-check text-xs"></i>
                        Jalankan Naik Kelas
                    </button>
                </div>
            </form>
        </x-modal>

        <x-modal name="create-user" title="Tambah User" maxWidth="md">
            @include('admin.users.partials.create-form', [
                'kelasList' => $kelasList
            ])
        </x-modal>

        <x-confirm-delete />
    </div>

    <script>
        (function () {
            var form = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var roleSelect = document.getElementById('role-select');
            var kelasSelect = document.getElementById('kelas-select');
            var jurusanSelect = document.getElementById('jurusan-select');
            var debounceTimer;

            if (form) {
                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function () {
                            form.submit();
                        }, 400);
                    });
                }

                if (roleSelect) {
                    roleSelect.addEventListener('change', function () {
                        form.submit();
                    });
                }

                if (kelasSelect) {
                    kelasSelect.addEventListener('change', function () {
                        form.submit();
                    });
                }

                if (jurusanSelect) {
                    jurusanSelect.addEventListener('change', function () {
                        form.submit();
                    });
                }
            }

            var selectAllUsers = document.getElementById('select-all-users');
            var userCheckboxes = document.querySelectorAll('.user-checkbox');
            var bulkDeleteButton = document.getElementById('bulk-delete-button');
            var promoteSelectedButton = document.getElementById('promote-selected-button');
            var bulkDeleteForm = document.getElementById('bulk-delete-form');
            var promoteSelectedForm = document.getElementById('promote-selected-form');

            function getCheckedUserIds() {
                return Array.prototype.slice.call(document.querySelectorAll('.user-checkbox:checked'))
                    .map(function (checkbox) {
                        return checkbox.value;
                    });
            }

            function clearSelectedInputs(targetForm) {
                if (!targetForm) {
                    return;
                }

                var oldInputs = targetForm.querySelectorAll('.selected-user-input');

                oldInputs.forEach(function (input) {
                    input.remove();
                });
            }

            function appendSelectedInputs(targetForm) {
                if (!targetForm) {
                    return;
                }

                clearSelectedInputs(targetForm);

                getCheckedUserIds().forEach(function (id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = id;
                    input.className = 'selected-user-input';

                    targetForm.appendChild(input);
                });
            }

            function updateActionButtons() {
                var checkedCount = getCheckedUserIds().length;

                if (bulkDeleteButton) {
                    bulkDeleteButton.disabled = checkedCount === 0;
                }

                if (promoteSelectedButton) {
                    promoteSelectedButton.disabled = checkedCount === 0;
                }

                if (selectAllUsers) {
                    var enabledCheckboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
                    var checkedEnabledCheckboxes = document.querySelectorAll('.user-checkbox:not(:disabled):checked');

                    selectAllUsers.checked = enabledCheckboxes.length > 0 && enabledCheckboxes.length === checkedEnabledCheckboxes.length;
                }
            }

            if (selectAllUsers) {
                selectAllUsers.addEventListener('change', function () {
                    userCheckboxes.forEach(function (checkbox) {
                        if (!checkbox.disabled) {
                            checkbox.checked = selectAllUsers.checked;
                        }
                    });

                    updateActionButtons();
                });
            }

            userCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', updateActionButtons);
            });

            if (bulkDeleteForm) {
                bulkDeleteForm.addEventListener('submit', function (event) {
                    if (getCheckedUserIds().length === 0) {
                        event.preventDefault();
                        alert('Pilih minimal satu user terlebih dahulu.');
                        return;
                    }

                    if (!confirm('Yakin ingin menghapus semua user yang dipilih?')) {
                        event.preventDefault();
                        return;
                    }

                    appendSelectedInputs(bulkDeleteForm);
                });
            }

            if (promoteSelectedForm) {
                promoteSelectedForm.addEventListener('submit', function (event) {
                    if (getCheckedUserIds().length === 0) {
                        event.preventDefault();
                        alert('Pilih minimal satu siswa terlebih dahulu.');
                        return;
                    }

                    if (!confirm('Yakin ingin menaikkan siswa yang dipilih? X menjadi XI, XI menjadi XII, dan XII akan dihapus/lulus.')) {
                        event.preventDefault();
                        return;
                    }

                    appendSelectedInputs(promoteSelectedForm);
                });
            }

            updateActionButtons();
        })();
    </script>
@endsection