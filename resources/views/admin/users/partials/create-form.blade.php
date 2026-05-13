<div x-data="{
    errors: {},
    isSubmitting: false,

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
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
            :class="errors.name ? 'border-red-500 focus:ring-red-500' : ''"
            required
        >
        <p x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="mt-1 text-sm text-red-600"></p>
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
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
            :class="errors.nomor_identitas ? 'border-red-500 focus:ring-red-500' : ''"
            required
        >
        <p x-show="errors.nomor_identitas" x-text="errors.nomor_identitas ? errors.nomor_identitas[0] : ''" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Kelas -->
    <div>
        <label for="kelas" class="block text-sm font-medium text-slate-700 mb-2">
            Kelas
        </label>
        <select
            id="kelas"
            name="kelas"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
            :class="errors.kelas ? 'border-red-500 focus:ring-red-500' : ''"
        >
            <option value="">Pilih Kelas</option>
            <option value="10">Kelas 10</option>
            <option value="11">Kelas 11</option>
            <option value="12">Kelas 12</option>
        </select>
        <p x-show="errors.kelas" x-text="errors.kelas ? errors.kelas[0] : ''" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Jurusan -->
    <div>
        <label for="jurusan" class="block text-sm font-medium text-slate-700 mb-2">
            Jurusan
        </label>
        <select
            id="jurusan"
            name="jurusan"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-slate-300 focus:ring-emerald-500"
            :class="errors.jurusan ? 'border-red-500 focus:ring-red-500' : ''"
        >
            <option value="">Pilih Jurusan</option>
            <optgroup label="TKJ">
                <option value="TKJ 1">TKJ 1</option>
                <option value="TKJ 2">TKJ 2</option>
            </optgroup>
            <optgroup label="KULINER">
                <option value="KULINER 1">KULINER 1</option>
                <option value="KULINER 2">KULINER 2</option>
            </optgroup>
            <optgroup label="APL">
                <option value="APL 1">APL 1</option>
                <option value="APL 2">APL 2</option>
            </optgroup>
            <optgroup label="TITL">
                <option value="TITL 1">TITL 1</option>
                <option value="TITL 2">TITL 2</option>
                <option value="TITL 3">TITL 3</option>
            </optgroup>
            <optgroup label="TKI">
                <option value="TKI 1">TKI 1</option>
                <option value="TKI 2">TKI 2</option>
                <option value="TKI 3">TKI 3</option>
            </optgroup>
            <optgroup label="TPTUP">
                <option value="TPTUP 1">TPTUP 1</option>
                <option value="TPTUP 2">TPTUP 2</option>
            </optgroup>
            <optgroup label="DKV">
                <option value="DKV 1">DKV 1</option>
                <option value="DKV 2">DKV 2</option>
            </optgroup>
            <optgroup label="TOI">
                <option value="TOI 1">TOI 1</option>
                <option value="TOI 2">TOI 2</option>
            </optgroup>
        </select>
        <p x-show="errors.jurusan" x-text="errors.jurusan ? errors.jurusan[0] : ''" class="mt-1 text-sm text-red-600"></p>
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
                Password<span class="text-red-500">*</span>
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