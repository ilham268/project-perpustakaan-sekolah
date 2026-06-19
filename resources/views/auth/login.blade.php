<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Lantera</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatSoft {
            0%, 100% {
                transform: translateY(0px) translateX(0px) scale(1);
            }
            50% {
                transform: translateY(-14px) translateX(8px) scale(1.04);
            }
        }

        @keyframes shine {
            0% {
                transform: translateX(-130%) rotate(20deg);
            }
            100% {
                transform: translateX(190%) rotate(20deg);
            }
        }

        @keyframes pulseRing {
            0% {
                transform: scale(.94);
                opacity: .65;
            }
            70% {
                transform: scale(1.12);
                opacity: 0;
            }
            100% {
                opacity: 0;
            }
        }

        @keyframes blinkCaret {
            0%, 45% {
                opacity: 1;
            }
            46%, 100% {
                opacity: 0;
            }
        }

        @keyframes borderGlow {
            0%, 100% {
                box-shadow: 0 0 0 rgba(52, 211, 153, 0);
            }
            50% {
                box-shadow: 0 0 32px rgba(52, 211, 153, .22);
            }
        }

        .fade-up {
            animation: fadeUp .75s ease both;
        }

        .delay-100 { animation-delay: .1s; }
        .delay-200 { animation-delay: .2s; }
        .delay-300 { animation-delay: .3s; }
        .delay-400 { animation-delay: .4s; }

        .floating-orb {
            animation: floatSoft 7s ease-in-out infinite;
        }

        .floating-orb-2 {
            animation-delay: -2s;
        }

        .floating-orb-3 {
            animation-delay: -4s;
        }

        .login-glass {
            background: linear-gradient(145deg, rgba(255, 255, 255, .94), rgba(255, 255, 255, .82));
            backdrop-filter: blur(18px);
            box-shadow:
                0 28px 80px rgba(15, 118, 110, .15),
                inset 0 1px 0 rgba(255, 255, 255, .9);
        }

        .hero-glass {
            background: linear-gradient(145deg, rgba(255, 255, 255, .14), rgba(255, 255, 255, .08));
            backdrop-filter: blur(20px);
            animation: borderGlow 4s ease-in-out infinite;
        }

        .button-shine::before {
            content: "";
            position: absolute;
            top: -60%;
            left: -45%;
            width: 42%;
            height: 220%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .45), transparent);
            animation: shine 3.4s ease-in-out infinite;
        }

        .logo-ring::before,
        .logo-ring::after {
            content: "";
            position: absolute;
            inset: -12px;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, .45);
            animation: pulseRing 2.7s ease-out infinite;
        }

        .logo-ring::after {
            animation-delay: 1.25s;
        }

        .typing-caret::after {
            content: "|";
            margin-left: 4px;
            color: #bbf7d0;
            animation: blinkCaret .85s infinite;
        }

        .input-focus-shadow:focus {
            box-shadow:
                0 0 0 4px rgba(16, 185, 129, .12),
                0 10px 28px rgba(16, 185, 129, .10);
        }

        @media (max-width: 640px) {
            .login-glass {
                backdrop-filter: blur(12px);
                box-shadow:
                    0 20px 50px rgba(15, 118, 110, .12),
                    inset 0 1px 0 rgba(255, 255, 255, .9);
            }

            .floating-orb {
                animation-duration: 9s;
            }

            .button-shine::before {
                animation-duration: 4.2s;
            }
        }

        @media (max-width: 380px) {
            .login-glass {
                border-radius: 1.25rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body class="min-h-[100svh] overflow-x-hidden bg-emerald-50 selection:bg-emerald-200 selection:text-emerald-950">

    <main class="relative min-h-[100svh] overflow-hidden">

        <!-- Background -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,#bbf7d0_0,transparent_32%),radial-gradient(circle_at_bottom_right,#99f6e4_0,transparent_34%)]"></div>

        <div class="floating-orb absolute -left-24 -top-24 h-48 w-48 rounded-full bg-emerald-300/35 blur-3xl sm:h-64 sm:w-64"></div>
        <div class="floating-orb floating-orb-2 absolute -right-24 top-1/3 h-56 w-56 rounded-full bg-teal-300/35 blur-3xl sm:h-72 sm:w-72"></div>
        <div class="floating-orb floating-orb-3 absolute -bottom-24 left-1/3 h-52 w-52 rounded-full bg-lime-300/25 blur-3xl sm:h-64 sm:w-64"></div>

        <div class="relative z-10 grid min-h-[100svh] lg:grid-cols-2">

            <!-- Left Side -->
            <section class="flex min-h-[100svh] items-center justify-center px-4 py-6 sm:px-6 sm:py-8 md:px-8 lg:px-12">

                <div class="w-full max-w-sm sm:max-w-md lg:max-w-sm xl:max-w-md">

                    <!-- Mobile Logo -->
                    <div class="mb-5 flex justify-center lg:hidden fade-up">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-xl shadow-emerald-500/15 ring-1 ring-emerald-100 sm:h-20 sm:w-20">
                            <img
                                src="{{ asset('image/smkn1cerme.png') }}"
                                alt="Logo SMKN 1 Cerme"
                                class="h-11 w-auto sm:h-14"
                            >
                        </div>
                    </div>

                    <!-- Login Card -->
                    <div class="login-glass fade-up rounded-[1.4rem] border border-white/70 p-5 sm:rounded-[1.7rem] sm:p-7 md:p-8">

                        <div class="mb-5 text-center sm:mb-6 sm:text-left">
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                                Masuk Akun
                            </h1>

                            <p class="mt-2 text-xs leading-relaxed text-slate-500 sm:text-sm">
                                Silakan masuk menggunakan nomor identitas dan kata sandi.
                            </p>
                        </div>

                        <form class="space-y-4" action="{{ route('login') }}" method="POST">
                            @csrf

                            <!-- Nomor Identitas -->
                            <div class="fade-up delay-100">
                                <label for="nomor_identitas" class="mb-2 block text-sm font-bold text-slate-700">
                                    Nomor Identitas <span class="text-red-500">*</span>
                                </label>

                                <div class="group relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition duration-300 group-focus-within:text-emerald-500">
                                        <i class="fa-solid fa-id-card"></i>
                                    </span>

                                    <input
                                        id="nomor_identitas"
                                        name="nomor_identitas"
                                        type="text"
                                        class="input-focus-shadow block w-full rounded-2xl border border-emerald-100 bg-white/85 py-3 pl-11 pr-4 text-sm text-slate-800 shadow-sm outline-none transition duration-300 placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white sm:py-3.5"
                                        placeholder="Masukkan nomor identitas"
                                        value="{{ old('nomor_identitas') }}"
                                    >
                                </div>

                                @error('nomor_identitas')
                                    <p class="mt-2 text-sm font-medium text-red-600">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="fade-up delay-200">
                                <label for="password" class="mb-2 block text-sm font-bold text-slate-700">
                                    Kata Sandi <span class="text-red-500">*</span>
                                </label>

                                <div class="group relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition duration-300 group-focus-within:text-emerald-500">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>

                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="input-focus-shadow block w-full rounded-2xl border border-emerald-100 bg-white/85 py-3 pl-11 pr-12 text-sm text-slate-800 shadow-sm outline-none transition duration-300 placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white sm:py-3.5"
                                        placeholder="Masukkan kata sandi"
                                    >

                                    <button
                                        type="button"
                                        onclick="togglePassword()"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition duration-300 hover:text-emerald-600"
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                    >
                                        <i id="eyeIcon" class="fas fa-eye"></i>
                                    </button>
                                </div>

                                @error('password')
                                    <p class="mt-2 text-sm font-medium text-red-600">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Submit -->
                            <div class="fade-up delay-300 pt-2">
                                <button
                                    type="submit"
                                    class="button-shine relative flex w-full items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 via-green-500 to-teal-600 px-5 py-3 text-xs font-black uppercase tracking-wide text-white shadow-xl shadow-emerald-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-emerald-500/35 focus:outline-none focus:ring-4 focus:ring-emerald-200 active:translate-y-0 sm:py-3.5 sm:text-sm"
                                >
                                    <span class="relative z-10 flex items-center gap-2">
                                        Masuk Sekarang
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                    </span>
                                </button>
                            </div>

                        </form>
                    </div>

                    <p class="mt-5 text-center text-xs text-slate-400 fade-up delay-400">
                        © {{ date('Y') }} Siswa • SMKN 1 Cerme
                    </p>

                </div>
            </section>

            <!-- Right Side Desktop -->
            <section class="relative hidden min-h-[100svh] overflow-hidden bg-gradient-to-br from-emerald-600 via-green-600 to-teal-800 p-6 lg:flex lg:items-center lg:justify-center xl:p-8">

                <!-- Pattern -->
                <div
                    class="absolute inset-0 opacity-20"
                    style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.8) 1px, transparent 0); background-size: 28px 28px;"
                ></div>

                <div class="floating-orb absolute -left-24 top-24 h-64 w-64 rounded-full bg-lime-300/20 blur-3xl"></div>
                <div class="floating-orb floating-orb-2 absolute bottom-10 right-10 h-80 w-80 rounded-full bg-cyan-200/20 blur-3xl"></div>

                <div class="relative z-10 w-full max-w-sm text-white xl:max-w-md">

                    <div class="hero-glass fade-up rounded-[1.8rem] border border-white/20 p-6 shadow-2xl xl:p-7">

                        <h2 class="text-center text-2xl font-black leading-tight tracking-tight xl:text-4xl">
                            Selamat datang di
                            <span id="typingText" class="typing-caret block text-emerald-100"></span>
                        </h2>

                        <!-- Logo Card -->
                        <div class="mt-6 rounded-[1.7rem] border border-white/20 bg-white/15 p-5 shadow-2xl backdrop-blur-md transition duration-300 hover:-translate-y-1 hover:bg-white/20 xl:p-6">

                            <div class="logo-ring relative mx-auto flex h-36 w-36 items-center justify-center rounded-full bg-white/95 shadow-2xl shadow-emerald-950/25 xl:h-44 xl:w-44">
                                <img
                                    src="{{ asset('image/smkn1cerme.png') }}"
                                    alt="Logo SMKN 1 Cerme"
                                    class="relative z-10 h-24 w-auto drop-shadow-2xl transition duration-500 hover:scale-105 xl:h-32"
                                >
                            </div>

                            <div class="mt-6 text-center">
                                <p class="text-lg font-black tracking-wide text-white xl:text-xl">
                                    SMKN 1 CERME
                                </p>

                                <p class="mt-2 text-xs font-semibold text-emerald-50/90">
                                    Platform Sistem Informasi Perpustakaan
                                </p>
                            </div>

                        </div>

                    </div>

                </div>
            </section>

        </div>

    </main>

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

        const typingTarget = document.getElementById('typingText');
        const words = ['Perpustakaan Digital', 'SMKN 1 Cerme'];

        let wordIndex = 0;
        let charIndex = 0;
        let isDeleting = false;

        function typeLoop() {
            if (!typingTarget) return;

            const currentWord = words[wordIndex];
            typingTarget.textContent = currentWord.substring(0, charIndex);

            if (!isDeleting && charIndex < currentWord.length) {
                charIndex++;
                setTimeout(typeLoop, 95);
                return;
            }

            if (!isDeleting && charIndex === currentWord.length) {
                isDeleting = true;
                setTimeout(typeLoop, 1200);
                return;
            }

            if (isDeleting && charIndex > 0) {
                charIndex--;
                setTimeout(typeLoop, 45);
                return;
            }

            isDeleting = false;
            wordIndex = (wordIndex + 1) % words.length;
            setTimeout(typeLoop, 350);
        }

        typeLoop();
    </script>

</body>
</html>