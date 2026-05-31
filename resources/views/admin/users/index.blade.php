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

        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 px-5 py-5 shadow-md shadow-emerald-100/60 md:px-7 md:py-6">
            <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute right-20 -bottom-20 h-48 w-48 rounded-full bg-white/10"></div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">
                        Kelola User
                    </h1>

                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-50">
                        Atur data admin, petugas, siswa, kelas, dan jurusan perpustakaan dengan lebih mudah.
                    </p>
                </div>

                <div class="grid w-full grid-cols-2 gap-3 lg:w-[360px]">
                    <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-emerald-50">
                                    Data User
                                </p>

                                <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                    {{ $totalUsers }}
                                </p>
                            </div>

                            <div class="hidden h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20 sm:flex">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/15 px-4 py-3 ring-1 ring-white/20 backdrop-blur-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-emerald-50">
                                    Kelas User
                                </p>

                                <p class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                                    {{ $kelasFilterList->count() }}
                                </p>
                            </div>

                            <div class="hidden h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/20 sm:flex">
                                <i class="fas fa-school text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white/95 shadow-sm">
            <div class="border-b border-slate-100 bg-white/80 p-5 md:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 md:text-xl">
                            Daftar User
                        </h2>

                        <p class="mt-1 max-w-lg text-sm leading-relaxed text-slate-500">
                            Kelola akun pengguna berdasarkan role, kelas, dan jurusan.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 xl:items-end">
                        <div class="flex flex-wrap items-center gap-2 xl:justify-end">

                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'promote-classes')"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-violet-200 bg-white px-4 text-sm font-semibold text-violet-700 shadow-sm transition hover:border-violet-300 hover:bg-violet-50 focus:outline-none focus:ring-4 focus:ring-violet-100"
                            >
                                Naik Kelas Rombel
                            </button>

                            <button
                                type="submit"
                                form="promote-selected-form"
                                id="promote-selected-button"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:border-slate-200 disabled:hover:bg-slate-100 disabled:hover:text-slate-400"
                                disabled
                            >
                                Naikkan Dipilih
                            </button>

                            <button
                                type="submit"
                                form="bulk-delete-form"
                                id="bulk-delete-button"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:hover:border-slate-200 disabled:hover:bg-slate-100 disabled:hover:text-slate-400"
                                disabled
                            >
                                Hapus Dipilih
                            </button>

                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'import-user-excel')"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
                            >
                                Import Excel
                            </button>

                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'create-user')"
                                class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request('search') }}"
                                placeholder="Cari nama atau nomor identitas..."
                                autocomplete="off"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 py-3 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
                            >
                        </div>

                        <div class="lg:col-span-2">
                            <select
                                name="role"
                                id="role-select"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-700 transition focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-100"
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
                                class="flex h-full min-h-[46px] items-center justify-center rounded-2xl border border-slate-200 bg-slate-100/80 px-4 text-sm font-semibold text-slate-500 transition hover:bg-slate-200/70 hover:text-slate-700"
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

            <div class="overflow-x-auto bg-white/90">
                <table class="w-full min-w-[1040px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="w-12 border border-slate-200 px-4 py-4 text-center">
                                <input
                                    type="checkbox"
                                    id="select-all-users"
                                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                >
                            </th>

                            <th class="w-16 border border-slate-200 px-5 py-4 text-left">
                                No
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Nama
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Nomor Identitas
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Kelas
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Jurusan
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-left">
                                Role
                            </th>

                            <th class="border border-slate-200 px-5 py-4 text-center">
                                Tanggal Daftar
                            </th>

                            <th class="w-32 border border-slate-200 px-5 py-4 text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $index => $user)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="border border-slate-200 px-4 py-4 text-center">
                                    <input
                                        type="checkbox"
                                        value="{{ $user->id }}"
                                        class="user-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                        {{ auth()->id() === $user->id ? 'disabled' : '' }}
                                    >
                                </td>

                                <td class="border border-slate-200 px-5 py-4 font-medium text-slate-600">
                                    {{ $isPaginator ? ($users->firstItem() + $index) : ($index + 1) }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $user->name }}
                                    </span>
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $user->nomor_identitas ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $user->kelas ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-slate-500">
                                    {{ $user->jurusan ?? '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    @if($user->role == 'admin')
                                        <span class="font-semibold text-red-600">
                                            Admin
                                        </span>
                                    @elseif($user->role == 'petugas')
                                        <span class="font-semibold text-emerald-600">
                                            Petugas
                                        </span>
                                    @elseif($user->role == 'siswa' || $user->role == 'peminjam')
                                        <span class="font-semibold text-blue-600">
                                            Siswa
                                        </span>
                                    @else
                                        <span class="font-semibold text-slate-600">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="border border-slate-200 px-5 py-4 text-center text-slate-500">
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                                </td>

                                <td class="border border-slate-200 px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'update-user-{{ $user->id }}')"
                                            title="Edit User"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                        >
                                            <i class="fas fa-pen text-sm"></i>
                                        </button>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-delete', { url: '{{ route('users.destroy', $user->id) }}' })"
                                            title="Hapus User"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-red-100 transition hover:bg-red-100"
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
                                <td colspan="9" class="border border-slate-200 px-6 py-16 text-center">
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

            <div class="border-t border-slate-100 bg-white/80 px-5 py-4">
                <div class="flex flex-col items-center justify-between gap-3 lg:flex-row">
                    <p class="text-sm text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $firstItem }}</span>
                        &ndash;
                        <span class="font-semibold text-slate-700">{{ $lastItem }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $totalUsers }}</span>
                        data
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @if($isShowAll)
                            <a
                                href="{{ $showPagedUrl }}"
                                class="inline-flex min-h-[40px] items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-800"
                            >
                                Tampilkan Ringkas
                            </a>
                        @else
                            <a
                                href="{{ $showAllUrl }}"
                                class="inline-flex min-h-[40px] items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-emerald-100 transition hover:bg-emerald-700"
                            >
                                Lihat Semua Data
                            </a>
                        @endif

                        @if($isPaginator && !$isShowAll && $lastPage > 1)
                            <div class="flex flex-wrap items-center justify-center gap-1 rounded-2xl border border-slate-200 bg-white px-2 py-1 shadow-sm">
                                @php
                                    $start = max(1, $currentPage - 1);
                                    $end = min($lastPage, $currentPage + 1);
                                @endphp

                                @if($start > 1)
                                    <a href="{{ $users->url(1) }}" class="flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                        1
                                    </a>

                                    @if($start > 2)
                                        <span class="flex h-9 min-w-9 items-center justify-center px-2 text-sm text-slate-400">
                                            ...
                                        </span>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $currentPage)
                                        <span class="flex h-9 min-w-9 items-center justify-center rounded-xl bg-emerald-600 px-3 text-sm font-bold text-white shadow-sm">
                                            {{ $i }}
                                        </span>
                                    @else
                                        <a href="{{ $users->url($i) }}" class="flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                            {{ $i }}
                                        </a>
                                    @endif
                                @endfor

                                @if($end < $lastPage)
                                    @if($end < $lastPage - 1)
                                        <span class="flex h-9 min-w-9 items-center justify-center px-2 text-sm text-slate-400">
                                            ...
                                        </span>
                                    @endif

                                    <a href="{{ $users->url($lastPage) }}" class="flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
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
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Kelas <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="kelas"
                        required
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                        <option value="">Pilih Kelas</option>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        File Excel <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="file"
                        name="file"
                        accept=".xlsx,.xls"
                        required
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600 ring-1 ring-blue-100">
                            <i class="fas fa-circle-info text-xs"></i>
                        </div>

                        <p class="text-xs leading-relaxed text-slate-600">
                            Upload Excel asli daftar absen. Sistem akan mencari header <strong>NISN</strong> dan <strong>NAMA</strong> otomatis di setiap sheet. Nama sheet akan dijadikan jurusan atau rombel.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
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

                <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-red-600 ring-1 ring-red-100">
                            <i class="fas fa-triangle-exclamation text-sm"></i>
                        </div>

                        <div class="text-sm leading-relaxed text-slate-700">
                            <p class="font-bold text-slate-900">
                                Pilih kelas/rombel yang ingin dinaikkan.
                            </p>

                            <p class="mt-2">
                                Yang diproses hanya kelas/rombel yang dicentang.
                                Kalau dalam satu rombel ada siswa tidak naik, gunakan tombol <strong>Naikkan Dipilih</strong> dari tabel user.
                            </p>

                            <p class="mt-2 text-xs text-slate-500">
                                Aturan: X naik ke XI, XI naik ke XII, XII dihapus karena dianggap lulus.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-700">
                        Pilih Kelas/Rombel <span class="text-red-500">*</span>
                    </label>

                    <div class="max-h-72 space-y-4 overflow-y-auto rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        @forelse($kelasRombelList->groupBy('nama_kelas') as $namaKelas => $rombels)
                            <div>
                                <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Kelas {{ $namaKelas }}
                                </p>

                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach($rombels as $rombel)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-purple-200 hover:bg-purple-50">
                                            <input
                                                type="checkbox"
                                                name="rombels[]"
                                                value="{{ $rombel->nama_kelas }}|{{ $rombel->jurusan }}"
                                                class="h-4 w-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500"
                                            >

                                            <span class="font-medium">
                                                {{ $rombel->nama_kelas }} - {{ $rombel->jurusan }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl bg-white px-4 py-6 text-center text-sm text-slate-500">
                                Belum ada data kelas/rombel.
                            </div>
                        @endforelse
                    </div>
                </div>

                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <input
                        type="checkbox"
                        name="confirm"
                        value="1"
                        required
                        class="mt-1 h-4 w-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500"
                    >

                    <span class="text-sm leading-relaxed text-slate-600">
                        Saya yakin ingin menjalankan proses naik kelas untuk kelas/rombel yang dipilih.
                    </span>
                </label>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-700"
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