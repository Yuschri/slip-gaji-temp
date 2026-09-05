<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Import PDF BPJSTK</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        h2 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #1a252f;
        }
        .meta-info {
            font-size: 10px;
            margin-bottom: 12px;
            color: #555;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .summary-box span {
            margin-right: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        tr.status-gagal {
            background-color: #fef2f2;
        }
        tr.status-berhasil {
            background-color: #f0fdf4;
        }
        .badge-gagal {
            color: #dc2626;
            font-weight: bold;
        }
        .badge-berhasil {
            color: #16a34a;
            font-weight: bold;
        }
        .keterangan-gagal {
            color: #b91c1c;
            font-size: 8.5px;
            font-weight: 500;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>Laporan Hasil Import PDF BPJSTK</h2>
    <div class="meta-info">
        Periode Laporan: <strong>Bulan {{ $bulan }} / Tahun {{ $tahun }}</strong> &bull;
        Waktu Import: {{ date('d-m-Y H:i:s') }}
    </div>

    <div class="summary-box">
        <span><strong>Total Data:</strong> {{ count($rows) }}</span>
        <span><strong style="color: #16a34a;">Berhasil:</strong> {{ $successCount }}</span>
        <span><strong style="color: #dc2626;">Gagal:</strong> {{ $failedCount }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 70px;">No. Pegawai</th>
                <th>Nama Tenaga Kerja</th>
                <th style="width: 80px;">No. Referensi</th>
                <th style="width: 70px;" class="text-right">Upah (Rp)</th>
                <th style="width: 60px;" class="text-right">JHT (TK)</th>
                <th style="width: 65px;" class="text-right">Total Iuran</th>
                <th style="width: 55px;">Status</th>
                <th style="width: 170px;">KETERANGAN GAGAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $index => $row)
                <tr class="{{ ($row['status'] ?? '') === 'GAGAL' ? 'status-gagal' : 'status-berhasil' }}">
                    <td style="text-align: center;">{{ $row['no'] ?: ($index + 1) }}</td>
                    <td>{{ $row['no_pegawai'] ?: '-' }}</td>
                    <td><strong>{{ $row['nama'] }}</strong></td>
                    <td>{{ $row['no_ref'] ?: '-' }}</td>
                    <td class="text-right">{{ number_format($row['upah'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['jht_tk'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['total_iuran'], 0, ',', '.') }}</td>
                    <td>
                        @if(($row['status'] ?? '') === 'GAGAL')
                            <span class="badge-gagal">GAGAL</span>
                        @else
                            <span class="badge-berhasil">BERHASIL</span>
                        @endif
                    </td>
                    <td class="keterangan-gagal">
                        {{ $row['keterangan'] ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
