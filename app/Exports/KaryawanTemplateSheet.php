<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class KaryawanTemplateSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Template Import Karyawan';
    }

    public function array(): array
    {
        return [
            // Row 1: Title
            ['TEMPLATE IMPORT DATA KARYAWAN', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 2: empty
            [],
            // Row 3: Section headers grouping row
            [
                '--- DATA KARYAWAN ---',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '--- GAJI POKOK, TUNJANGAN & POTONGAN ---',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '--- BPJS KETENAGAKERJAAN ---',
                '',
                '--- BPJS KESEHATAN ---',
                '',
                '',
                '',
                '--- PPH 21 ---',
                '',
            ],
            // Row 4: Column headers
            [
                'NIP *',
                'NIK',
                'Tanggal Lahir (YYYY-MM-DD)',
                'Tanggal Masuk (YYYY-MM-DD)',
                'Nama Karyawan *',
                'Cabang *',
                'Divisi *',
                'Jabatan *',
                'Nomor Rekening',
                'Nomor WhatsApp',
                'Periode Cut Off (15/21) *',
                // Gaji & Tunjangan
                'Gaji Pokok *',
                'T. Pengalaman Kerja',
                'T. Jabatan',
                'T. Profesi',
                'T. Kehadiran',
                'T. Kinerja',
                'T. Operasional',
                'Sedekah Rombongan',
                // BPJS TK
                'No. Referensi TK',
                'Upah yang Didaftarkan TK *',
                // BPJS Kesehatan
                'No. JKN Peserta',
                'Beban BPJS Kesehatan (jumlah tanggungan)',
                'NPP',
                'Upah yang Didaftarkan KS *',
                // PPh 21
                'Identitas (NPWP/KTP)',
                'PTKP (TK/0, TK/1, TK/2, TK/3, K/0, K/1, K/2, K/3)',
            ],
            // Row 5: Contoh data
            [
                'KRY001',
                '3201234567890001',
                '1995-05-10',
                '2022-01-01',
                'Budi Santoso',
                'Pusat',
                'Marketing',
                'Staff',
                '1234567890',
                '08123456789',
                '21',
                // Gaji
                '5000000',
                '500000',
                '300000',
                '200000',
                '400000',
                '300000',
                '200000',
                '50000',
                // BPJS TK
                'REF-001',
                '5000000',
                // BPJS KS
                'JKN-001',
                '2',
                'NPP-001',
                '5000000',
                // PPh 21
                '123456789012345',
                'TK/0',
            ],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $lastCol = 'AB'; // Column AB = 28 columns

        // Row 1: Big title
        $sheet->mergeCells('A1:AB1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(35);

        // Row 2
        // Data Karyawan: A2–K2
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2:K2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(35);

        // Gaji & Tunjangan: L2–S2
        $sheet->mergeCells('L2:S2');
        $sheet->getStyle('L2:S2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(35);

        // BPJS TK: T–U (2 cols)
        $sheet->mergeCells('T2:U2');
        $sheet->getStyle('T2:U2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF6A1B9A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(35);

        // BPJS KS: V–Y (4 cols)
        $sheet->mergeCells('V2:Y2');
        $sheet->getStyle('V2:Y2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFB71C1C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(35);

        // PPh 21: Z–AA (2 cols)
        $sheet->mergeCells('Z2:AA2');
        $sheet->getStyle('Z2:AA2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE65100']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(35);

        // Row 3: Section group headers
        // Data Karyawan: A–K (11 cols)
        $sheet->getStyle('A3:K3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Gaji & Tunjangan: L–S (8 cols)
        $sheet->getStyle('L3:S3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // BPJS TK: T–U (2 cols)
        $sheet->getStyle('T3:U3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF6A1B9A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // BPJS KS: V–Y (4 cols)
        $sheet->getStyle('V3:Y3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFB71C1C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // PPh 21: Z–AA (2 cols)
        $sheet->getStyle('Z3:AA3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE65100']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Row 4: Column headers
        $sheet->getStyle('A4:AA4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCE8F5']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF90A4AE']],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(40);

        // Row 5: Example data styling
        $sheet->getStyle('A5:AA5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF9C4']],
            'font' => ['italic' => true, 'color' => ['argb' => 'FF555555']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDBDBD']],
            ],
        ]);

        // Highlight required fields in row 4
        $requiredCols = ['A', 'E', 'F', 'G', 'H', 'K', 'L', 'U', 'Y'];
        foreach ($requiredCols as $col) {
            $sheet->getStyle("{$col}4")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFBBDEFB']],
                'font' => ['bold' => true, 'color' => ['argb' => 'FF1565C0']],
            ]);
        }

        // Add a note row below example
        $sheet->setCellValue('A6', '* Kolom bertanda bintang (*) WAJIB diisi. Baris no 4 adalah contoh data, tidak perlu dihapus. Isi data di bawah baris ini.');
        $sheet->mergeCells('A6:AA6');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFC62828'], 'italic' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF3E0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // NIP
            'B' => 20,  // NIK
            'C' => 26,  // Tanggal Lahir
            'D' => 26,  // Tanggal Masuk
            'E' => 25,  // Nama Karyawan
            'F' => 12,  // Cabang
            'G' => 18,  // Divisi
            'H' => 18,  // Jabatan
            'I' => 18,  // Nomor Rekening
            'J' => 18,  // No WA
            'K' => 20,  // Periode Cut Off
            'L' => 16,  // Gaji Pokok
            'M' => 20,  // T. Pengalaman
            'N' => 14,  // T. Jabatan
            'O' => 14,  // T. Profesi
            'P' => 14,  // T. Kehadiran
            'Q' => 14,  // T. Kinerja
            'R' => 16,  // T. Operasional
            'S' => 18,  // Sedekah
            'T' => 22,  // No. Referensi TK
            'U' => 26,  // Upah TK
            'V' => 20,  // No JKN
            'W' => 30,  // Beban BPJS KS
            'X' => 14,  // NPP
            'Y' => 26,  // Upah KS
            'Z' => 22,  // Identitas
            'AA' => 32, // PTKP
        ];
    }
}
