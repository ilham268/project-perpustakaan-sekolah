<form id="updateUserForm" class="space-y-4" action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Nama -->
    <div>
        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
            Nama Lengkap<span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            id="name"
            name="name"
            placeholder="Masukkan nama lengkap"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('name') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
            value="{{ old('name', $user->name) }}"
            required
        >
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nomor Identitas -->
    <div>
        <label for="nomor_identitas" class="block text-sm font-medium text-slate-700 mb-2">
            Nomor Identitas<span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            id="nomor_identitas"
            name="nomor_identitas"
            placeholder="Masukkan nomor identitas"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('nomor_identitas') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
            value="{{ old('nomor_identitas', $user->nomor_identitas) }}"
            required
        >
        @error('nomor_identitas')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kelas -->
    <div>
        <label for="kelas" class="block text-sm font-medium text-slate-700 mb-2">
            Kelas
        </label>
        <select
            id="kelas"
            name="kelas"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('kelas') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
        >
            <option value="">Pilih Kelas</option>
            <option value="10" {{ old('kelas', $user->kelas) == '10' ? 'selected' : '' }}>Kelas 10</option>
            <option value="11" {{ old('kelas', $user->kelas) == '11' ? 'selected' : '' }}>Kelas 11</option>
            <option value="12" {{ old('kelas', $user->kelas) == '12' ? 'selected' : '' }}>Kelas 12</option>
        </select>
        @error('kelas')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Jurusan -->
    <div>
        <label for="jurusan" class="block text-sm font-medium text-slate-700 mb-2">
            Jurusan
        </label>
        <select
            id="jurusan"
            name="jurusan"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('jurusan') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
        >
            <option value="">Pilih Jurusan</option>
            <optgroup label="TKJ">
                <option value="TKJ 1" {{ old('jurusan', $user->jurusan) == 'TKJ 1' ? 'selected' : '' }}>TKJ 1</option>
                <option value="TKJ 2" {{ old('jurusan', $user->jurusan) == 'TKJ 2' ? 'selected' : '' }}>TKJ 2</option>
            </optgroup>
            <optgroup label="KULINER">
                <option value="KULINER 1" {{ old('jurusan', $user->jurusan) == 'KULINER 1' ? 'selected' : '' }}>KULINER 1</option>
                <option value="KULINER 2" {{ old('jurusan', $user->jurusan) == 'KULINER 2' ? 'selected' : '' }}>KULINER 2</option>
            </optgroup>
            <optgroup label="APL">
                <option value="APL 1" {{ old('jurusan', $user->jurusan) == 'APL 1' ? 'selected' : '' }}>APL 1</option>
                <option value="APL 2" {{ old('jurusan', $user->jurusan) == 'APL 2' ? 'selected' : '' }}>APL 2</option>
            </optgroup>
            <optgroup label="TITL">
                <option value="TITL 1" {{ old('jurusan', $user->jurusan) == 'TITL 1' ? 'selected' : '' }}>TITL 1</option>
                <option value="TITL 2" {{ old('jurusan', $user->jurusan) == 'TITL 2' ? 'selected' : '' }}>TITL 2</option>
                <option value="TITL 3" {{ old('jurusan', $user->jurusan) == 'TITL 3' ? 'selected' : '' }}>TITL 3</option>
            </optgroup>
            <optgroup label="TKI">
                <option value="TKI 1" {{ old('jurusan', $user->jurusan) == 'TKI 1' ? 'selected' : '' }}>TKI 1</option>
                <option value="TKI 2" {{ old('jurusan', $user->jurusan) == 'TKI 2' ? 'selected' : '' }}>TKI 2</option>
                <option value="TKI 3" {{ old('jurusan', $user->jurusan) == 'TKI 3' ? 'selected' : '' }}>TKI 3</option>
            </optgroup>
            <optgroup label="TPTUP">
                <option value="TPTUP 1" {{ old('jurusan', $user->jurusan) == 'TPTUP 1' ? 'selected' : '' }}>TPTUP 1</option>
                <option value="TPTUP 2" {{ old('jurusan', $user->jurusan) == 'TPTUP 2' ? 'selected' : '' }}>TPTUP 2</option>
            </optgroup>
            <optgroup label="DKV">
                <option value="DKV 1" {{ old('jurusan', $user->jurusan) == 'DKV 1' ? 'selected' : '' }}>DKV 1</option>
                <option value="DKV 2" {{ old('jurusan', $user->jurusan) == 'DKV 2' ? 'selected' : '' }}>DKV 2</option>
            </optgroup>
            <optgroup label="TOI">
                <option value="TOI 1" {{ old('jurusan', $user->jurusan) == 'TOI 1' ? 'selected' : '' }}>TOI 1</option>
                <option value="TOI 2" {{ old('jurusan', $user->jurusan) == 'TOI 2' ? 'selected' : '' }}>TOI 2</option>
            </optgroup>
        </select>
        @error('jurusan')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Role & Password in 2 columns -->
    <div class="grid grid-cols-2 gap-4">
        <!-- Role -->
        <div>
            <label for="role" class="block text-sm font-medium text-slate-700 mb-2">
                Role<span class="text-red-500">*</span>
            </label>
            <select
                id="role"
                name="role"
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
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                Password Baru
            </label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="••••••••"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 @error('password') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-emerald-500 @enderror"
            >
            <p class="mt-1 text-xs text-slate-500">Kosongkan jika tidak ingin mengubah password</p>
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