<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Anggota - {{ $user->nomor_identitas }} - {{ $user->name }}</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #ffffff;
            color: #111827;

        }

        .page-wrap {
            width: 100%;
            min-height: 100%;
        }

        .card {
            width: 100%;
            max-width: 180mm;
            margin: 0 auto;
            border-radius: 14px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(90, 90, 90, 0.299);
        }

        .card-header {
            background: #2eaecf;
            color: #ffffff;
            padding: 12px 14px 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-logo {
            width: 24mm;
            text-align: center;
        }

        .header-logo img {
            width: 18mm;
            height: auto;
            object-fit: contain;
            display: inline-block;
        }

        .header-center {
            text-align: center;
        }

        .title {
            margin: 0;
            font-size: 17pt;
            line-height: 1.08;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .subtitle {
            margin: 2px 0 0;
            font-size: 13.5pt;
            line-height: 1.05;
            font-weight: bold;
            text-transform: uppercase;
        }

        .address {
            margin: 5px 0 0;
            font-size: 8pt;
            line-height: 1.3;
        }

        .card-body {
            position: relative;
            min-height: 72mm;
            padding: 18px 18px 20px;
            background: #ffffff;
        }

        .body-watermark {
            position: absolute;
            right: 10mm;
            top: 10mm;
            width: 56mm;
            opacity: 0.2;
            z-index: 1;
        }

        .body-watermark img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .body-table {
            width: 100%;
            border-collapse: collapse;
        }

        .body-table td {
            vertical-align: top;
        }

        .identity-box {
            width: 64%;
            padding-top: 10px;
            position: relative;
            z-index: 2;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
        }

        .identity-table td {
            font-size: 14.5pt;
            padding: 5px 0;
            color: #111827;
        }

        .identity-label {
            width: 44mm;
            white-space: nowrap;
        }

        .identity-sep {
            width: 6mm;
            text-align: center;
        }

        .identity-value {
            font-family: 'Times New Roman', serif;
            font-size: 15.5pt;
            font-weight: normal;
        }

        .footer-line {
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 12px;
            height: 1px;
            background: rgba(17, 24, 39, 0.08);
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <div class="card">
            <div class="card-header">
                <table class="header-table">
                    <tr>
                        <td class="header-logo">
                            <img src="{{ public_path('image/smkn1cerme.png') }}" alt="Logo SMKN 1 CERME">
                        </td>
                        <td class="header-center">
                            <div class="title">Kartu Anggota Perpustakaan</div>
                            <div class="subtitle">SMK NEGERI 1 CERME</div>
                            <div class="address">JL. Jurit, Cerme Kidul,Kec.Cerme,Kabupaten Gresik, Jawa Timur</div>
                        </td>
                        <td class="header-logo">
                            <img src="{{ public_path('image/logoLanteraWhite.png') }}" alt="Logo Lantera">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card-body">
                <div class="body-watermark">
                    <img src="{{ public_path('image/smkn1cerme.png') }}" alt="Watermark SMKN 1 CERME">
                </div>

                <table class="body-table">
                    <tr>
                        <td class="identity-box">
                            <table class="identity-table">
                                <tr>
                                    <td class="identity-label">Nama</td>
                                    <td class="identity-sep">:</td>
                                    <td class="identity-value">{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="identity-label">No Anggota</td>
                                    <td class="identity-sep">:</td>
                                    <td class="identity-value">{{ $user->nomor_identitas }}</td>
                                </tr>
                                <tr>
                                    <td class="identity-label">Tanggal Registrasi</td>
                                    <td class="identity-sep">:</td>
                                    <td class="identity-value">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <div class="footer-line"></div>
            </div>
        </div>
    </div>
</body>
</html>
