<div x-data="{
    errors: {},
    isSubmitting: false,
    selectedRole: '',

    submitForm(event) {
        event.preventDefault();
        this.errors = {};
        this.isSubmitting = true;

        const formData = new FormData(event.target);

        fetch('{{ route('users.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route('users.index') }}?created=1';
            } else if (data.errors) {
                this.errors = data.errors;
                this.isSubmitting = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSubmitting = false;
        });
    }
}">
    <form @submit="submitForm" class="space-y-4">
        @csrf

        <!-- Nama -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                Nama Lengkap
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Masukkan nama lengkap"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
                :class="errors.name ? 'border-red-500 focus:ring-red-500' : ''"
                required
            >

            <p x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="mt-1 text-sm text-red-600"></p>
        </div>

        <!-- Nomor Identitas -->
        <div>
            <label for="nomor_identitas" class="block text-sm font-medium text-slate-700 mb-2">
                Nomor Identitas
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                id="nomor_identitas"
                name="nomor_identitas"
                placeholder="Masukkan nomor identitas"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
                :class="errors.nomor_identitas ? 'border-red-500 focus:ring-red-500' : ''"
                required
            >

            <p x-show="errors.nomor_identitas" x-text="errors.nomor_identitas ? errors.nomor_identitas[0] : ''" class="mt-1 text-sm text-red-600"></p>
        </div>

        <!-- Kelas dari Database -->
        <div>
            <label for="kelas_id" class="block text-sm font-medium text-slate-700 mb-2">
                Kelas
                <span x-show="selectedRole === 'siswa'" class="text-red-500">*</span>
            </label>

            <select
                id="kelas_id"
                name="kelas_id"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
                :class="errors.kelas_id ? 'border-red-500 focus:ring-red-500' : ''"
                :required="selectedRole === 'siswa'"
            >
                <option value="">Pilih Kelas</option>

                @foreach(($kelasList ?? collect()) as $kelas)
                    <option value="{{ $kelas->id }}">
                        {{ $kelas->nama_kelas }} - {{ $kelas->jurusan }}
                    </option>
                @endforeach
            </select>

            <p x-show="errors.kelas_id" x-text="errors.kelas_id ? errors.kelas_id[0] : ''" class="mt-1 text-sm text-red-600"></p>

            @if(($kelasList ?? collect())->isEmpty())
                <p class="mt-1 text-sm text-red-600">
                    Data kelas belum ada. Tambahkan kelas dulu di bagian Kelola Kelas.
                </p>
            @endif
        </div>

        <!-- Role & Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-slate-700 mb-2">
                    Role
                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="role"
                    name="role"
                    x-model="selectedRole"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
                    :class="errors.role ? 'border-red-500 focus:ring-red-500' : ''"
                    required
                >
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                    <option value="siswa">Siswa</option>
                </select>

                <p x-show="errors.role" x-text="errors.role ? errors.role[0] : ''" class="mt-1 text-sm text-red-600"></p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                    Password
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
                    :class="errors.password ? 'border-red-500 focus:ring-red-500' : ''"
                    required
                >

                <p x-show="errors.password" x-text="errors.password ? errors.password[0] : ''" class="mt-1 text-sm text-red-600"></p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button
                type="submit"
                :disabled="isSubmitting"
                class="px-6 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-show="!isSubmitting">Simpan</span>
                <span x-show="isSubmitting">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>