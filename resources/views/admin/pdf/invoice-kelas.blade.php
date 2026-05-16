<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Denda Kelas</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 13px;
            line-height: 1.5;
        }

        .header {
            border-bottom: 2px solid #10b981;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #064e3b;
        }

        .subtitle {
            margin: 4px 0 0;
            color: #64748b;
        }

        .info {
            width: 100%;
            margin-bottom: 20px;
        }

        .info td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            width: 160px;
            color: #64748b;
        }

        .value {
            font-weight: bold;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        table.items th {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #d1fae5;
            padding: 10px;
            text-align: left;
        }

        table.items td {
            border: 1px solid #e2e8f0;
            padding: 10px;
        }

        .text-right {
            text-align: right;
        }

        .total-box {
            margin-top: 20px;
            text-align: right;
        }

        .total {
            display: inline-block;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            color: #065f46;
        }

        .footer {
            margin-top: 40px;
            color: #64748b;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">Nota Pembayaran Denda Kelas</h1>
        <p class="subtitle">Bukti pembayaran denda peminjaman kelas</p>
    </div>

    <table class="info">
        <tr>
            <td class="label">Nomor Nota</td>
            <td class="value">: {{ $invoiceNumber }}</td>
        </tr>
        <tr>
            <td class="label">Nama Peminjam</td>
            <td class="value">: {{ $user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Identitas</td>
            <td class="value">: {{ $user->nomor_identitas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kategori</td>
            <td class="value">: {{ $pinjamKelas->kategori->nama_kategori ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kode</td>
            <td class="value">: {{ $pinjamKelas->kode_buku ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kondisi</td>
            <td class="value">: {{ ucfirst($pinjamKelas->kondisi ?? '-') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Denda</td>
            <td class="value">
                : {{ !empty($pinjamKelas->tanggal_denda) ? \Carbon\Carbon::parse($pinjamKelas->tanggal_denda)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Tanggal Bayar</td>
            <td class="value">
                : {{ !empty($tanggalBayar) ? \Carbon\Carbon::parse($tanggalBayar)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th>Keterangan</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['label'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-right">
                        Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <div class="total">
            Total: Rp {{ number_format($total, 0, ',', '.') }}
        </div>
    </div>

    <div class="footer">
        Nota ini dicetak otomatis oleh sistem perpustakaan.
    </div>

</body>
</html>