<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman - {{ $loan->bookItem->kode_buku }} - {{ $user->name }}</title>
    <style>
        /* Standar Ukuran A4 untuk dompdf */
        @page {
            size: portrait;
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Helper Styles karena Flex/Grid sering bermasalah di dompdf */
        .w-full { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Header menggunakan table untuk alignment */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #06B6D4;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .brand-logo {
            width: 80px; /* Disesuaikan untuk PDF */
        }

        .school-name h1 {
            font-size: 24pt;
            margin: 0;
            color: #06B6D4;
            text-transform: uppercase;
        }

        .school-name p {
            font-size: 11pt;
            margin: 0;
            font-weight: bold;
            color: #4B5563;
        }

        .main-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            color: #1F2937;
        }

        /* Info Section (Blue Box) */
        .info-section {
            background-color: #F0F9FF;
            padding: 15px;
            border-left: 4px solid #06B6D4;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            font-size: 10pt;
        }

        .info-label {
            width: 140px;
            font-weight: bold;
            color: #374151;
            padding-bottom: 5px;
        }

        /* Table Utama */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th {
            background-color: #0891B2;
            color: #ffffff;
            padding: 10px;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .main-table td {
            padding: 10px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9pt;
            color: #374151;
        }

        /* Rules Section (Yellow Box) */
        .rules-section {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin-bottom: 30px;
        }

        .rules-section h2 {
            font-size: 11pt;
            margin-top: 0;
            color: #92400E;
        }

        .rules-section ol {
            margin: 0;
            padding-left: 20px;
            font-size: 9pt;
            color: #78350F;
        }

        /* Signature Section */
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }

        .sig-box {
            width: 40%;
            text-align: center;
        }

        .sig-label {
            font-size: 10pt;
            margin-bottom: 60px;
            font-weight: bold;
        }

        .sig-line {
            border-bottom: 1.5px solid #000;
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="90">
                <img src="{{ public_path('image/logoClean.png') }}" class="brand-logo">
            </td>
            <td>
                <div class="school-name">
                    <h1>Lantera</h1>
                    <p>Perpustakaan SMKN 1 CERME</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="main-title">Kartu / Bukti Peminjaman Buku</div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">ID Peminjaman</td>
                <td width="10">:</td>
                <td>LTR-{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td class="text-right" style="color: #6B7280; font-size: 8pt;">Tanggal Cetak: {{ date('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Nama</td>
                <td>:</td>
                <td colspan="2" class="font-bold">{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Nomor Identitas</td>
                <td>:</td>
                <td colspan="2">{{ $user->nomor_identitas }}</td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th align="left">Judul Buku</th>
                <th width="80">Kode Buku</th>
                <th width="100">Tanggal Pinjam</th>
                <th width="100">Tanggal Kembali</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>{{ $loan->bookItem->book->judul }}</td>
                <td class="text-center">{{ $loan->bookItem->kode_buku }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d/m/Y') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="rules-section">
        <h2>Peraturan Peminjaman</h2>
        <ol>
            <li>Masa pinjam 7 hari.</li>
            <li>Denda Rp 1.000 / hari keterlambatan.</li>
            <li>Buku yang rusak/hilang wajib diganti.</li>
            <li>Maksimal 3 buku dalam satu waktu.</li>
        </ol>
    </div>

    <table class="signature-table">
        <tr>
            <td class="sig-box">
                <div class="sig-label">Petugas Perpustakaan</div>
                <div class="sig-line"></div>
            </td>
            <td width="20%"></td>
            <td class="sig-box">
                <div class="sig-label">Tanda Tangan</div>
                <div class="sig-line"></div>
            </td>
        </tr>
    </table>

</body>
</html>
