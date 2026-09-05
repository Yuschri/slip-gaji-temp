<?php

namespace App\Services;

use App\Models\Bpjstk;
use App\Models\Karyawan;
use App\Models\SkemaPph21;
use App\Models\SlipGaji;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class BpjstkImportService
{
    /**
     * Parse BPJSTK PDF file into structured employee rows.
     *
     * @param \Illuminate\Http\UploadedFile|string $file
     * @return array
     */
    public function parsePdf($file): array
    {
        $filePath = is_string($file) ? $file : $file->getRealPath();
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);

        $rows = [];

        foreach ($pdf->getPages() as $page) {
            $dataTM = $page->getDataTm();

            // Group text elements by Y coordinate (within 3pt tolerance)
            $rowsByY = [];
            foreach ($dataTM as $item) {
                $x = (float) $item[0][4];
                $y = (float) $item[0][5];
                $text = trim($item[1]);
                if ($text === '') {
                    continue;
                }

                $matchedY = null;
                foreach ($rowsByY as $yBucket => $cols) {
                    if (abs($yBucket - $y) <= 3) {
                        $matchedY = $yBucket;
                        break;
                    }
                }
                if ($matchedY === null) {
                    $matchedY = $y;
                    $rowsByY[$matchedY] = [];
                }
                $rowsByY[$matchedY][] = ['x' => $x, 'text' => $text];
            }

            krsort($rowsByY);

            foreach ($rowsByY as $yVal => $cols) {
                // Filter Y range for data rows (skip page header and footer)
                if ($yVal > 465 || $yVal < 290) {
                    continue;
                }

                usort($cols, fn($a, $b) => $a['x'] <=> $b['x']);

                $rowData = [
                    'y' => $yVal,
                    'no' => '',
                    'no_ref' => '',
                    'nik' => '',
                    'no_pegawai' => '',
                    'nama' => '',
                    'tgl_lahir' => '',
                    'tgl_kepesertaan' => '',
                    'upah' => 0.0,
                    'jkk' => 0.0,
                    'jkm' => 0.0,
                    'jht_pk' => 0.0,
                    'jht_tk' => 0.0,
                    'total_iuran' => 0.0,
                ];

                foreach ($cols as $c) {
                    $x = $c['x'];
                    $txt = $c['text'];

                    if ($x < 30) {
                        $rowData['no'] = $txt;
                    } elseif ($x < 75) {
                        $rowData['no_ref'] = $txt;
                    } elseif ($x < 145) {
                        $rowData['nik'] = $txt;
                    } elseif ($x < 190) {
                        $rowData['no_pegawai'] = $txt;
                    } elseif ($x < 300) {
                        $rowData['nama'] .= ($rowData['nama'] ? ' ' : '') . $txt;
                    } elseif ($x < 350) {
                        $rowData['tgl_lahir'] = $txt;
                    } elseif ($x < 410) {
                        $rowData['tgl_kepesertaan'] = $txt;
                    } elseif ($x < 500) {
                        $rowData['upah'] = $this->cleanNum($txt);
                    } elseif ($x >= 540 && $x < 590) {
                        $rowData['jkk'] = $this->cleanNum($txt);
                    } elseif ($x >= 590 && $x < 645) {
                        $rowData['jkm'] = $this->cleanNum($txt);
                    } elseif ($x >= 645 && $x < 705) {
                        $rowData['jht_pk'] = $this->cleanNum($txt);
                    } elseif ($x >= 705 && $x < 780) {
                        $rowData['jht_tk'] = $this->cleanNum($txt);
                    } elseif ($x >= 1010) {
                        $rowData['total_iuran'] = $this->cleanNum($txt);
                    }
                }

                $rowData['nama'] = trim($rowData['nama']);
                $rowData['no_pegawai'] = trim($rowData['no_pegawai']);

                if (!empty($rowData['nama']) && $rowData['nama'] !== 'Nama Tenaga Kerja' && is_numeric($rowData['no'])) {
                    $rows[] = $rowData;
                }
            }
        }

        return $rows;
    }

    /**
     * Check if any employee in the PDF file already has BPJSTK / SlipGaji data for the given month & year.
     *
     * @param \Illuminate\Http\UploadedFile|string $file
     * @param int|string $bulan
     * @param int|string $tahun
     * @return array
     */
    public function checkDuplicates($file, $bulan, $tahun): array
    {
        $rows = $this->parsePdf($file);
        if (empty($rows)) {
            return [
                'has_duplicates' => false,
                'duplicates' => [],
                'count' => 0,
            ];
        }

        $matchedKaryawanIds = [];
        $employeeNames = [];

        foreach ($rows as $row) {
            $karyawan = $this->findKaryawan($row['no_pegawai'], $row['nama']);
            if ($karyawan) {
                $matchedKaryawanIds[] = $karyawan->id_karyawan;
                $employeeNames[$karyawan->id_karyawan] = $karyawan->nama_karyawan;
            }
        }

        if (empty($matchedKaryawanIds)) {
            return [
                'has_duplicates' => false,
                'duplicates' => [],
                'count' => 0,
            ];
        }

        $existingSlips = SlipGaji::where('bulan', (string) $bulan)
            ->where('tahun', (string) $tahun)
            ->whereIn('id_karyawan', $matchedKaryawanIds)
            ->get();

        $duplicates = [];
        foreach ($existingSlips as $slip) {
            $duplicates[] = $slip->nama_karyawan ?: ($employeeNames[$slip->id_karyawan] ?? 'Unknown');
        }

        $duplicates = array_values(array_unique($duplicates));

        return [
            'has_duplicates' => !empty($duplicates),
            'duplicates' => $duplicates,
            'count' => count($duplicates),
        ];
    }

    /**
     * Process BPJSTK PDF import.
     *
     * @param \Illuminate\Http\UploadedFile|string $file
     * @param int|string $bulan
     * @param int|string $tahun
     * @param bool $overwrite
     * @return array
     */
    public function processImport($file, $bulan, $tahun, bool $overwrite = false): array
    {
        $rows = $this->parsePdf($file);

        $successCount = 0;
        $processedRows = [];
        $errors = [];
        $failedCount = 0;

        foreach ($rows as $idx => $row) {
            $noPegawai = $row['no_pegawai'];
            $namaPdf = $row['nama'];

            $validation = $this->validateAndFindKaryawan($noPegawai, $namaPdf);

            if (!$validation['success']) {
                $failedCount++;
                $errorMsg = $validation['error'];
                $row['status'] = 'GAGAL';
                $row['keterangan'] = $errorMsg;
                $processedRows[] = $row;
                $errors[] = "Baris " . ($idx + 1) . " ({$namaPdf}): {$errorMsg}";
                continue;
            }

            $karyawan = $validation['karyawan'];

            // Check duplicate slip if overwrite is false
            $existingSlip = SlipGaji::where('id_karyawan', $karyawan->id_karyawan)
                ->where('bulan', (string) $bulan)
                ->where('tahun', (string) $tahun)
                ->first();

            if ($existingSlip && !$overwrite) {
                $failedCount++;
                $errorMsg = "Slip gaji untuk '{$karyawan->nama_karyawan}' pada bulan {$bulan} tahun {$tahun} sudah ada.";
                $row['status'] = 'GAGAL';
                $row['keterangan'] = $errorMsg;
                $processedRows[] = $row;
                $errors[] = "Baris " . ($idx + 1) . " ({$namaPdf}): {$errorMsg}";
                continue;
            }

            try {
                DB::transaction(function () use ($karyawan, $row, $bulan, $tahun, $existingSlip) {
                    $tglKepesertaan = null;
                    if (!empty($row['tgl_kepesertaan'])) {
                        try {
                            $tglKepesertaan = Carbon::createFromFormat('d-m-Y', $row['tgl_kepesertaan'])->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $tglKepesertaan = null;
                        }
                    }

                    // Update or create tb_bpjstk
                    Bpjstk::updateOrCreate(
                        ['id_karyawan' => $karyawan->id_karyawan],
                        [
                            'no_referensi' => $row['no_ref'] ?: null,
                            'tanggal_kepesertaan' => $tglKepesertaan,
                            'upah_didaftarkan' => $row['upah'],
                            'iuran_jkk' => $row['jkk'],
                            'iuran_jkm' => $row['jkm'],
                            'pemberi_kerja' => $row['jht_pk'],
                            'tenaga_kerja' => $row['jht_tk'],
                            'total_iuran' => $row['total_iuran'],
                        ]
                    );

                    // Update or create SlipGaji for current month/year if slip exists or being imported
                    if ($existingSlip) {
                        $karyawan->load(['gaji', 'potongan', 'bpjstk', 'bpjsk', 'pph21']);

                        $gajiPokok = (float) ($existingSlip->gaji_pokok ?? $karyawan->gaji->gaji_pokok ?? 0);
                        $tPengalamanKerja = (float) ($existingSlip->t_pengalaman_kerja ?? $karyawan->gaji->t_pengalaman_kerja ?? 0);
                        $tJabatan = (float) ($existingSlip->t_jabatan ?? $karyawan->gaji->t_jabatan ?? 0);
                        $tProfesi = (float) ($existingSlip->t_profesi ?? $karyawan->gaji->t_profesi ?? 0);
                        $tOperasional = (float) ($existingSlip->t_operasional ?? $karyawan->gaji->t_operasional ?? 0);
                        $tKehadiran = (float) ($existingSlip->t_kehadiran ?? $karyawan->gaji->t_kehadiran ?? 0);
                        $tKinerja = (float) ($existingSlip->t_kinerja ?? $karyawan->gaji->t_kinerja ?? 0);

                        $tHariRaya = (float) ($existingSlip->t_hari_raya ?? 0);
                        $feeBeautician = (float) ($existingSlip->fee_beautician ?? 0);
                        $nominalLembur = (float) ($existingSlip->nominal_lembur ?? 0);
                        $pendapatanLainnya = (float) ($existingSlip->pendapatan_lainnya ?? 0);
                        $penyesuaianGajiLalu = (float) ($existingSlip->penyesuaian_gaji_lalu ?? 0);

                        $punishment = (float) ($existingSlip->punishment ?? 0);
                        $sedekahRombongan = (float) ($karyawan->potongan->potongan_sedekah_rombongan ?? 0);
                        $potonganLainnya = (float) ($existingSlip->potongan_lainnya ?? 0);

                        $bpjstkPerusahaan = (float) $row['total_iuran'];
                        $bpjskPerusahaan = (float) ($existingSlip->bpjsk_perusahaan ?? $karyawan->bpjsk->premi ?? 0);
                        $bpjstkKaryawan = (float) $row['jht_tk'];
                        $bpjskKaryawan = (float) ($existingSlip->bpjsk_karyawan ?? $karyawan->bpjsk->tanggungan_karyawan ?? 0);

                        $prorataBase = $gajiPokok + $tPengalamanKerja + $tJabatan + $tProfesi + $tKehadiran + $tKinerja + $tOperasional;
                        $nonProrata = $tHariRaya + $feeBeautician + $nominalLembur + $pendapatanLainnya;

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
                        $totalGaji = max(0, $subtotalPenerimaan + $bpjstkPerusahaan + $bpjskPerusahaan - $bpjstkKaryawan - $bpjskKaryawan);

                        $kategoriPph21 = $karyawan->pph21->kategori ?? null;
                        $pph21 = $this->calculatePph21($kategoriPph21, $totalGaji);

                        $nominalTransfer = $totalGaji - $totalPotonganLangsung - $pph21 - $bpjstkPerusahaan - $bpjskPerusahaan;
                        $totalDiterima = max(0, $nominalTransfer);

                        $existingSlip->update([
                            'bpjstk_perusahaan' => $bpjstkPerusahaan,
                            'bpjstk_karyawan' => $bpjstkKaryawan,
                            'subtotal_penerimaan' => $subtotalPenerimaan,
                            'pph21' => $pph21,
                            'total_diterima' => $totalDiterima,
                        ]);
                    }
                });

                $successCount++;
                $row['status'] = 'BERHASIL';
                $row['keterangan'] = 'Berhasil diimpor';
                $processedRows[] = $row;
            } catch (\Throwable $e) {
                $failedCount++;
                $errorMsg = "Error database: " . $e->getMessage();
                $row['status'] = 'GAGAL';
                $row['keterangan'] = $errorMsg;
                $processedRows[] = $row;
                $errors[] = "Baris " . ($idx + 1) . " ({$namaPdf}): {$errorMsg}";
                Log::error("BpjstkImport error baris " . ($idx + 1) . ": " . $e->getMessage());
            }
        }

        $errorPdfPath = null;

        if ($failedCount > 0) {
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $errorPdfPath = $tempDir . '/Hasil_Import_BPJSTK_Gagal_' . time() . '.pdf';
            $pdf = Pdf::loadView('pages.slip-gaji.bpjstk_error_report', [
                'rows' => $processedRows,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'successCount' => $successCount,
                'failedCount' => $failedCount,
            ])->setPaper('a4', 'landscape');

            file_put_contents($errorPdfPath, $pdf->output());
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
            'error_file' => $errorPdfPath,
        ];
    }

    /**
     * Validate employee with double checking (Nomor Pegawai / NIP + Nama) or Name only if NIP is empty.
     */
    private function validateAndFindKaryawan(string $noPegawai, string $namaPdf): array
    {
        $noPegawai = trim($noPegawai);
        $namaPdf = trim($namaPdf);

        if (empty($namaPdf)) {
            return ['success' => false, 'error' => 'Nama Tenaga Kerja pada PDF kosong.', 'karyawan' => null];
        }

        // Case 1: If Nomor Pegawai (NIP) is provided -> Double Checking
        if (!empty($noPegawai)) {
            $byNip = Karyawan::where('nip', $noPegawai)->first();
            $byName = Karyawan::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [mb_strtolower($namaPdf)])->first();

            if ($byNip && $byName) {
                if ($byNip->id_karyawan === $byName->id_karyawan) {
                    return ['success' => true, 'error' => null, 'karyawan' => $byNip];
                } else {
                    return [
                        'success' => false,
                        'error' => "NIP '{$noPegawai}' ({$byNip->nama_karyawan}) dan Nama '{$namaPdf}' ({$byName->nip}) tidak cocok.",
                        'karyawan' => null,
                    ];
                }
            }

            if ($byNip && !$byName) {
                return [
                    'success' => false,
                    'error' => "NIP '{$noPegawai}' ditemukan ({$byNip->nama_karyawan}), namun Nama '{$namaPdf}' tidak cocok.",
                    'karyawan' => null,
                ];
            }

            if (!$byNip && $byName) {
                return [
                    'success' => false,
                    'error' => "Nama '{$namaPdf}' ditemukan (NIP: {$byName->nip}), namun NIP '{$noPegawai}' di PDF tidak cocok.",
                    'karyawan' => null,
                ];
            }

            return [
                'success' => false,
                'error' => "Karyawan dengan NIP '{$noPegawai}' dan Nama '{$namaPdf}' tidak ditemukan di master data Karyawan.",
                'karyawan' => null,
            ];
        }

        // Case 2: If Nomor Pegawai (NIP) is empty -> Check by Name only
        $byName = Karyawan::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [mb_strtolower($namaPdf)])->first();

        if ($byName) {
            return ['success' => true, 'error' => null, 'karyawan' => $byName];
        }

        return [
            'success' => false,
            'error' => "Karyawan dengan nama '{$namaPdf}' tidak ditemukan di master data Karyawan.",
            'karyawan' => null,
        ];
    }

    private function findKaryawan(string $noPegawai, string $namaPdf): ?Karyawan
    {
        $res = $this->validateAndFindKaryawan($noPegawai, $namaPdf);
        return $res['success'] ? $res['karyawan'] : null;
    }

    private function cleanNum(mixed $val): float
    {
        if (is_null($val) || $val === '') {
            return 0.0;
        }

        $str = str_replace(',', '', (string) $val);
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
