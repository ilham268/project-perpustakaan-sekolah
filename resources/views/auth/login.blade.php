<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Lantera</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50">

    <div class="min-h-screen flex">

        <!-- Left Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">

            <div class="max-w-md w-full space-y-8">

                <!-- Logo -->
                <div>
                    <h1 class="text-4xl font-bold text-emerald-600 tracking-tight">
                        Lantera
                    </h1>
                </div>

                <!-- Login Form -->
                <div class="mt-8 bg-white p-8 rounded-2xl shadow-lg border border-slate-100">

                    <h2 class="text-3xl font-bold text-slate-800 mb-2">
                        Masuk
                    </h2>

                    <p class="text-slate-500 text-sm mb-6">
                        Silakan masukkan nomor identitas dan kata sandi
                    </p>

                    <form class="space-y-6" action="{{ route('login') }}" method="POST">
                        @csrf

                        <!-- Nomor Identitas -->
                        <div>
                            <label for="nomor_identitas" class="block text-sm font-medium text-slate-700 mb-2">
                                Nomor Identitas
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="nomor_identitas"
                                name="nomor_identitas"
                                type="text"
                                class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                                placeholder="Masukkan Nomor Identitas"
                                value="{{ old('nomor_identitas') }}"
                            >

                            @error('nomor_identitas')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

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
                                    placeholder="Masukkan kata sandi"
                                >

                                <button
                                    type="button"
                                    onclick="togglePassword()"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center"
                                >
                                    <i id="eyeIcon" class="fas fa-eye text-slate-400 hover:text-emerald-500 transition"></i>
                                </button>

                            </div>

                            @error('password')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button
                                type="submit"
                                class="w-full flex justify-center py-3 px-4 rounded-xl shadow-md text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400 transition-all duration-300"
                            >
                                Masuk
                            </button>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-sm text-slate-500">
                                Belum punya akun?

                                <a
                                    href="{{ route('register') }}"
                                    class="font-medium text-emerald-600 hover:text-emerald-700 transition"
                                >
                                    Daftar Disini!
                                </a>
                            </p>
                        </div>

                    </form>

                </div>

            </div>

        </div>

        <!-- Right Side -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700 items-center justify-center p-12 relative overflow-hidden">

            <!-- Glow Effect -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>

            <div class="max-w-lg text-white relative z-10">

                <h2 class="text-4xl font-bold mb-4">
                    Selamat datang di Lantera
                </h2>

                <p class="text-lg text-emerald-50 mb-8 leading-relaxed">
                    Solusi pintar untuk komunikasi dan administrasi Perpustakaan.
                </p>

                <!-- Card -->
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
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>