<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $slip->nama_karyawan }}</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 95%;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px;
            vertical-align: top;
        }

        .label {
            width: 180px;
        }

        .separator {
            width: 10px;
        }

        .value {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            background-color: #f2f2f2;
            border-top: 1px solid #000;
            border-bottom: 1px dotted #000;
            padding: 5px;
            margin-top: 10px;
        }

        .dotted td {
            border-bottom: 1px dotted #ccc;
        }

        .total {
            font-weight: bold;
            border-top: 2px solid #000;
            font-size: 14px;
        }

        .footer {
            margin-top: 20px;
        }

        .note {
            font-size: 9px;
            margin-top: 20px;
            font-style: italic;
            color: #666;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 80px; padding: 0; vertical-align: middle; border: none;">
                        @if(file_exists(public_path('kop.jpg')))
                            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('kop.jpg'))) }}"
                                style="width: 70px; height: auto;">
                        @endif
                    </td>
                    <td style="text-align: left; padding-left: 10px; vertical-align: middle; border: none;">
                        <h2 style="margin: 0; font-size: 16px;">PT. DOA NIAT YAKIN {{ strtoupper($slip->klinik) }}</h2>
                        <div style="font-size: 10px;">Empunala No. 10, {{ $slip->klinik }}</div>
                        <div style="font-size: 10px;">No. Telp (0321) 5219 721</div>
                    </td>
                </tr>
            </table>
        </div>

        <table>
            <tr>
                <td class="label">NAMA</td>
                <td class="separator">:</td>
                <td class="value">{{ strtoupper($slip->nama_karyawan) }}</td>
            </tr>
            <tr>
                <td class="label">DIVISI / KLINIK</td>
                <td class="separator">:</td>
                <td class="value">{{ strtoupper($slip->divisi) }} / {{ strtoupper($slip->klinik) }}</td>
            </tr>
            <tr>
                <td class="label">PERIODE</td>
                <td class="separator">:</td>
                <td class="value">{{ date('F', mktime(0, 0, 0, $slip->bulan, 10)) }} {{ $slip->tahun }}</td>
            </tr>

            <tr>
                <td colspan="3" class="section-title">PENERIMAAN :</td>
            </tr>

            <tr class="dotted">
                <td>GAJI POKOK</td>
                <td></td>
                <td class="right">{{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>T. JABATAN</td>
                <td></td>
                <td class="right">{{ number_format($slip->t_jabatan, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>T. PROFESI</td>
                <td></td>
                <td class="right">{{ number_format($slip->t_profesi, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>T. KEHADIRAN</td>
                <td></td>
                <td class="right">{{ number_format($slip->t_kehadiran, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>T. KINERJA</td>
                <td></td>
                <td class="right">{{ number_format($slip->t_kinerja, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>NOMINAL LEMBUR</td>
                <td></td>
                <td class="right">{{ number_format($slip->lembur, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="3" class="section-title">POTONGAN :</td>
            </tr>

            <tr class="dotted">
                <td>(PUNISHMENT)</td>
                <td></td>
                <td class="right">{{ number_format($slip->punishment, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>(BPJS TK)</td>
                <td></td>
                <td class="right">{{ number_format($slip->bpjstk, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>(BPJS KESEHATAN)</td>
                <td></td>
                <td class="right">{{ number_format($slip->bpjs_kesehatan, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>(PPH 21)</td>
                <td></td>
                <td class="right">{{ number_format($slip->pph_21, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>SEDEKAH ROMBONGAN</td>
                <td></td>
                <td class="right">{{ number_format($slip->sedekah_rombongan, 0, ',', '.') }}</td>
            </tr>
            <tr class="dotted">
                <td>LAIN-LAIN</td>
                <td></td>
                <td class="right">{{ number_format($slip->lain_lain, 0, ',', '.') }}</td>
            </tr>

            <tr class="total">
                <td>TOTAL DITERIMA</td>
                <td class="right">Rp</td>
                <td class="right">{{ number_format($slip->nominal_transfer, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div style="padding:15px; background-color: #eee; margin-top: 10px; font-style: italic;">
            <strong>
                {{ $terbilang }}
            </strong>
        </div>

        <div class="signature">
            Mojokerto, {{ date('d F Y') }}<br><br><br><br>
            ( ____________________ )
        </div>

        <hr>
        <h5 style="margin-bottom: 5px;">Status Kehadiran:</h5>
        <table style="width: 50%; font-size: 10px;">
            <tr>
                <td>CUTI</td>
                <td>: {{ $slip->cuti }}</td>
            </tr>
            <tr>
                <td>TERLAMBAT</td>
                <td>: {{ $slip->terlambat }}</td>
            </tr>
            <tr>
                <td>IJIN PULANG AWAL</td>
                <td>: {{ $slip->ijin_pulang_cepat }}</td>
            </tr>
            <tr>
                <td>IJIN TIDAK MASUK</td>
                <td>: {{ $slip->ijin_tidak_masuk }}</td>
            </tr>
            <tr>
                <td>NO CHECK IN/ CHECK OUT</td>
                <td>: {{ $slip->no_check_in_or_out }}</td>
            </tr>
            <tr>
                <td>NO CHECK IN & CHECK OUT</td>
                <td>: {{ $slip->no_check_in_and_out }}</td>
            </tr>
        </table>

        <div class="note">
            Harap diperhatikan, isi dalam slip gaji ini adalah bersifat rahasia, kecuali anda diminta untuk
            mengungkapkannya untuk keperluan hukum, pajak, dan pemerintah. Setiap pelanggaran atas kewajiban
            menjaga kerahasiaan ini akan dikenakan sanksi.
        </div>
    </div>

</body>

</html>