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
            border-bottom: 3px solid #059669;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }

        .header td {
            vertical-align: middle;
        }

        .brand-cell {
            padding-top: 29px;
        }

        .brand-text {
            padding-left: 7px;
        }

        .brand-text h1 {
            font-size: 44px;
            line-height: 1;
            margin: 0;
            color: #059669;
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
            color: #059669;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }

        .intro {
            width: 100%;
            margin: 6px 0 24px;
            border-left: 8px solid #10b981;
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
            overflow: hidden;
        }

        .main-table th {
            background-color: #059669;
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

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="brand-text brand-cell">
                <h1>Lantera</h1>
                <p class="brand-subtitle">SMK NEGERI 1 CERME</p>
            </td>
            <td class="invoice-label">
                <div class="title">NOTA</div>
            </td>
        </tr>
    </table>

    <div class="intro">
        Nota ini diterbitkan sebagai bukti resmi pelunasan denda perpustakaan. Mohon simpan nota ini sebagai arsip pembayaran.
    </div>

    <table class="meta">
        <tr>
            <td>
                <table class="box">
                    <tr>
                        <td>
                            <div class="label">Diterbitkan untuk</div>
                            <div class="value-strong">{{ $user->name }}</div>
                            <table class="info-table">
                                <tr>
                                    <td class="key">Nomor Identitas</td>
                                    <td>: {{ $user->nomor_identitas }}</td>
                                </tr>
                                <tr>
                                    <td class="key">Nomor Nota</td>
                                    <td>: INV-{{ $invoiceNumber }}</td>
                                </tr>
                                <tr>
                                    <td class="key">Tanggal Terbit</td>
                                    <td>: {{ \Carbon\Carbon::parse($return->tanggal_pengembalian)->translatedFormat('d F Y') }}</td>
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
                <th>Judul Buku</th>
                <th>Kode Buku</th>
                <th>Jenis Tagihan</th>
                <th width="200" class="text-right">Total Tagihan</th>
                <th width="120">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $return->loan->bookItem->book->judul }}</td>
                <td>{{ $return->loan->bookItem->kode_buku }}</td>
                <td>{{ $item['label'] }}</td>
                <td class="text-right">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                <td><span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">LUNAS</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Terima kasih telah menyelesaikan kewajiban Anda. Kartu anggota tetap aktif.</p>
        <p>© {{ date('Y') }} Lantera Digital Library - SMK Negeri 1 Cerme</p>
    </div>
</body>
</html>