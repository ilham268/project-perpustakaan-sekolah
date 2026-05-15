<div
    x-data="{
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
            .then(async (res) => {
                const data = await res.json();

                if (!res.ok) {
                    throw data;
                }

                return data;
            })
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route('users.index') }}?created=1';
                } else if (data.errors) {
                    this.errors = data.errors;
                    this.isSubmitting = false;
                }
            })
            .catch(error => {
                if (error.errors) {
                    this.errors = error.errors;
                } else {
                    console.error(error);
                }

                this.isSubmitting = false;
            });
        }
    }"
>
    <form @submit="submitForm" class="space-y-5">
        @csrf

        {{-- Header Info --}}
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 ring-1 ring-emerald-100">
                    <i class="fas fa-user-plus"></i>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-800">
                        Tambah Data User
                    </h4>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                        Lengkapi data pengguna baru. Kelas wajib diisi jika role yang dipilih adalah siswa.
                    </p>
                </div>
            </div>
        </div>

        {{-- Nama --}}
        <div>
            <label for="name" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="fas fa-user text-xs text-slate-400"></i>
                Nama Lengkap
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Masukkan nama lengkap"
                class="block w-full rounded-2xl border px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:outline-none focus:ring-4"
                :class="errors.name
                    ? 'border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100'
                    : 'border-slate-200 bg-slate-50/80 focus:border-emerald-400 focus:bg-white focus:ring-emerald-100'"
                required
            >

            <p
                x-show="errors.name"
                x-text="errors.name ? errors.name[0] : ''"
                class="mt-1.5 text-sm font-medium text-red-600"
            ></p>
        </div>

        {{-- Nomor Identitas --}}
        <div>
            <label for="nomor_identitas" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="fas fa-id-card text-xs text-slate-400"></i>
                Nomor Identitas
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                id="nomor_identitas"
                name="nomor_identitas"
                placeholder="Masukkan nomor identitas"
                class="block w-full rounded-2xl border px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:outline-none focus:ring-4"
                :class="errors.nomor_identitas
                    ? 'border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100'
                    : 'border-slate-200 bg-slate-50/80 focus:border-emerald-400 focus:bg-white focus:ring-emerald-100'"
                required
            >

            <p
                x-show="errors.nomor_identitas"
                x-text="errors.nomor_identitas ? errors.nomor_identitas[0] : ''"
                class="mt-1.5 text-sm font-medium text-red-600"
            ></p>
        </div>

        {{-- Kelas --}}
        <div>
            <label for="kelas_id" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="fas fa-school text-xs text-slate-400"></i>
                Kelas
                <span x-show="selectedRole === 'siswa'" class="text-red-500">*</span>
            </label>

            <select
                id="kelas_id"
                name="kelas_id"
                class="block w-full rounded-2xl border px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:outline-none focus:ring-4"
                :class="errors.kelas_id
                    ? 'border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100'
                    : 'border-slate-200 bg-slate-50/80 focus:border-emerald-400 focus:bg-white focus:ring-emerald-100'"
                :required="selectedRole === 'siswa'"
            >
                <option value="">Pilih Kelas</option>

                @foreach(($kelasList ?? collect()) as $kelas)
                    <option value="{{ $kelas->id }}">
                        {{ $kelas->nama_kelas }} - {{ $kelas->jurusan }}
                    </option>
                @endforeach
            </select>

            <p
                x-show="errors.kelas_id"
                x-text="errors.kelas_id ? errors.kelas_id[0] : ''"
                class="mt-1.5 text-sm font-medium text-red-600"
            ></p>

            @if(($kelasList ?? collect())->isEmpty())
                <div class="mt-2 flex items-start gap-2 rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600 ring-1 ring-red-100">
                    <i class="fas fa-circle-exclamation mt-0.5 text-xs"></i>
                    <span>Data kelas belum ada. Tambahkan kelas dulu di bagian Kelola Kelas.</span>
                </div>
            @endif
        </div>

        {{-- Role & Password --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- Role --}}
            <div>
                <label for="role" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <i class="fas fa-user-shield text-xs text-slate-400"></i>
                    Role
                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="role"
                    name="role"
                    x-model="selectedRole"
                    class="block w-full rounded-2xl border px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:outline-none focus:ring-4"
                    :class="errors.role
                        ? 'border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100'
                        : 'border-slate-200 bg-slate-50/80 focus:border-emerald-400 focus:bg-white focus:ring-emerald-100'"
                    required
                >
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                    <option value="siswa">Siswa</option>
                </select>

                <p
                    x-show="errors.role"
                    x-text="errors.role ? errors.role[0] : ''"
                    class="mt-1.5 text-sm font-medium text-red-600"
                ></p>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <i class="fas fa-lock text-xs text-slate-400"></i>
                    Password
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    class="block w-full rounded-2xl border px-4 py-3 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 transition focus:outline-none focus:ring-4"
                    :class="errors.password
                        ? 'border-red-300 bg-red-50/40 focus:border-red-400 focus:ring-red-100'
                        : 'border-slate-200 bg-slate-50/80 focus:border-emerald-400 focus:bg-white focus:ring-emerald-100'"
                    required
                >

                <p
                    x-show="errors.password"
                    x-text="errors.password ? errors.password[0] : ''"
                    class="mt-1.5 text-sm font-medium text-red-600"
                ></p>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:items-center sm:justify-end">
            <button
                type="submit"
                :disabled="isSubmitting"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-emerald-100 transition hover:-translate-y-0.5 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0"
            >
                <i x-show="!isSubmitting" class="fas fa-save text-xs"></i>
                <i x-show="isSubmitting" class="fas fa-spinner fa-spin text-xs"></i>

                <span x-show="!isSubmitting">Simpan User</span>
                <span x-show="isSubmitting">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>