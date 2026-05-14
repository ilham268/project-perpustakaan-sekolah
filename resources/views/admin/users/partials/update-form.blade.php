<form
    action="{{ route('users.update', $user->id) }}"
    method="POST"
    class="space-y-4"
    x-data="{ selectedRole: '{{ old('role', $user->role) }}' }"
>
    @csrf
    @method('PUT')

    <!-- Nama -->
    <div>
        <label for="name_{{ $user->id }}" class="block text-sm font-medium text-slate-700 mb-2">
            Nama Lengkap
            <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="name_{{ $user->id }}"
            name="name"
            placeholder="Masukkan nama lengkap"
            value="{{ old('name', $user->name) }}"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('name') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
            required
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nomor Identitas -->
    <div>
        <label for="nomor_identitas_{{ $user->id }}" class="block text-sm font-medium text-slate-700 mb-2">
            Nomor Identitas
            <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="nomor_identitas_{{ $user->id }}"
            name="nomor_identitas"
            placeholder="Masukkan nomor identitas"
            value="{{ old('nomor_identitas', $user->nomor_identitas) }}"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('nomor_identitas') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
            required
        >

        @error('nomor_identitas')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kelas dari Database -->
    <div>
        <label for="kelas_id_{{ $user->id }}" class="block text-sm font-medium text-slate-700 mb-2">
            Kelas
            <span x-show="selectedRole === 'siswa'" class="text-red-500">*</span>
        </label>

        <select
            id="kelas_id_{{ $user->id }}"
            name="kelas_id"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('kelas_id') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
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
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @if(($kelasList ?? collect())->isEmpty())
            <p class="mt-1 text-sm text-red-600">
                Data kelas belum ada. Tambahkan kelas dulu di bagian Kelola Kelas.
            </p>
        @endif

        @if($user->kelas)
            <p class="mt-1 text-xs text-slate-500">
                Kelas saat ini: {{ $user->kelas }}{{ $user->jurusan ? ' - ' . $user->jurusan : '' }}
            </p>
        @endif
    </div>

    <!-- Role & Password -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Role -->
        <div>
            <label for="role_{{ $user->id }}" class="block text-sm font-medium text-slate-700 mb-2">
                Role
                <span class="text-red-500">*</span>
            </label>

            <select
                id="role_{{ $user->id }}"
                name="role"
                x-model="selectedRole"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('role') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
                required
            >
                <option value="">Pilih Role</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>

            @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password_{{ $user->id }}" class="block text-sm font-medium text-slate-700 mb-2">
                Password Baru
            </label>

            <input
                type="password"
                id="password_{{ $user->id }}"
                name="password"
                placeholder="Kosongkan jika tidak diubah"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('password') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
            >

            <p class="mt-1 text-xs text-slate-500">
                Kosongkan jika tidak ingin mengubah password
            </p>

            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end pt-4">
        <button
            type="submit"
            class="px-6 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors font-medium"
        >
            Simpan
        </button>
    </div>
</form>