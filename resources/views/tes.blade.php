<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengaduan Sarana Prasarana - SMKN 4 Bojonegoro</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="font-sans bg-gray-50">

<!-- NAVBAR -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo2.png') }}" class="w-36" alt="Logo">
        </div>

        <div class="hidden md:flex gap-8 text-gray-600">
            <a href="#beranda" class="font-semibold text-cyan-600 hover:text-cyan-700 transition">Beranda</a>
            <a href="#fitur" class="hover:text-cyan-600 transition">Fitur</a>
            <a href="#cara" class="hover:text-cyan-600 transition">Cara Lapor</a>
            <a href="#faq" class="hover:text-cyan-600 transition">FAQ</a>
            <a href="#tentang" class="hover:text-cyan-600 transition">Tentang</a>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('login') }}" class="px-4 py-2 bg-cyan-600 text-white font-semibold rounded-lg text-sm hover:bg-cyan-700 transition">Masuk</a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-transparent font-semibold border-2 border-cyan-600 text-cyan-600 rounded-lg text-sm hover:bg-cyan-50 transition">Daftar</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section id="beranda" class="bg-gradient-to-br from-cyan-50 via-blue-50 to-cyan-100 py-16 md:py-28">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 items-center gap-12">
        <!-- TEXT -->
        <div>
            <p class="text-sm text-cyan-600 font-semibold mb-3 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Sistem Pengaduan Terpercaya
            </p>

            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-4">
                Laporkan Kerusakan Sarana & Prasarana dengan Mudah
            </h1>

            <p class="text-lg text-gray-600 mb-8">
                Platform resmi SMK Negeri 4 Bojonegoro untuk melaporkan keluhan kerusakan sarana dan prasarana sekolah. Cepat, Aman, dan Transparan.
            </p>

            <!-- BUTTONS -->
            <div class="flex gap-4 flex-wrap">
                <a href="{{ route('login') }}" class="bg-cyan-600 text-white px-7 py-3 rounded-lg font-semibold shadow-lg hover:bg-cyan-700 transition transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Pengaduan
                </a>
                <a href="#cara" class="border-2 border-gray-300 text-gray-700 px-7 py-3 rounded-lg font-semibold hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-question-circle"></i> Pelajari Selengkapnya
                </a>
            </div>

            <!-- STATS -->
            <div class="flex items-center gap-8 mt-10 flex-wrap">
                <div>
                    <p class="text-3xl font-bold text-cyan-600">{{ $aspiration ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Pengaduan Masuk</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-cyan-600">{{ $user ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Pengguna Aktif</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-cyan-600">{{ $resolved ?? 0 }}%</p>
                    <p class="text-sm text-gray-600">Pengaduan Terselesaikan</p>
                </div>
            </div>
        </div>

        <!-- IMAGE -->
        <div class="relative hidden md:block">
            <div class="relative z-10">
                <img src="{{ asset('images/logosmk.png') }}" class="w-full max-w-sm mx-auto drop-shadow-xl" alt="SMKN 4 Bojonegoro">
            </div>
            <!-- Dekorasi -->
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-cyan-200 rounded-full opacity-50 blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full opacity-30 blur-2xl"></div>
        </div>
    </div>
</section>

<!-- FITUR UTAMA -->
<section id="fitur" class="py-16 md:py-24">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-12">
            <p class="text-sm text-cyan-600 font-semibold mb-2">✨ KEUNGGULAN APLIKASI</p>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Sistem pengaduan yang dirancang untuk kemudahan dan transparansi</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Fitur 1 -->
            <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border-l-4 border-cyan-600">
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-mobile-alt text-cyan-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Mudah Digunakan</h3>
                <p class="text-gray-600">Interface yang user-friendly dan intuitif untuk semua kalangan pengguna</p>
            </div>

            <!-- Fitur 2 -->
            <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border-l-4 border-cyan-600">
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-search text-cyan-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pantau Status Real-time</h3>
                <p class="text-gray-600">Lacak status pengaduan Anda kapan saja dan dapatkan update terkini</p>
            </div>

            <!-- Fitur 3 -->
            <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border-l-4 border-cyan-600">
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-cyan-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Aman & Terpercaya</h3>
                <p class="text-gray-600">Data pengaduan Anda dilindungi dengan enkripsi tingkat tinggi</p>
            </div>

            <!-- Fitur 4 -->
            <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border-l-4 border-cyan-600">
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-image text-cyan-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Lampiran Bukti</h3>
                <p class="text-gray-600">Tambahkan foto atau dokumen sebagai bukti kerusakan yang dilaporkan</p>
            </div>

            <!-- Fitur 5 -->
            <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border-l-4 border-cyan-600">
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-cyan-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Laporan Transparan</h3>
                <p class="text-gray-600">Statistik pengaduan yang akurat dan dapat dipertanggungjawabkan</p>
            </div>

            <!-- Fitur 6 -->
            <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border-l-4 border-cyan-600">
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-bell text-cyan-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Notifikasi Otomatis</h3>
                <p class="text-gray-600">Menerima pemberitahuan ketika pengaduan Anda diproses atau selesai</p>
            </div>
        </div>
    </div>
</section>

<!-- CARA LAPOR -->
<section id="cara" class="bg-cyan-50 py-16 md:py-24">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-12">
            <p class="text-sm text-cyan-600 font-semibold mb-2">📋 PANDUAN SINGKAT</p>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Cara Melaporkan Kerusakan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Ikuti langkah-langkah mudah di bawah ini untuk mengirim laporan Anda</p>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            <!-- Step 1 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-cyan-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4 mx-auto">1</div>
                <h3 class="font-semibold text-gray-900 mb-2">Buat Akun</h3>
                <p class="text-sm text-gray-600">Daftar dengan nomor identitas dan email aktif Anda</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-cyan-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4 mx-auto">2</div>
                <h3 class="font-semibold text-gray-900 mb-2">Isi Form</h3>
                <p class="text-sm text-gray-600">Jelaskan detail kerusakan dan lokasi dengan lengkap</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-cyan-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4 mx-auto">3</div>
                <h3 class="font-semibold text-gray-900 mb-2">Upload Foto</h3>
                <p class="text-sm text-gray-600">Sertakan foto kerusakan sebagai bukti nyata</p>
            </div>

            <!-- Step 4 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-cyan-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4 mx-auto">4</div>
                <h3 class="font-semibold text-gray-900 mb-2">Kirim & Pantau</h3>
                <p class="text-sm text-gray-600">Pantau perkembangan pengaduan Anda secara langsung</p>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('login') }}" class="bg-cyan-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-cyan-700 transition inline-flex items-center gap-2 shadow-lg">
                <i class="fas fa-arrow-right"></i> Mulai Buat Pengaduan
            </a>
        </div>
    </div>
