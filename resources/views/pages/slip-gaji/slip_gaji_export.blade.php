<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $slip->karyawan ? $slip->karyawan->nama_karyawan : '' }}</title>
    <style>
        @page {
            margin: 0.8cm 1.2cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #222;
            margin: 0 0 3px 0;
        }

        .header-sub {
            font-size: 10px;
            color: #444;
            margin: 2px 0;
        }

        .kop-line {
            width: 100%;
            height: 4px;
            background-color: #d63384;
            margin-top: 8px;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-label {
            width: 100px;
            font-weight: bold;
        }

        .info-sep {
            width: 15px;
            font-weight: bold;
        }

        .info-val {
            font-weight: bold;
        }

        .main-grid {
            width: 100%;
            border-collapse: collapse;
            vertical-align: top;
        }

        .grid-col {
            width: 48%;
            vertical-align: top;
        }

        .section-header {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            margin-top: 5px;
            text-transform: uppercase;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .item-table td {
            padding: 2.5px 0;
            vertical-align: middle;
        }

        .col-rp {
            width: 35px;
            text-align: left;
        }

        .col-val {
            text-align: right;
        }

        .total-box {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            margin-top: 18px;
            padding: 6px 0;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            font-weight: bold;
        }

        .terbilang-box {
            font-size: 10.5px;
            font-style: italic;
            margin-top: 8px;
            color: #333;
        }

        .signature-box {
            float: right;
            width: 220px;
            text-align: center;
            margin-top: 20px;
            font-size: 10.5px;
        }

        .disclaimer {
            clear: both;
            text-align: center;
            font-size: 8.5px;
            font-style: italic;
            color: #444;
            margin-top: 80px;
            line-height: 1.4;
        }
    </style>
</head>

<body>

    @php
        $klinikName = strtoupper($slip->klinik ?: ($slip->karyawan ? $slip->karyawan->cabang : 'HO'));
        $kopFile = 'ho.png';

        if (str_contains($klinikName, 'KAV') || str_contains($klinikName, 'DPR')) {
            $kopFile = 'kav_dpr.png';
        } elseif (str_contains($klinikName, 'KUTAI')) {
            $kopFile = 'kutai.png';
        } elseif (str_contains($klinikName, 'KUTISARI')) {
            $kopFile = 'kutisari.png';
        } elseif (str_contains($klinikName, 'MADIUN')) {
            $kopFile = 'madiun.png';
        } elseif (str_contains($klinikName, 'MOJOKERTO')) {
            $kopFile = 'mojokerto.png';
        } elseif (str_contains($klinikName, 'TAMAN PARIS')) {
            $kopFile = 'taman_paris.png';
        } elseif (str_contains($klinikName, 'HO') || str_contains($klinikName, 'HEAD OFFICE')) {
            $kopFile = 'ho.png';
        } elseif (str_contains($klinikName, 'MULYOSARI')) {
            $kopFile = 'mulyosari.png';
        }

        $kopPath = public_path('assets/images/kop/' . $kopFile);
        $kopBase64 = file_exists($kopPath) ? base64_encode(file_get_contents($kopPath)) : null;

        $bulanIndo = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOPEMBER',
            12 => 'DESEMBER'
        ];
        $namaBulan = $bulanIndo[(int) $slip->bulan] ?? strtoupper(date('F', mktime(0, 0, 0, (int) $slip->bulan, 10)));
    @endphp

    <!-- HEADER / KOP SURAT -->
    <table class="header-table">
        <tr>
            @if($kopBase64)
                <td style="width: 100%;">
                    <img src="data:image/png;base64,{{ $kopBase64 }}" style="width: 100%; height: auto;">
                </td>
            @else
                <td style="width: 80px; vertical-align: middle;">
                    <div style="font-size: 24px; font-weight: bold; color: #d63384;">DNY</div>
                </td>
                <td style="vertical-align: middle; padding-left: 10px;">
                    <div class="header-title">PT. Doa Niat Yakin {{ $klinikName }}</div>
                    <div class="header-sub">Jl. Mulyosari Raya, No. 310, Kec. Mulyorejo, Kota Surabaya, 60113</div>
                    <div class="header-sub">tlp: (031) 359 54121 / 0851 1368 32311 &nbsp;&nbsp; website: www.dnyskincare.com
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <div class="kop-line"></div>

    <!-- INFORMASI KARYAWAN & PERIODE -->
    <table class="info-table">
        <tr>
            <td class="info-label">NAMA</td>
            <td class="info-sep">:</td>
            <td class="info-val">{{ strtoupper($slip->karyawan ? $slip->karyawan->nama_karyawan : '-') }}</td>
        </tr>
        <tr>
            <td class="info-label">JABATAN</td>
            <td class="info-sep">:</td>
            <td class="info-val">
                {{ strtoupper(($slip->karyawan && $slip->karyawan->jabatan) ? $slip->karyawan->jabatan->nama_jabatan : ($slip->divisi ?: '-')) }}
            </td>
        </tr>
        <tr>
            <td class="info-label">PERIODE</td>
            <td class="info-sep">:</td>
            <td class="info-val">{{ $namaBulan }} {{ $slip->tahun }}</td>
        </tr>
    </table>

    <!-- MAIN GRID 2 KOLOM -->
    <table class="main-grid">
        <tr>
            <!-- KOLOM KIRI: PENERIMAAN -->
            <td class="grid-col" style="padding-right: 15px;">
                <div class="section-header">PENERIMAAN :</div>
                <table class="item-table">
                    <tr>
                        <td>GAJI POKOK</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>T. PENGALAMAN KERJA</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->t_pengalaman_kerja, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>T. JABATAN</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->t_jabatan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>T. PROFESI</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->t_profesi, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>OPERASIONAL</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->t_operasional, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>T. KEHADIRAN</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->t_kehadiran, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>T. KINERJA</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->t_kinerja, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>T. HARI RAYA</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->t_hari_raya, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>NOMINAL LEMBUR</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->nominal_lembur, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>FEE BEAUTICIAN</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->fee_beautician, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>LAIN-LAIN</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->pendapatan_lainnya, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>PENYES. GAJI LALU</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->penyesuaian_gaji_lalu, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>

            <!-- KOLOM KANAN: POTONGAN & KETIDAKHADIRAN -->
            <td class="grid-col" style="padding-left: 15px;">
                <div class="section-header">POTONGAN :</div>
                <table class="item-table">
                    <tr>
                        <td>(PUNISHMENT)</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->punishment, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>(BPJS TK KARY.)</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->bpjstk_karyawan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>(BPJS KES. KARY.)</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->bpjsk_karyawan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>(PPH 21)</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->pph21, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>SEDEKAH ROMBONGAN</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->sedekah_rombongan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>POTONGAN LAINNYA</td>
                        <td class="col-rp">: Rp.</td>
                        <td class="col-val">{{ number_format($slip->potongan_lainnya, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <div class="section-header" style="margin-top: 15px;">KETIDAKHADIRAN :</div>
                <table class="item-table">
                    <tr>
                        <td>TERLAMBAT</td>
                        <td style="width: 15px;">:</td>
                        <td class="col-val">{{ $slip->terlambat_kali ?: '' }}</td>
                    </tr>
                    <tr>
                        <td>IJIN PULANG AWAL</td>
                        <td style="width: 15px;">:</td>
                        <td class="col-val">{{ $slip->ijin_pulang_awal ?: '' }}</td>
                    </tr>
                    <tr>
                        <td>IJIN TDK MASUK</td>
                        <td style="width: 15px;">:</td>
                        <td class="col-val">{{ $slip->ijin_tidak_masuk ?: '' }}</td>
                    </tr>
                    <tr>
                        <td>NO CHECK IN/ CHECK OUT</td>
                        <td style="width: 15px;">:</td>
                        <td class="col-val">{{ $slip->no_checkin_or_checkout ?: '' }}</td>
                    </tr>
                    <tr>
                        <td>NO CHECK IN & CHECK OUT</td>
                        <td style="width: 15px;">:</td>
                        <td class="col-val">{{ $slip->no_checkin_and_checkout ?: '' }}</td>
                    </tr>
                    <tr>
                        <td>LAIN LAIN</td>
                        <td style="width: 15px;">:</td>
                        <td class="col-val">{{ $slip->kehadiran_lainnya ?: '' }}</td>
                    </tr>
                    <tr>
                        <td>CUTI</td>
                        <td style="width: 15px;">:</td>
                        <td class="col-val">{{ $slip->cuti ?: '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TOTAL DITERIMA BOX -->
    <div class="total-box">
        <table class="total-table">
            <tr>
                <td>TOTAL DITERIMA</td>
                <td style="width: 35px; text-align: left;">Rp.</td>
                <td class="col-val" style="width: 120px;">{{ number_format($slip->total_diterima, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- TERBILANG -->
    <div class="terbilang-box">
        <strong>Terbilang :</strong> {{ $terbilang }}
    </div>

    <!-- TANDA TANGAN -->
    <div class="signature-box">
        <div>Sidoarjo, {{ date('d') }} {{ $namaBulan }} {{ $slip->tahun }}</div>
        <div style="margin-top: 5px;">Penerima,</div>
        <div style="margin-top: 55px; font-weight: bold;">(
            {{ strtoupper($slip->karyawan ? $slip->karyawan->nama_karyawan : '') }} )
        </div>
    </div>

    <!-- DISCLAIMER KERAHASIAAN -->
    <div class="disclaimer">
        "Slip Gaji ini bersifat rahasia, dilarang menyebarkan, memfoto dan membagi info gaji ini kepada siapapun.<br>
        Setiap pelanggaran atas kewajiban menjaga kerahasiaan ini akan dikenakan sanksi"
    </div>

</body>

</html>