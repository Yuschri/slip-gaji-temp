<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SlipGajiTemplateSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Template Import Slip Gaji';
    }

    public function array(): array
    {
        return [
            // Row 1: Title
            ['TEMPLATE IMPORT DATA SLIP GAJI', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 2: Empty row
            [],
            // Row 3: Group headers
            [
                '--- DATA KARYAWAN ---',
                '',
                '--- PENDAPATAN ---',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '--- POTONGAN ---',
                '',
                '--- KEHADIRAN ---',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            // Row 4: Column headers
            [
                'NIP *',
                'Nama Karyawan *',
                'Tunjangan Kehadiran',
                'Tunjangan Kinerja',
                'Tunjangan Hari Raya',
                'Fee Beautician',
                'Nominal Lembur',
                'Pendapatan Lainnya',
                'Penyesuaian Gaji Lalu',
                'Punishment',
                'Potongan Lainnya',
                'Lembur (Kali)',
                'Lembur (Menit)',
                'Terlambat (Kali)',
                'Terlambat (Menit)',
                'Ijin Pulang Cepat',
                'Ijin Tidak Masuk',
                'No Check In/Out',
                'No Check In & Out',
                'Cuti (Hari)',
                'Kehadiran Lainnya',
            ],
            // Row 5: Example data
            [
                'KRY001',
                'Budi Santoso',
                '250000',
                '300000',
                '0',
                '150000',
                '120000',
                '50000',
                '0',
                '25000',
                '10000',
                '2',
                '30',
                '1',
                '10',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
            ],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:U1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(35);

        // Group headers (row 3)
        $sheet->getStyle('A3:B3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('C3:J3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('K3:L3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFB71C1C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('M3:U3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF6A1B9A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Column headers (row 4)
        $sheet->getStyle('A4:U4')->applyFromArray([
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

        // Example row (row 5)
        $sheet->getStyle('A5:U5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF9C4']],
            'font' => ['italic' => true, 'color' => ['argb' => 'FF555555']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDBDBD']],
            ],
        ]);

        // Required columns marker style
        foreach (['A', 'B'] as $col) {
            $sheet->getStyle($col . '4')->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFBBDEFB']],
                'font' => ['bold' => true, 'color' => ['argb' => 'FF1565C0']],
            ]);
        }

        $sheet->setCellValue('A6', '* Kolom bertanda bintang (*) wajib diisi. Baris ke-5 adalah contoh agar mudah diikuti, silakan hapus sebelum import.');
        $sheet->mergeCells('A6:U6');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFC62828'], 'italic' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF3E0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 24,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 16,
            'G' => 16,
            'H' => 18,
            'I' => 20,
            'J' => 14,
            'K' => 18,
            'L' => 14,
            'M' => 14,
            'N' => 16,
            'O' => 16,
            'P' => 16,
            'Q' => 16,
            'R' => 16,
            'S' => 18,
            'T' => 12,
            'U' => 18,
        ];
    }
}