</section>

<!-- STATISTIK -->
<section class="py-16 md:py-24">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-12">
            <p class="text-sm text-cyan-600 font-semibold mb-2">📊 DATA APLIKASI</p>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Statistik Pengaduan</h2>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 text-white p-8 rounded-xl shadow-lg text-center">
                <i class="fas fa-inbox text-4xl mb-3 opacity-80"></i>
                <h3 class="text-4xl font-bold">{{ $aspiration ?? 0 }}</h3>
                <p class="text-cyan-100 mt-2">Total Pengaduan</p>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-8 rounded-xl shadow-lg text-center">
                <i class="fas fa-check-circle text-4xl mb-3 opacity-80"></i>
                <h3 class="text-4xl font-bold">{{ $resolved ?? 0 }}%</h3>
                <p class="text-blue-100 mt-2">Terselesaikan</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-8 rounded-xl shadow-lg text-center">
                <i class="fas fa-users text-4xl mb-3 opacity-80"></i>
                <h3 class="text-4xl font-bold">{{ $user ?? 0 }}</h3>
                <p class="text-purple-100 mt-2">Pengguna Terdaftar</p>
            </div>

            <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white p-8 rounded-xl shadow-lg text-center">
                <i class="fas fa-hourglass-half text-4xl mb-3 opacity-80"></i>
                <h3 class="text-4xl font-bold">{{ $pending ?? 0 }}</h3>
                <p class="text-pink-100 mt-2">Sedang Diproses</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section id="faq" class="bg-gray-100 py-16 md:py-24">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-12">
            <p class="text-sm text-cyan-600 font-semibold mb-2">❓ PERTANYAAN UMUM</p>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">FAQ</h2>
        </div>

        <div class="space-y-4">
            <!-- FAQ 1 -->
            <details class="bg-white p-6 rounded-lg shadow-sm group cursor-pointer">
                <summary class="flex items-center justify-between font-semibold text-gray-900 select-none">
                    Bagaimana cara mendaftar di sistem ini?
                    <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                </summary>
                <p class="text-gray-600 mt-4">Anda dapat mendaftar dengan klik tombol "Daftar" di halaman utama, kemudian isi formulir dengan data diri yang valid. Pastikan email Anda aktif untuk verifikasi akun.</p>
            </details>

            <!-- FAQ 2 -->
            <details class="bg-white p-6 rounded-lg shadow-sm group cursor-pointer">
                <summary class="flex items-center justify-between font-semibold text-gray-900 select-none">
                    Apakah data saya aman di aplikasi ini?
                    <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                </summary>
                <p class="text-gray-600 mt-4">Ya, semua data pengguna dilindungi dengan enkripsi tingkat tinggi dan sesuai dengan standar keamanan data internasional. Kami tidak akan membagikan data Anda kepada pihak ketiga.</p>
            </details>

            <!-- FAQ 3 -->
            <details class="bg-white p-6 rounded-lg shadow-sm group cursor-pointer">
                <summary class="flex items-center justify-between font-semibold text-gray-900 select-none">
                    Berapa lama waktu yang diperlukan untuk memproses pengaduan?
                    <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                </summary>
                <p class="text-gray-600 mt-4">Pengaduan akan diverifikasi dalam 1-2 hari kerja. Setelah itu, tim teknis akan melakukan perbaikan sesuai prioritas dan kondisi kerusakan. Anda akan mendapatkan notifikasi untuk setiap update status.</p>
            </details>

            <!-- FAQ 4 -->
            <details class="bg-white p-6 rounded-lg shadow-sm group cursor-pointer">
                <summary class="flex items-center justify-between font-semibold text-gray-900 select-none">
                    Bisakah saya melacak status pengaduan saya?
                    <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                </summary>
                <p class="text-gray-600 mt-4">Tentu! Setelah login, Anda dapat melihat semua pengaduan Anda beserta status terkini. Setiap perubahan status akan diberitahukan melalui notifikasi otomatis.</p>
            </details>

            <!-- FAQ 5 -->
            <details class="bg-white p-6 rounded-lg shadow-sm group cursor-pointer">
                <summary class="flex items-center justify-between font-semibold text-gray-900 select-none">
                    Apa saja jenis kerusakan yang bisa dilaporkan?
                    <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                </summary>
                <p class="text-gray-600 mt-4">Semua jenis kerusakan sarana dan prasarana sekolah dapat dilaporkan, mulai dari kerusakan bangunan, peralatan pembelajaran, meubelé, hingga fasilitas umum lainnya.</p>
            </details>
        </div>
    </div>
