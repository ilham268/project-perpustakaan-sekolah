@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
    @php
        $kelasList = $kelasList ?? collect();
        $tingkatKelas = ['X', 'XI', 'XII'];
        $kelasFilterList = $kelasList->pluck('nama_kelas')->filter()->unique()->values();
        $jurusanList = $jurusanList ?? $kelasList->pluck('jurusan')->filter()->unique()->values();
    @endphp

    <div class="space-y-6">

        {{-- Flash Messages --}}
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

        {{-- Page Header --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 md:px-7 md:py-6 shadow-md shadow-emerald-100/60">
            <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                        Kelola User
                    </h1>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                        Atur data admin, petugas, siswa, kelas, dan jurusan perpustakaan dengan lebih mudah.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 w-full lg:w-[360px]">
                    <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-emerald-50">
                                    Data User
                                </p>
                                <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                    {{ $users->total() }}
                                </p>
                            </div>

                            <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-emerald-50">
                                    Data Kelas
                                </p>
                                <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                    {{ $kelasList->count() }}
                                </p>
                            </div>

                            <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20">
                                <i class="fas fa-school text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Section --}}
        <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">

            {{-- Section Header --}}
            <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                            <i class="fas fa-user-group"></i>
                        </div>

                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-slate-900">
                                Daftar User
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Kelola akun pengguna berdasarkan role, kelas, dan jurusan.
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="$dispatch('open-modal', 'create-user')"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-100 transition hover:bg-emerald-700 hover:-translate-y-0.5"
                    >
                        <i class="fas fa-plus text-xs"></i>
                        <span>Tambah User</span>
                    </button>
                </div>

                {{-- Search & Filter --}}
                <form method="GET" action="{{ route('users.index') }}" id="filter-form" class="mt-5">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                        <div class="relative lg:col-span-5">
                            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>

                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request('search') }}"
                                placeholder="Cari nama atau nomor identitas..."
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 py-3 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 transition focus:bg-white focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                autocomplete="off"
                            >
                        </div>

                        <div class="lg:col-span-2">
                            <select
                                name="role"
                                id="role-select"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-700 transition focus:bg-white focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-700 transition focus:bg-white focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-700 transition focus:bg-white focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                class="flex h-full min-h-[46px] items-center justify-center rounded-2xl border border-slate-200 bg-slate-100/80 px-4 text-sm font-semibold text-slate-500 transition hover:bg-slate-200/70 hover:text-slate-700"
                                title="Reset Filter"
                            >
                                <i class="fas fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table User --}}
            <div class="overflow-x-auto bg-white/90">
                <table class="w-full min-w-[980px]">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-100/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-4 text-left w-16">No</th>
                            <th class="px-5 py-4 text-left">Nama</th>
                            <th class="px-5 py-4 text-left">Nomor Identitas</th>
                            <th class="px-5 py-4 text-left">Kelas</th>
                            <th class="px-5 py-4 text-left">Jurusan</th>
                            <th class="px-5 py-4 text-left">Role</th>
                            <th class="px-5 py-4 text-left">Tanggal Daftar</th>
                            <th class="px-5 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $index => $user)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
                                        {{ $users->firstItem() + $index }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">
                                            {{ $user->name }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-400">
                                            {{ $user->email ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-sm font-medium text-slate-600">
                                        {{ $user->nomor_identitas ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    @if($user->kelas)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                            {{ $user->kelas }}
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if($user->jurusan)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                            {{ $user->jurusan }}
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-red-50 text-red-700 ring-red-100',
                                            'petugas' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                                            'siswa' => 'bg-blue-50 text-blue-700 ring-blue-100',
                                            'peminjam' => 'bg-blue-50 text-blue-700 ring-blue-100',
                                        ];
                                    @endphp

                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                        {{ $user->role == 'siswa' ? 'Siswa' : ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-sm text-slate-500">
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'update-user-{{ $user->id }}')"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                            title="Edit User"
                                        >
                                            <i class="fas fa-pen text-sm"></i>
                                        </button>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-delete', { url: '{{ route('users.destroy', $user->id) }}' })"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                            title="Hapus User"
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
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>
                                    <p class="mt-4 text-base font-bold text-slate-700">
                                        Tidak ada data user
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        Klik tombol "Tambah User" untuk menambahkan user baru.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-slate-100 bg-white/80 px-5 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $users->firstItem() ?? 0 }}</span>
                        –
                        <span class="font-semibold text-slate-700">{{ $users->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $users->total() }}</span>
                        data
                    </p>

                    <div class="flex items-center gap-1">
                        @if ($users->onFirstPage())
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300 cursor-not-allowed">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        @php
                            $start = max(1, $users->currentPage() - 1);
                            $end = min($users->lastPage(), $users->currentPage() + 1);
                        @endphp

                        @if($start > 1)
                            <a href="{{ $users->url(1) }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-sm text-slate-600 transition hover:bg-slate-100">1</a>

                            @if($start > 2)
                                <span class="flex h-9 w-9 items-center justify-center text-sm text-slate-400">...</span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $users->currentPage())
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-sm">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $users->url($i) }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-sm text-slate-600 transition hover:bg-slate-100">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        @if($end < $users->lastPage())
                            @if($end < $users->lastPage() - 1)
                                <span class="flex h-9 w-9 items-center justify-center text-sm text-slate-400">...</span>
                            @endif

                            <a href="{{ $users->url($users->lastPage()) }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-sm text-slate-600 transition hover:bg-slate-100">
                                {{ $users->lastPage() }}
                            </a>
                        @endif

                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300 cursor-not-allowed">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kelola Kelas --}}
        <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-100 bg-white/80">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                            <i class="fas fa-school"></i>
                        </div>

                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-slate-900">
                                Kelola Kelas
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Pilih tingkat kelas X, XI, atau XII, lalu isi jurusan yang sesuai.
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="$dispatch('open-modal', 'create-kelas')"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-100 transition hover:bg-emerald-700 hover:-translate-y-0.5"
                    >
                        <i class="fas fa-plus text-xs"></i>
                        <span>Tambah Kelas</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white/90">
                <table class="w-full min-w-[760px]">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-100/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-4 text-left w-16">No</th>
                            <th class="px-5 py-4 text-left">Nama Kelas</th>
                            <th class="px-5 py-4 text-left">Jurusan</th>
                            <th class="px-5 py-4 text-left">Tanggal Dibuat</th>
                            <th class="px-5 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($kelasList as $index => $kelas)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
                                        {{ $index + 1 }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                        <i class="fas fa-layer-group text-[10px]"></i>
                                        {{ $kelas->nama_kelas }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                        <i class="fas fa-graduation-cap text-[10px]"></i>
                                        {{ $kelas->jurusan }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-sm text-slate-500">
                                        {{ $kelas->created_at ? $kelas->created_at->format('d/m/Y') : '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'update-kelas-{{ $kelas->id }}')"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                            title="Edit Kelas"
                                        >
                                            <i class="fas fa-pen text-sm"></i>
                                        </button>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-delete', { url: '{{ route('kelas.destroy', $kelas->id) }}' })"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
                                            title="Hapus Kelas"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <x-modal name="update-kelas-{{ $kelas->id }}" title="Edit Kelas" maxWidth="md">
                                <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" class="space-y-5">
                                    @csrf
                                    @method('PUT')

                                    {{-- Radio Kelas --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                                            Pilih Kelas <span class="text-red-500">*</span>
                                        </label>

                                        <div class="grid grid-cols-3 gap-3">
                                            @foreach($tingkatKelas as $tingkat)
                                                <label class="relative cursor-pointer">
                                                    <input
                                                        type="radio"
                                                        name="nama_kelas"
                                                        value="{{ $tingkat }}"
                                                        class="peer sr-only"
                                                        {{ old('nama_kelas', $kelas->nama_kelas) == $tingkat ? 'checked' : '' }}
                                                        required
                                                    >

                                                    <div class="rounded-2xl border-2 border-slate-200 bg-white px-4 py-4 text-center transition-all hover:border-emerald-200 hover:bg-emerald-50/40 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-sm">
                                                        <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                                            <i class="fas fa-school text-sm"></i>
                                                        </div>

                                                        <p class="text-xl font-extrabold text-slate-800">
                                                            {{ $tingkat }}
                                                        </p>

                                                        <p class="mt-0.5 text-[11px] font-medium text-slate-400">
                                                            Kelas {{ $tingkat }}
                                                        </p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>

                                        @error('nama_kelas')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Jurusan --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            Jurusan <span class="text-red-500">*</span>
                                        </label>

                                        <input
                                            name="jurusan"
                                            type="text"
                                            value="{{ old('jurusan', $kelas->jurusan) }}"
                                            placeholder="Contoh: Rekayasa Perangkat Lunak"
                                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm placeholder-slate-400 transition focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                            required
                                        >

                                        @error('jurusan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex justify-end gap-3 pt-2">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600"
                                        >
                                            <i class="fas fa-save text-xs"></i>
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </x-modal>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <i class="fas fa-school text-2xl"></i>
                                    </div>
                                    <p class="mt-4 text-base font-bold text-slate-700">
                                        Tidak ada data kelas
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        Klik tombol "Tambah Kelas" untuk menambahkan kelas baru.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Tambah User --}}
        <x-modal name="create-user" title="Tambah User" maxWidth="md">
            @include('admin.users.partials.create-form', [
                'kelasList' => $kelasList
            ])
        </x-modal>

        {{-- Modal Tambah Kelas --}}
        <x-modal name="create-kelas" title="Tambah Kelas" maxWidth="md">
            <form action="{{ route('kelas.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Radio Kelas --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">
                        Pilih Kelas <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-3 gap-3">
                        @foreach($tingkatKelas as $tingkat)
                            <label class="relative cursor-pointer">
                                <input
                                    type="radio"
                                    name="nama_kelas"
                                    value="{{ $tingkat }}"
                                    class="peer sr-only"
                                    {{ old('nama_kelas') == $tingkat ? 'checked' : '' }}
                                    required
                                >

                                <div class="rounded-2xl border-2 border-slate-200 bg-white px-4 py-4 text-center transition-all hover:border-emerald-200 hover:bg-emerald-50/40 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-sm">
                                    <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                        <i class="fas fa-school text-sm"></i>
                                    </div>

                                    <p class="text-xl font-extrabold text-slate-800">
                                        {{ $tingkat }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] font-medium text-slate-400">
                                        Kelas {{ $tingkat }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('nama_kelas')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jurusan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Jurusan <span class="text-red-500">*</span>
                    </label>

                    <input
                        name="jurusan"
                        type="text"
                        value="{{ old('jurusan') }}"
                        placeholder="Contoh: Rekayasa Perangkat Lunak"
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm placeholder-slate-400 transition focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        required
                    >

                    @error('jurusan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-3">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                            <i class="fas fa-circle-info text-xs"></i>
                        </div>

                        <p class="text-xs leading-relaxed text-slate-600">
                            Pilih tingkat kelas, lalu isi jurusan. Contoh:
                            <strong>X - RPL</strong>, <strong>XI - RPL</strong>, atau <strong>XII - TKJ</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        <i class="fas fa-save text-xs"></i>
                        Simpan
                    </button>
                </div>
            </form>
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

            if (!form) {
                return;
            }

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
        })();
    </script>
@endsection