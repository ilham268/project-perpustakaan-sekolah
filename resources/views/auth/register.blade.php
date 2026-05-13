<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Lantera</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50">

    <div class="min-h-screen flex">

        <!-- Left Side - Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">

            <div class="max-w-md w-full space-y-8">

                <!-- Logo -->
                <div>
                    <h1 class="text-4xl font-bold text-emerald-600 tracking-tight">
                        Lantera
                    </h1>
                </div>

                <!-- Register Form -->
                <div class="mt-8 bg-white p-8 rounded-2xl shadow-lg border border-slate-100">

                    <h2 class="text-3xl font-bold text-slate-800 mb-2">
                        Daftar
                    </h2>

                    <p class="text-slate-500 text-sm mb-6">
                        Silakan isi data diri Anda
                    </p>

                    <form class="space-y-6" action="{{ route('register') }}" method="POST">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                                Nama Lengkap
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('name') }}"
                                required
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor Identitas (NISN) -->
                        <div>
                            <label for="nomor_identitas" class="block text-sm font-medium text-slate-700 mb-2">
                                Nomor Identitas (NISN)
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="nomor_identitas"
                                name="nomor_identitas"
                                type="text"
                                class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                                placeholder="Masukkan NISN"
                                value="{{ old('nomor_identitas') }}"
                                required
                            >
                            @error('nomor_identitas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kelas (Dropdown Format Lengkap) -->
                        <div>
                            <label for="kelas" class="block text-sm font-medium text-slate-700 mb-2">
                                Kelas
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="kelas"
                                name="kelas"
                                class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition bg-white"
                                required
                            >
                                <option value="">Pilih Kelas</option>
                                <option value="X RPL 1" {{ old('kelas') == 'X RPL 1' ? 'selected' : '' }}>X RPL 1</option>
                                <option value="X RPL 2" {{ old('kelas') == 'X RPL 2' ? 'selected' : '' }}>X RPL 2</option>
                                <option value="XI RPL 1" {{ old('kelas') == 'XI RPL 1' ? 'selected' : '' }}>XI RPL 1</option>
                                <option value="XI RPL 2" {{ old('kelas') == 'XI RPL 2' ? 'selected' : '' }}>XI RPL 2</option>
                                <option value="XII RPL 1" {{ old('kelas') == 'XII RPL 1' ? 'selected' : '' }}>XII RPL 1</option>
                                <option value="XII RPL 2" {{ old('kelas') == 'XII RPL 2' ? 'selected' : '' }}>XII RPL 2</option>
                                <option value="X TKJ 1" {{ old('kelas') == 'X TKJ 1' ? 'selected' : '' }}>X TKJ 1</option>
                                <option value="X TKJ 2" {{ old('kelas') == 'X TKJ 2' ? 'selected' : '' }}>X TKJ 2</option>
                                <option value="XI TKJ 1" {{ old('kelas') == 'XI TKJ 1' ? 'selected' : '' }}>XI TKJ 1</option>
                                <option value="XI TKJ 2" {{ old('kelas') == 'XI TKJ 2' ? 'selected' : '' }}>XI TKJ 2</option>
                                <option value="XII TKJ 1" {{ old('kelas') == 'XII TKJ 1' ? 'selected' : '' }}>XII TKJ 1</option>
                                <option value="XII TKJ 2" {{ old('kelas') == 'XII TKJ 2' ? 'selected' : '' }}>XII TKJ 2</option>
                                <option value="X TITL 1" {{ old('kelas') == 'X TITL 1' ? 'selected' : '' }}>X TITL 1</option>
                                <option value="X TITL 2" {{ old('kelas') == 'X TITL 2' ? 'selected' : '' }}>X TITL 2</option>
                                <option value="XI TITL 1" {{ old('kelas') == 'XI TITL 1' ? 'selected' : '' }}>XI TITL 1</option>
                                <option value="XI TITL 2" {{ old('kelas') == 'XI TITL 2' ? 'selected' : '' }}>XI TITL 2</option>
                                <option value="XII TITL 1" {{ old('kelas') == 'XII TITL 1' ? 'selected' : '' }}>XII TITL 1</option>
                                <option value="XII TITL 2" {{ old('kelas') == 'XII TITL 2' ? 'selected' : '' }}>XII TITL 2</option>
                                <option value="X TKI 1" {{ old('kelas') == 'X TKI 1' ? 'selected' : '' }}>X TKI 1</option>
                                <option value="X TKI 2" {{ old('kelas') == 'X TKI 2' ? 'selected' : '' }}>X TKI 2</option>
                                <option value="XI TKI 1" {{ old('kelas') == 'XI TKI 1' ? 'selected' : '' }}>XI TKI 1</option>
                                <option value="XI TKI 2" {{ old('kelas') == 'XI TKI 2' ? 'selected' : '' }}>XI TKI 2</option>
                                <option value="XII TKI 1" {{ old('kelas') == 'XII TKI 1' ? 'selected' : '' }}>XII TKI 1</option>
                                <option value="XII TKI 2" {{ old('kelas') == 'XII TKI 2' ? 'selected' : '' }}>XII TKI 2</option>
                            </select>
                            @error('kelas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jurusan (Hidden karena sudah termasuk di kelas) -->
                        <input type="hidden" name="jurusan" value="Otomatis">

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                                Kata Sandi
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                                    placeholder="Minimal 8 karakter"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center"
                                >
                                    <i id="eyeIconPassword" class="fas fa-eye text-slate-400 hover:text-emerald-500 transition"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                                Konfirmasi Kata Sandi
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                                    placeholder="Ketik ulang kata sandi"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation')"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center"
                                >
                                    <i id="eyeIconConfirm" class="fas fa-eye text-slate-400 hover:text-emerald-500 transition"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button
                                type="submit"
                                class="w-full flex justify-center py-3 px-4 rounded-xl shadow-md text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400 transition-all duration-300"
                            >
                                Daftar
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-sm text-slate-500">
                                Sudah punya akun?

                                <a
                                    href="{{ route('login') }}"
                                    class="font-medium text-emerald-600 hover:text-emerald-700 transition"
                                >
                                    Masuk Disini!
                                </a>
                            </p>
                        </div>

                    </form>

                </div>

            </div>

        </div>

        <!-- Right Side -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700 items-center justify-center p-12 relative overflow-hidden">

            <div class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>

            <div class="max-w-lg text-white relative z-10">

                <h2 class="text-4xl font-bold mb-4">
                    Bergabunglah dengan Lantera
                </h2>

                <p class="text-lg text-emerald-50 mb-8 leading-relaxed">
                    Daftar sekarang dan nikmati kemudahan mengakses perpustakaan digital.
                </p>

                <div class="rounded-3xl bg-white/10 border border-white/20 p-8 backdrop-blur-md shadow-2xl">
                    <div class="mx-auto flex items-center justify-center">
                        <img
                            src="{{ asset('image/smkn1cerme.png') }}"
                            alt="Logo SMKN 1"
                            class="h-52 w-auto drop-shadow-2xl hover:scale-105 transition duration-300"
                        >
                    </div>
                    <p class="mt-6 text-center text-lg font-semibold text-white">
                        SMKN 1 CERME
                    </p>
                    <p class="mt-2 text-center text-sm font-medium text-emerald-50">
                        Platform Sistem Informasi Perpustakaan
                    </p>
                </div>

            </div>

        </div>

    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId === 'password' ? 'eyeIconPassword' : 'eyeIconConfirm');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>