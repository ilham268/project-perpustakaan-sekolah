<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pembayaran Denda #{{ $invoiceNumber }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px 48px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            width: 100%;
            border-bottom: 3px solid #111827;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }

        .header td {
            vertical-align: middle;
        }

        .brand-cell {
            padding-top: 29px;
        }

        .brand-logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .brand-text {
            padding-left: 7px;
        }

        .brand-text h1 {
            font-size: 44px;
            line-height: 1;
            margin: 0;
            color: #0891b2;

            letter-spacing: 0.5px;
        }



        .brand-subtitle {
            font-size: 18px;
            font-weight: bold;
            margin-top: 4px;
            color: #0f172a;
        }

        .invoice-label {
            text-align: right;
        }

        .invoice-label .title {
            font-size: 64px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }

        .intro {
            width: 100%;
            margin: 6px 0 24px;
            border-left: 8px solid #06b6d4;
            padding: 6px 0 6px 14px;
            font-size: 17px;
            color: #1f2937;
        }

        .meta {
            width: 100%;
            margin-bottom: 22px;
        }

        .box {
            width: 100%;
            background: #ffffff;
        }

        .box td {
            padding: 18px 20px;
            vertical-align: top;
        }

        .label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .value-strong {
            font-size: 30px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .muted {
            color: #64748b;
        }

        .info-table {
            width: 100%;
            margin-top: 6px;
        }

        .info-table td {
            padding: 4px 0;
            font-size: 15px;
        }

        .info-table .key {
            width: 180px;
            color: #64748b;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 0px;
            overflow: hidden;
        }

        .main-table th {
            background-color: #06b6d4;
            color: #ffffff;
            text-align: left;
            padding: 19px 22px;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .main-table td {
            padding: 20px 22px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 18px;
            color: #1f2937;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .main-table tbody tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="78" class="brand-cell">
                <img src="{{ public_path('image/logoLantera.png') }}" class="brand-logo">
            </td>
            <td class="brand-text brand-cell">
                <h1>Lantera</h1>
                <p class="brand-subtitle">SMKN 1 CERME</p>
            </td>
            <td class="invoice-label">
                <div class="title">Nota</div>
            </td>
        </tr>
    </table>

    <div class="intro">
        Nota ini diterbitkan sebagai bukti resmi tagihan denda perpustakaan. Mohon simpan nota ini sebagai arsip pembayaran.
    </div>

    <table class="meta">
        <tr>
            <td>
                <table class="box">
                    <tr>
                        <td>
                            <div class="label">Ditagihkan kepada</div>
                            <div class="value-strong">{{ $user->name }}</div>
                            <table class="info-table">
                                <tr>
                                    <td class="key">Nomor Identitas</td>
                                    <td>: {{ $user->nomor_identitas }}</td>
                                </tr>
                                <tr>
                                    <td class="key">Nomor Nota</td>
                                    <td>: NTP-{{ $invoiceNumber }}</td>
                                </tr>
                                <tr>
                                    <td class="key">Tanggal Terbit</td>
                                    <td>: {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->format('d F Y') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>Buku</th>
                <th>Kode Buku</th>
                <th>Jenis Tagihan</th>
                <th width="180" class="text-right">Total Tagihan</th>
                <th width="150">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $return->loan->bookItem->book->judul }}</td>
                <td>{{ $return->loan->bookItem->kode_buku }}</td>
                <td>{{ $item['label'] }}</td>
                <td class="text-right">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                <td>Lunas</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
