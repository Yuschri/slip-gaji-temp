<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\SkemaPph21;
use App\Models\SlipGaji;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LemburImportService
{
    /**
     * Check if any employee in the Excel file already has a SlipGaji for the given month and year.
     *
     * @param \Illuminate\Http\UploadedFile|string $file
     * @param int|string $bulan
     * @param int|string $tahun
     * @return array
     */
    public function checkDuplicates($file, $bulan, $tahun): array
    {
        $filePath = is_string($file) ? $file : $file->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $namesInFile = [];

        for ($r = 1; $r <= $highestRow; $r++) {
            $cVal = $sheet->getCell('C' . $r)->getCalculatedValue();
            $name = trim((string) ($cVal ?? ''));

            if ($this->isValidEmployeeName($name)) {
                $namesInFile[$name] = true;
            }
        }

        $uniqueNames = array_keys($namesInFile);

        if (empty($uniqueNames)) {
            return [
                'has_duplicates' => false,
                'duplicates' => [],
                'count' => 0,
            ];
        }

        // Find matching Karyawans and check existing slips
        $existingSlips = SlipGaji::where('bulan', (string) $bulan)
            ->where('tahun', (string) $tahun)
            ->whereHas('karyawan', function ($query) use ($uniqueNames) {
                $query->where(function ($q) use ($uniqueNames) {
                    foreach ($uniqueNames as $name) {
                        $q->orWhereRaw('LOWER(TRIM(nama_karyawan)) = ?', [mb_strtolower(trim($name))]);
                    }
                });
            })
            ->get();

        $duplicates = [];
        foreach ($existingSlips as $slip) {
            $duplicates[] = $slip->nama_karyawan ?: ($slip->karyawan->nama_karyawan ?? 'Unknown');
        }

        $duplicates = array_values(array_unique($duplicates));

        return [
            'has_duplicates' => !empty($duplicates),
            'duplicates' => $duplicates,
            'count' => count($duplicates),
        ];
    }

    /**
     * Process the import of Lembur Excel data.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int|string $bulan
     * @param int|string $tahun
     * @param bool $overwrite
     * @return array
     */
    public function processImport($file, $bulan, $tahun, bool $overwrite = false): array
    {
        $filePath = $file->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $successCount = 0;
        $failedRows = []; // [row_index => error_message]
        $errors = [];

        // Find header row (usually row 3 where C = 'NAMA')
        $headerRow = 3;
        for ($r = 1; $r <= min(10, $highestRow); $r++) {
            $val = strtoupper(trim((string) $sheet->getCell('C' . $r)->getCalculatedValue()));
            if ($val === 'NAMA' || $val === 'NAMA KARYAWAN') {
                $headerRow = $r;
                break;
            }
        }

        // Identify all columns that represent daily "Total Jam"
        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

        $totalJamCols = [];
        for ($c = 1; $c <= $highestColIndex; $c++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $header3 = trim((string) $sheet->getCell($colLetter . $headerRow)->getCalculatedValue());
            if (strcasecmp($header3, 'Total Jam') === 0) {
                $totalJamCols[] = $colLetter;
            }
        }

        for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
            $nameVal = $sheet->getCell('C' . $r)->getCalculatedValue();
            $namaStr = trim((string) ($nameVal ?? ''));

            if (!$this->isValidEmployeeName($namaStr)) {
                continue;
            }

            $jamVal = $sheet->getCell('DS' . $r)->getCalculatedValue();
            $nominalVal = $sheet->getCell('DU' . $r)->getCalculatedValue();

            $jamLembur = $this->toNumber($jamVal);
            $nominalLembur = $this->toNumber($nominalVal);

            // Count how many daily "Total Jam" columns have a value > 0 for this employee
            $lemburCount = 0;
            foreach ($totalJamCols as $colLetter) {
                $dailyJamVal = $sheet->getCell($colLetter . $r)->getCalculatedValue();
                if ($dailyJamVal !== null && $dailyJamVal !== '' && is_numeric($dailyJamVal) && (float) $dailyJamVal > 0) {
                    $lemburCount++;
                }
            }

            $lemburKali = !empty($totalJamCols) ? $lemburCount : ($jamLembur > 0 ? 1 : 0);

            // Search employee in DB
            $karyawan = Karyawan::with(['gaji', 'potongan', 'bpjstk', 'bpjsk', 'pph21'])
                ->whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [mb_strtolower($namaStr)])
                ->first();

            if (!$karyawan) {
                $errorMsg = "Karyawan dengan nama '{$namaStr}' tidak ditemukan di master data Karyawan.";
                $failedRows[$r] = $errorMsg;
                $errors[] = "Baris {$r}: {$errorMsg}";
                continue;
            }

            // Check duplicate slip if overwrite is false
            $existingSlip = SlipGaji::where('id_karyawan', $karyawan->id_karyawan)
                ->where('bulan', (string) $bulan)
                ->where('tahun', (string) $tahun)
                ->first();

            if ($existingSlip && !$overwrite) {
                $errorMsg = "Slip gaji untuk '{$karyawan->nama_karyawan}' pada bulan {$bulan} tahun {$tahun} sudah ada.";
                $failedRows[$r] = $errorMsg;
                $errors[] = "Baris {$r}: {$errorMsg}";
                continue;
            }

            try {
                DB::transaction(function () use ($karyawan, $jamLembur, $lemburKali, $nominalLembur, $bulan, $tahun, $existingSlip) {
                    $lemburMenit = (int) round($jamLembur * 60);

                    $gajiPokok = (float) ($karyawan->gaji->gaji_pokok ?? 0);
                    $tPengalamanKerja = (float) ($karyawan->gaji->t_pengalaman_kerja ?? 0);
                    $tJabatan = (float) ($karyawan->gaji->t_jabatan ?? 0);
                    $tProfesi = (float) ($karyawan->gaji->t_profesi ?? 0);
                    $tOperasional = (float) ($karyawan->gaji->t_operasional ?? 0);
                    $tKehadiran = (float) ($karyawan->gaji->t_kehadiran ?? 0);
                    $tKinerja = (float) ($karyawan->gaji->t_kinerja ?? 0);

                    $tHariRaya = (float) ($existingSlip->t_hari_raya ?? 0);
                    $feeBeautician = (float) ($existingSlip->fee_beautician ?? 0);
                    $pendapatanLainnya = (float) ($existingSlip->pendapatan_lainnya ?? 0);
                    $penyesuaianGajiLalu = (float) ($existingSlip->penyesuaian_gaji_lalu ?? 0);

                    $punishment = (float) ($existingSlip->punishment ?? 0);
                    $potonganLainnya = (float) ($existingSlip->potongan_lainnya ?? 0);

                    $sedekahRombongan = (float) ($karyawan->potongan->potongan_sedekah_rombongan ?? 0);
                    $bpjstkPerusahaan = (float) ($karyawan->bpjstk->total_iuran ?? 0);
                    $bpjskPerusahaan = (float) ($karyawan->bpjsk->premi ?? 0);
                    $bpjstkKaryawan = (float) ($karyawan->bpjstk->tenaga_kerja ?? 0);
                    $bpjskKaryawan = (float) ($karyawan->bpjsk->tanggungan_karyawan ?? 0);

                    $terlambatKali = (int) ($existingSlip->terlambat_kali ?? 0);
                    $terlambatMenit = (int) ($existingSlip->terlambat_menit ?? 0);
                    $ijinPulangCepat = (int) ($existingSlip->ijin_pulang_awal ?? 0);
                    $ijinTidakMasuk = (int) ($existingSlip->ijin_tidak_masuk ?? 0);
                    $noCheckInOrOut = (int) ($existingSlip->no_checkin_or_checkout ?? 0);
                    $noCheckInAndOut = (int) ($existingSlip->no_checkin_and_checkout ?? 0);
                    $cuti = (int) ($existingSlip->cuti ?? 0);
                    $kehadiranLainnya = (int) ($existingSlip->kehadiran_lainnya ?? 0);

                    $prorataBase = $gajiPokok
                        + $tPengalamanKerja
                        + $tJabatan
                        + $tProfesi
                        + $tKehadiran
                        + $tKinerja
                        + $tOperasional;

                    $nonProrata = $tHariRaya
                        + $feeBeautician
                        + $nominalLembur
                        + $pendapatanLainnya;

                    $subtotalPenerimaan = round(
                        $this->hitungGajiPerPeriode(
                            $karyawan->tanggal_masuk,
                            $karyawan->periode_cut_off ?? 21,
                            $prorataBase,
                            $bulan,
                            $tahun
                        ) + $nonProrata
                    );

                    $totalPotonganLangsung = $punishment + $sedekahRombongan + $potonganLainnya;

                    $totalGaji = $subtotalPenerimaan
                        + $bpjstkPerusahaan
                        + $bpjskPerusahaan
                        - $bpjstkKaryawan
                        - $bpjskKaryawan;

                    if ($totalGaji < 0) {
                        $totalGaji = 0;
                    }

                    $kategoriPph21 = $karyawan->pph21->kategori ?? null;
                    $pph21 = $this->calculatePph21($kategoriPph21, $totalGaji);

                    $nominalTransfer = $totalGaji - $totalPotonganLangsung - $pph21 - $bpjstkPerusahaan - $bpjskPerusahaan;
                    $totalDiterima = max(0, $nominalTransfer);

                    // Update or create Kehadiran
                    Kehadiran::updateOrCreate(
                        [
                            'id_karyawan' => $karyawan->id_karyawan,
                            'bulan' => (int) $bulan,
                            'tahun' => (int) $tahun,
                        ],
                        [
                            'lembur' => $lemburKali,
                            'lembur_menit' => $lemburMenit,
                        ]
                    );

                    // Update or create SlipGaji
                    SlipGaji::updateOrCreate(
                        [
                            'id_karyawan' => $karyawan->id_karyawan,
                            'bulan' => (string) $bulan,
                            'tahun' => (string) $tahun,
                        ],
                        [
                            'gaji_pokok' => $gajiPokok,
                            't_pengalaman_kerja' => $tPengalamanKerja,
                            't_jabatan' => $tJabatan,
                            't_profesi' => $tProfesi,
                            't_operasional' => $tOperasional,
                            't_kehadiran' => $tKehadiran,
                            't_kinerja' => $tKinerja,
                            't_hari_raya' => $tHariRaya,
                            'fee_beautician' => $feeBeautician,
                            'nominal_lembur' => $nominalLembur,
                            'pendapatan_lainnya' => $pendapatanLainnya,
                            'penyesuaian_gaji_lalu' => $penyesuaianGajiLalu,
                            'subtotal_penerimaan' => $subtotalPenerimaan,
                            'bpjstk_perusahaan' => $bpjstkPerusahaan,
                            'bpjsk_perusahaan' => $bpjskPerusahaan,
                            'punishment' => $punishment,
                            'sedekah_rombongan' => $sedekahRombongan,
                            'potongan_lainnya' => $potonganLainnya,
                            'bpjstk_karyawan' => $bpjstkKaryawan,
                            'bpjsk_karyawan' => $bpjskKaryawan,
                            'pph21' => $pph21,
                            'lembur_kali' => $lemburKali,
                            'lembur_menit' => $lemburMenit,
                            'terlambat_kali' => $terlambatKali,
                            'terlambat_menit' => $terlambatMenit,
                            'ijin_pulang_awal' => $ijinPulangCepat,
                            'ijin_tidak_masuk' => $ijinTidakMasuk,
                            'no_checkin_or_checkout' => $noCheckInOrOut,
                            'no_checkin_and_checkout' => $noCheckInAndOut,
                            'cuti' => $cuti,
                            'kehadiran_lainnya' => $kehadiranLainnya,
                            'total_diterima' => $totalDiterima,
                            'is_resign' => false,
                            'tanggal_resign' => null,
                        ]
                    );

                });

                $successCount++;
            } catch (\Throwable $e) {
                $errorMsg = "Gagal memproses data: " . $e->getMessage();
                $failedRows[$r] = $errorMsg;
                $errors[] = "Baris {$r} ({$namaStr}): " . $e->getMessage();
                Log::error("LemburImport error baris {$r}: " . $e->getMessage());
            }
        }

        $errorSpreadsheetPath = null;

        // If any rows failed, modify the spreadsheet and write failure notes
        if (!empty($failedRows)) {
            $highestColumn = $sheet->getHighestColumn();
            $errColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn) + 1;
            $errColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($errColIndex);

            // Set Header in Row 3 (or $headerRow)
            $sheet->setCellValue($errColLetter . $headerRow, 'KETERANGAN GAGAL');
            $sheet->getStyle($errColLetter . $headerRow)->getFont()->setBold(true);

            foreach ($failedRows as $rIdx => $errMsg) {
                $sheet->setCellValue($errColLetter . $rIdx, $errMsg);
            }

            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $errorSpreadsheetPath = $tempDir . '/Hasil_Import_Lembur_Gagal_' . time() . '.xlsx';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($errorSpreadsheetPath);
        }

        return [
            'success_count' => $successCount,
            'failed_count' => count($failedRows),
            'errors' => $errors,
            'error_file' => $errorSpreadsheetPath,
        ];
    }

    private function isValidEmployeeName(string $name): bool
    {
        if ($name === '') {
            return false;
        }

        $upper = strtoupper($name);

        if (
            $upper === 'NAMA' ||
            $upper === 'NAMA KARYAWAN' ||
            $upper === 'NO' ||
            $upper === 'TOTAL' ||
            str_starts_with($upper, 'REKAP LEMBUR') ||
            str_starts_with($upper, '*')
        ) {
            return false;
        }

        return true;
    }

    private function toNumber(mixed $value): float
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $str = str_replace('.', '', (string) $value);
        $str = str_replace(',', '.', $str);
        $str = preg_replace('/[^0-9.\-]/', '', $str);

        return (float) ($str ?: 0);
    }

    private function calculatePph21(?string $kategori, float $totalGaji): float
    {
        if (empty($kategori) || $totalGaji <= 0) {
            return 0;
        }

        $skema = SkemaPph21::where('golongan', $kategori)
            ->where('batas_uang', '<=', $totalGaji)
            ->orderBy('batas_uang', 'desc')
            ->first();

        $ter = $skema ? (float) $skema->persen : 0;
        return round($totalGaji * $ter);
    }

    private function hitungGajiPerPeriode(?Carbon $tanggalMasuk, int $cutoff, float $thpFull, $bulan, $tahun): float
    {
        if (!$tanggalMasuk || $thpFull <= 0) {
            return $thpFull;
        }

        $cutoff = in_array((int) $cutoff, [15, 21], true) ? (int) $cutoff : 21;

        $tglMasuk = $tanggalMasuk->copy()->startOfDay();
        $akhirTraining = $tglMasuk->copy()->addMonths(3)->subDay();

        $periodeAkhir = Carbon::create((int) $tahun, (int) $bulan, $cutoff)->startOfDay();
        $periodeAwal = $periodeAkhir->copy()->subMonthNoOverflow()->addDay();

        $totalHariPeriode = $periodeAwal->diffInDays($periodeAkhir) + 1;

        if ($tglMasuk->gt($periodeAkhir)) {
            return 0;
        }

        $tglMulaiHitung = $tglMasuk->gt($periodeAwal) ? $tglMasuk->copy() : $periodeAwal->copy();

        if ($akhirTraining->gte($tglMulaiHitung) && $akhirTraining->lt($periodeAkhir)) {
            $hariTraining = $tglMulaiHitung->diffInDays($akhirTraining) + 1;
            $gajiTraining = ($hariTraining / $totalHariPeriode) * 0.8 * $thpFull;

            $tglMulaiLulus = $akhirTraining->copy()->addDay();
            $hariLulus = $tglMulaiLulus->diffInDays($periodeAkhir) + 1;
            $gajiLulus = ($hariLulus / $totalHariPeriode) * $thpFull;

            return $gajiTraining + $gajiLulus;
        }

        $hariKerja = $tglMulaiHitung->diffInDays($periodeAkhir) + 1;
        if ($periodeAkhir->lte($akhirTraining)) {
            return ($hariKerja / $totalHariPeriode) * 0.8 * $thpFull;
        }

        return ($hariKerja / $totalHariPeriode) * $thpFull;
    }
}
