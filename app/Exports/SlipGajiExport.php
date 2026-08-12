<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SlipGajiExport implements FromArray, WithTitle, WithStyles, ShouldAutoSize, WithColumnWidths
{
    protected array $rows;
    protected string $filterLabel;
    protected array $headers = [
        'NO',
        'NIP',
        'NO. HP',
        'NAMA KARYAWAN',
        'TANGGAL MASUK',
        'DIVISI',
        'NOMOR REKENING',
        'THP',
        'GAJI POKOK',
        'T. JABATAN',
        'T. PROFESI',
        'T. KEHADIRAN',
        'T. KINERJA',
        'PROSENTASE GAJI',
        'JML HARI GABUNG',
        'NOMINAL LEMBUR',
        'FEE BEAUTICIAN',
        'PUNISHMENT',
        'ITEM PENGURANG GAJI (BPJS TK. KARY.)',
        'ITEM PENGURANG GAJI (BPJS KES. KARY.)',
        'PPH21',
        'SEDEKAH ROMBONGAN',
        'POT. LAINNYA',
        'NOMINAL YANG DITRANSFER',
        // KEHADIRAN
        'LEMBUR (KALI)',
        'LEMBUR (MENIT)',
        'TERLAMBAT (KALI)',
        'TERLAMBAT (MENIT)',
        'IJIN PULANG AWAL',
        'IJIN TIDAK MASUK',
        'NO CHECK IN/CHECK OUT',
        'NO CHECK IN & CHECK OUT',
        'CUTI (HARI)',
        'KEHADIRAN LAINNYA',
    ];

    public function __construct(array $rows, string $filterLabel = 'Semua data')
    {
        $this->rows = $rows;
        $this->filterLabel = $filterLabel;
    }

    public function array(): array
    {
        $dataRows = [$this->headers];

        foreach ($this->rows as $row) {
            $dataRows[] = array_values($row);
        }

        return $dataRows;
    }

    public function title(): string
    {
        return 'Sheet1';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 16,
            'C' => 16,
            'D' => 28,
            'E' => 14,
            'F' => 18,
            'G' => 20,
            'H' => 14,
            'I' => 14,
            'J' => 14,
            'K' => 14,
            'L' => 14,
            'M' => 14,
            'N' => 16,
            'O' => 14,
            'P' => 14,
            'Q' => 16,
            'R' => 14,
            'S' => 24,
            'T' => 24,
            'U' => 12,
            'V' => 16,
            'W' => 14,
            'X' => 20,
            'Y' => 14,
            'Z' => 16,
            'AA' => 16,
            'AB' => 18,
            'AC' => 16,
            'AD' => 16,
            'AE' => 20,
            'AF' => 22,
            'AG' => 12,
            'AH' => 18,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $lastRow = count($this->rows) + 1;

        $sheet->setAutoFilter('A1:AH1');
        $sheet->freezePane('A2');

        $sheet->getStyle('A1:AH1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0B5ED7'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(36);

        if ($lastRow >= 2) {
            $sheet->getStyle('E2:E' . $lastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);
            $sheet->getStyle('H2:M' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('N2:N' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('O2:O' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('P2:X' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('Y2:AH' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->getStyle('A2:AH' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('E2:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('H2:X' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('Y2:AH' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':AH' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFF8FAFC'],
                        ],
                    ]);
                }
            }
        }
    }
}
