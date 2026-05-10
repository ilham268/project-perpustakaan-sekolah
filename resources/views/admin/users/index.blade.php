@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
    <!-- Flash Messages -->
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

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800">Kelola User</h3>
        <button
            @click="$dispatch('open-modal', 'create-user')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors text-sm font-medium shadow-sm"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah User</span>
        </button>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('users.index') }}" id="filter-form" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 text-sm"></i>
            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ request('search') }}"
                placeholder="Cari nama atau nomor identitas..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition"
                autocomplete="off"
            >
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-slate-600">Role :</span>
            <select name="role" id="role-select" class="px-6 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                <option value="">Semua</option>
                <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="peminjam" {{ request('role') == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
            </select>
        </div>
    </form>

    <script>
        (function () {
            var form = document.getElementById('filter-form');
            var searchInput = document.getElementById('search-input');
            var roleSelect = document.getElementById('role-select');
            var debounceTimer;

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 400);
            });

            roleSelect.addEventListener('change', function () {
                form.submit();
            });
        })();
    </script>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-emerald-600 text-white text-sm">
                        <th class="px-5 py-3 text-left font-semibold w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Nama</th>
                        <th class="px-5 py-3 text-left font-semibold">Nomor Identitas</th>
                        <th class="px-5 py-3 text-left font-semibold">Role</th>
                        <th class="px-5 py-3 text-left font-semibold">Tanggal Daftar</th>
                        <th class="px-5 py-3 text-center font-semibold w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $users->firstItem() + $index }}</td>
                        <td class="px-5 py-4 text-sm font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $user->nomor_identitas }}</td>
                        <td class="px-5 py-4">
                            @php
                                $roleColors = [
                                    'admin'     => 'bg-red-100 text-red-700',
                                    'petugas'   => 'bg-emerald-100 text-emerald-700',
                                    'peminjam'  => 'bg-blue-100 text-blue-700',
                                ];
                            @endphp
                            <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    @click="$dispatch('open-modal', 'update-user-{{ $user->id }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-600 transition-colors"
                                    title="Edit User"
                                >
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button
                                    type="button"
                                    @click="$dispatch('open-confirm-delete', { url: '{{ route('users.destroy', $user->id) }}' })"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors"
                                    title="Hapus User"
                                >
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <x-modal name="update-user-{{ $user->id }}" title="Edit User" maxWidth="md">
                        @include('admin.users.partials.update-form', ['user' => $user])
                    </x-modal>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <i class="fas fa-users text-5xl mb-3 block text-slate-300"></i>
                            <p class="text-base font-medium text-slate-500">Tidak ada data user</p>
                            <p class="text-sm text-slate-400 mt-1">Klik tombol "Tambah User" untuk menambahkan user baru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if ($users->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center text-slate-300 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @php
                    $start = max(1, $users->currentPage() - 1);
                    $end   = min($users->lastPage(), $users->currentPage() + 1);
                @endphp

                @if($start > 1)
                    <a href="{{ $users->url(1) }}" class="w-8 h-8 flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">1</a>
                    @if($start > 2)
                        <span class="w-8 h-8 flex items-center justify-center text-sm text-slate-400">...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $users->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center text-sm bg-emerald-600 text-white rounded-lg font-medium">{{ $i }}</span>
                    @else
                        <a href="{{ $users->url($i) }}" class="w-8 h-8 flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">{{ $i }}</a>
                    @endif
                @endfor

                @if($end < $users->lastPage())
                    @if($end < $users->lastPage() - 1)
                        <span class="w-8 h-8 flex items-center justify-center text-sm text-slate-400">...</span>
                    @endif
                    <a href="{{ $users->url($users->lastPage()) }}" class="w-8 h-8 flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">{{ $users->lastPage() }}</a>
                @endif

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center text-slate-300 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="create-user" title="Tambah User" maxWidth="md">
        @include('admin.users.partials.create-form')
    </x-modal>

    <x-confirm-delete />
@endsection