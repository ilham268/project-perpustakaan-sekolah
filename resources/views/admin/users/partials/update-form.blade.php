<form
    action="{{ route('users.update', $user->id) }}"
    method="POST"
    class="space-y-5"
    x-data="{ selectedRole: '{{ old('role', $user->role) }}' }"
>
    @csrf
    @method('PUT')

    {{-- Header Info --}}
    <div class="rounded-2xl border border-[var(--hairline)] bg-[var(--emerald-tint)]/70 p-4">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-[var(--emerald-deep)] ring-1 ring-[var(--emerald)]/15">
                <i class="fas fa-user-pen"></i>
            </div>

            <div>
                <h4 class="font-display text-sm font-semibold text-[var(--forest)]">
                    Edit Data User
                </h4>
                <p class="mt-1 text-xs leading-relaxed text-[var(--muted)]">
                    Perbarui informasi user. Password boleh dikosongkan jika tidak ingin diubah.
                </p>
            </div>
        </div>
    </div>

    {{-- Nama --}}
    <div>
        <label for="name_{{ $user->id }}" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
            <i class="fas fa-user text-xs text-[var(--muted)]"></i>
            Nama Lengkap
            <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="name_{{ $user->id }}"
            name="name"
            placeholder="Masukkan nama lengkap"
            value="{{ old('name', $user->name) }}"
            class="block w-full rounded-xl border px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:outline-none focus:ring-4
            @error('name')
                border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100
            @else
                border-[var(--hairline)] bg-[var(--paper)] focus:border-[var(--emerald)] focus:bg-white focus:ring-[var(--emerald-tint)]
            @enderror"
            required
        >

        @error('name')
            <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nomor Identitas --}}
    <div>
        <label for="nomor_identitas_{{ $user->id }}" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
            <i class="fas fa-id-card text-xs text-[var(--muted)]"></i>
            Nomor Identitas
            <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="nomor_identitas_{{ $user->id }}"
            name="nomor_identitas"
            placeholder="Masukkan nomor identitas"
            value="{{ old('nomor_identitas', $user->nomor_identitas) }}"
            class="block w-full rounded-xl border px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:outline-none focus:ring-4
            @error('nomor_identitas')
                border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100
            @else
                border-[var(--hairline)] bg-[var(--paper)] focus:border-[var(--emerald)] focus:bg-white focus:ring-[var(--emerald-tint)]
            @enderror"
            required
        >

        @error('nomor_identitas')
            <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kelas --}}
    <div>
        <label for="kelas_id_{{ $user->id }}" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
            <i class="fas fa-school text-xs text-[var(--muted)]"></i>
            Kelas
            <span x-show="selectedRole === 'siswa'" class="text-red-500">*</span>
        </label>

        <select
            id="kelas_id_{{ $user->id }}"
            name="kelas_id"
            class="block w-full rounded-xl border px-4 py-3 text-sm text-[var(--text)] shadow-sm transition focus:outline-none focus:ring-4
            @error('kelas_id')
                border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100
            @else
                border-[var(--hairline)] bg-[var(--paper)] focus:border-[var(--emerald)] focus:bg-white focus:ring-[var(--emerald-tint)]
            @enderror"
            :required="selectedRole === 'siswa'"
        >
            <option value="">Pilih Kelas</option>

            @foreach(($kelasList ?? collect()) as $kelas)
                <option
                    value="{{ $kelas->id }}"
                    {{ old('kelas_id') == $kelas->id || (!old('kelas_id') && $user->kelas == $kelas->nama_kelas) ? 'selected' : '' }}
                >
                    {{ $kelas->nama_kelas }} - {{ $kelas->jurusan }}
                </option>
            @endforeach
        </select>

        @error('kelas_id')
            <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror

        @if(($kelasList ?? collect())->isEmpty())
            <div class="mt-2 flex items-start gap-2 rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600 ring-1 ring-red-100">
                <i class="fas fa-circle-exclamation mt-0.5 text-xs"></i>
                <span>Data kelas belum ada. Tambahkan kelas dulu di bagian Kelola Kelas.</span>
            </div>
        @endif

        @if($user->kelas)
            <div class="mt-2 inline-flex items-center gap-2 rounded-xl bg-[var(--sand)]/60 px-3 py-2 text-xs font-medium text-[var(--text)]/70">
                <i class="fas fa-circle-info text-[var(--muted)]"></i>
                Kelas saat ini: {{ $user->kelas }}{{ $user->jurusan ? ' - ' . $user->jurusan : '' }}
            </div>
        @endif
    </div>

    {{-- Role & Password --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Role --}}
        <div>
            <label for="role_{{ $user->id }}" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                <i class="fas fa-user-shield text-xs text-[var(--muted)]"></i>
                Role
                <span class="text-red-500">*</span>
            </label>

            <select
                id="role_{{ $user->id }}"
                name="role"
                x-model="selectedRole"
                class="block w-full rounded-xl border px-4 py-3 text-sm text-[var(--text)] shadow-sm transition focus:outline-none focus:ring-4
                @error('role')
                    border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100
                @else
                    border-[var(--hairline)] bg-[var(--paper)] focus:border-[var(--emerald)] focus:bg-white focus:ring-[var(--emerald-tint)]
                @enderror"
                required
            >
                <option value="">Pilih Role</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>

            @error('role')
                <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password_{{ $user->id }}" class="mb-2 flex items-center gap-2 text-sm font-semibold text-[var(--text)]">
                <i class="fas fa-lock text-xs text-[var(--muted)]"></i>
                Password Baru
            </label>

            <input
                type="password"
                id="password_{{ $user->id }}"
                name="password"
                placeholder="Kosongkan jika tidak diubah"
                class="block w-full rounded-xl border px-4 py-3 text-sm text-[var(--text)] shadow-sm placeholder:text-[var(--muted)] transition focus:outline-none focus:ring-4
                @error('password')
                    border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100
                @else
                    border-[var(--hairline)] bg-[var(--paper)] focus:border-[var(--emerald)] focus:bg-white focus:ring-[var(--emerald-tint)]
                @enderror"
            >

            <p class="mt-2 flex items-center gap-1.5 text-xs text-[var(--muted)]">
                <i class="fas fa-circle-info text-[var(--muted)]"></i>
                Kosongkan jika tidak ingin mengubah password.
            </p>

            @error('password')
                <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:items-center sm:justify-end">
        <button
            type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--emerald-deep)] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[var(--forest)]"
        >
            <i class="fas fa-save text-xs"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>