</section>

<!-- TENTANG -->
<section id="tentang" class="py-16 md:py-24">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <p class="text-sm text-cyan-600 font-semibold mb-2">ℹ️ TENTANG KAMI</p>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Visi & Misi</h2>

            <div class="space-y-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-bullseye text-cyan-600"></i> Visi
                    </h3>
                    <p class="text-gray-600">Menjadi sistem pengaduan terpercaya yang meningkatkan kualitas sarana dan prasarana di SMK Negeri 4 Bojonegoro melalui partisipasi aktif seluruh warga sekolah.</p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-target text-cyan-600"></i> Misi
                    </h3>
                    <p class="text-gray-600">Memberikan platform yang mudah digunakan untuk melaporkan kerusakan, meningkatkan transparansi dalam penanganan masalah, dan mempercepat proses perbaikan sarana dan prasarana sekolah.</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-cyan-50 to-blue-50 p-8 rounded-xl">
            <h3 class="font-semibold text-gray-900 mb-6 text-lg">Informasi Kontak</h3>

            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-cyan-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Lokasi</p>
                        <p class="text-gray-600 text-sm">SMK Negeri 4 Bojonegoro, Jawa Timur</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-cyan-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Telepon</p>
                        <p class="text-gray-600 text-sm">(0353) XXXX - XXXX</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-cyan-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Email</p>
                        <p class="text-gray-600 text-sm">pengaduan@smkn4bjn.sch.id</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="bg-gradient-to-r from-cyan-600 to-blue-600 py-16 text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Siap Melaporkan?</h2>
        <p class="text-cyan-100 mb-8 text-lg">Bantu kami menjaga sarana dan prasarana sekolah dengan melaporkan kerusakan segera.</p>
        <div class="flex gap-4 justify-center flex-wrap">
            <a href="{{ route('login') }}" class="bg-white text-cyan-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-arrow-right"></i> Mulai Sekarang
            </a>
            <a href="#faq" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:bg-opacity-10 transition">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="font-semibold text-white mb-4">Tentang</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#tentang" class="hover:text-white transition">Visi & Misi</a></li>
                    <li><a href="#fitur" class="hover:text-white transition">Fitur</a></li>
                    <li><a href="#cara" class="hover:text-white transition">Panduan</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">Bantuan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#faq" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="#" class="hover:text-white transition">Hubungi Kami</a></li>
                    <li><a href="#" class="hover:text-white transition">Laporan Bug</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">Kebijakan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Terms & Conditions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">Ikuti Kami</h4>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-8 text-center text-sm">
            <p>&copy; <span id="year"></span> Sistem Pengaduan Sarana Prasarana SMK Negeri 4 Bojonegoro. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script>
    document.getElementById('year').textContent = new Date().getFullYear();
</script>

</body>
</html>
