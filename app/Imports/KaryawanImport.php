<?php

namespace App\Imports;

use App\Models\Bpjsk;
use App\Models\Bpjstk;
use App\Models\Divisi;
use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Potongan;
use App\Models\Pph21;
use App\Models\SkemaBPJSK;
use App\Models\SkemaBPJSTK;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class KaryawanImport implements ToCollection, WithStartRow, SkipsEmptyRows
{
    public int $imported = 0;
    public int $skipped = 0;
    public array $errors = [];

    /** Start reading from row 5 (row 4 = headers, row 5 = example data placeholder, actual data from row 5 after user deletes example) */
    public function startRow(): int
    {
        return 5;
    }

    /**
     * Column index mapping (0-based):
     *  0  = NIP
     *  1  = NIK
     *  2  = Tanggal Lahir
     *  3  = Tanggal Masuk
     *  4  = Nama Karyawan
     *  5  = Cabang
     *  6  = Divisi
     *  7  = Jabatan
     *  8  = Nomor Rekening
     *  9  = Nomor WhatsApp
     * 10  = Periode Cut Off
     * 11  = Gaji Pokok
     * 12  = T. Pengalaman Kerja
     * 13  = T. Jabatan
     * 14  = T. Profesi
     * 15  = T. Kehadiran
     * 16  = T. Kinerja
     * 17  = T. Operasional
     * 18  = Sedekah Rombongan
     * 19  = No. Referensi TK
     * 20  = Upah yang Didaftarkan TK
     * 21  = No. JKN Peserta
     * 22  = Beban BPJS Kesehatan (jumlah tanggungan)
     * 23  = NPP
     * 24  = Upah yang Didaftarkan KS
     * 25  = Identitas (NPWP/KTP)
     * 26  = PTKP
     */
    public function collection(Collection $rows)
    {
        // Load active BPJS schemas once
        $skemaBpjstk = SkemaBPJSTK::where('is_active', true)->first();
        $skemaBpjsk  = SkemaBPJSK::where('is_active', true)->first();

        // PTKP to Kategori mapping (same as show.blade.php)
        $ptkpToKategori = [
            'TK/0' => 'A',
            'TK/1' => 'A',
            'K/0'  => 'A',
            'TK/2' => 'B',
            'TK/3' => 'B',
            'K/1'  => 'B',
            'K/2'  => 'B',
            'K/3'  => 'C',
        ];

        $BPJSK_PREMI_MAX = 600000;
        $BPJSK_UPAH_MAX  = 12000000;

        foreach ($rows as $index => $row) {
            $rowNum = $index + 5; // actual Excel row number

            // Skip note/instruction rows
            $nip = $this->cleanStr($row[0] ?? '');
            if (empty($nip) || str_starts_with($nip, '*') || str_starts_with($nip, '-') || str_starts_with($nip, 'NIP')) {
                continue;
            }

            $namaKaryawan = $this->cleanStr($row[4] ?? '');
            if (empty($namaKaryawan)) {
                $this->skipped++;
                continue;
            }

            try {
                DB::transaction(function () use (
                    $row, $rowNum, $nip, $namaKaryawan,
                    $skemaBpjstk, $skemaBpjsk,
                    $ptkpToKategori, $BPJSK_PREMI_MAX, $BPJSK_UPAH_MAX
                ) {
                    // ─── 1. Resolve Divisi & Jabatan ───────────────────────────────────
                    $namaDivisi  = $this->cleanStr($row[6] ?? '');
                    $namaJabatan = $this->cleanStr($row[7] ?? '');

                    $divisi  = Divisi::firstOrCreate(['nama_divisi'  => $namaDivisi]);
                    $jabatan = Jabatan::firstOrCreate(['nama_jabatan' => $namaJabatan]);

                    // ─── 2. Parse dates ────────────────────────────────────────────────
                    $tanggalLahir = $this->parseDate($row[2] ?? '');
                    $tanggalMasuk = $this->parseDate($row[3] ?? '');

                    // ─── 3. Periode Cut Off ────────────────────────────────────────────
                    $cutoff = (int) ($row[10] ?? 21);
                    if (!in_array($cutoff, [15, 21])) $cutoff = 21;

                    // ─── 4. Create / Update Karyawan ───────────────────────────────────
                    $karyawan = Karyawan::updateOrCreate(
                        ['nip' => $nip],
                        [
                            'nik'             => $this->cleanStr($row[1] ?? ''),
                            'tanggal_lahir'   => $tanggalLahir,
                            'tanggal_masuk'   => $tanggalMasuk,
                            'nama_karyawan'   => $namaKaryawan,
                            'cabang'          => $this->cleanStr($row[5] ?? ''),
                            'id_divisi'       => $divisi->id_divisi,
                            'id_jabatan'      => $jabatan->id_jabatan,
                            'nomor_rekening'  => $this->cleanStr($row[8] ?? ''),
                            'no_wa'           => $this->cleanStr($row[9] ?? ''),
                            'periode_cut_off' => $cutoff,
                            'is_active'       => true,
                        ]
                    );

                    $karyawanId = $karyawan->id_karyawan;

                    // ─── 5. Gaji & Tunjangan ──────────────────────────────────────────
                    $gajiPokok          = $this->toNumber($row[11] ?? 0);
                    $tPengalamanKerja   = $this->toNumber($row[12] ?? 0);
                    $tJabatan           = $this->toNumber($row[13] ?? 0);
                    $tProfesi           = $this->toNumber($row[14] ?? 0);
                    $tKehadiran         = $this->toNumber($row[15] ?? 0);
                    $tKinerja           = $this->toNumber($row[16] ?? 0);
                    $tOperasional       = $this->toNumber($row[17] ?? 0);
                    $sedekahRombongan   = $this->toNumber($row[18] ?? 0);

                    Gaji::updateOrCreate(
                        ['id_karyawan' => $karyawanId],
                        [
                            'gaji_pokok'        => $gajiPokok,
                            't_pengalaman_kerja' => $tPengalamanKerja,
                            't_jabatan'         => $tJabatan,
                            't_profesi'         => $tProfesi,
                            't_kehadiran'       => $tKehadiran,
                            't_kinerja'         => $tKinerja,
                            't_operasional'     => $tOperasional,
                        ]
                    );

                    Potongan::updateOrCreate(
                        ['id_karyawan' => $karyawanId],
                        ['potongan_sedekah_rombongan' => $sedekahRombongan]
                    );

                    // ─── 6. BPJS Ketenagakerjaan ──────────────────────────────────────
                    $noRefTk   = $this->cleanStr($row[19] ?? '');
                    $upahTk    = $this->toNumber($row[20] ?? 0);

                    $iuranJkk       = 0;
                    $iuranJkm       = 0;
                    $pemberiKerja   = 0;
                    $tenagaKerja    = 0;
                    $totalIuranTk   = 0;

                    if ($skemaBpjstk && $upahTk > 0) {
                        $iuranJkk     = round($upahTk * $skemaBpjstk->iuran_jkk);
                        $iuranJkm     = round($upahTk * $skemaBpjstk->iuran_jkm);
                        $pemberiKerja = round($upahTk * $skemaBpjstk->pemberi_kerja);
                        $tenagaKerja  = round($upahTk * $skemaBpjstk->tenaga_kerja);
                        $totalIuranTk = $iuranJkk + $iuranJkm + $pemberiKerja + $tenagaKerja;
                    }

                    Bpjstk::updateOrCreate(
                        ['id_karyawan' => $karyawanId],
                        [
                            'no_referensi'   => $noRefTk,
                            'upah_didaftarkan' => $upahTk,
                            'iuran_jkk'      => $iuranJkk,
                            'iuran_jkm'      => $iuranJkm,
                            'pemberi_kerja'  => $pemberiKerja,
                            'tenaga_kerja'   => $tenagaKerja,
                            'total_iuran'    => $totalIuranTk,
                        ]
                    );

                    // ─── 7. BPJS Kesehatan ────────────────────────────────────────────
                    $noJkn    = $this->cleanStr($row[21] ?? '');
                    $beban    = (int) ($row[22] ?? 0);
                    $npp      = $this->cleanStr($row[23] ?? '');
                    $upahKs   = $this->toNumber($row[24] ?? 0);

                    $premi              = 0;
                    $tanggunganPerusahaan = 0;
                    $tanggunganKaryawan  = 0;

                    if ($skemaBpjsk && $upahKs > 0) {
                        // Cap upah at max (same logic as show.blade.php)
                        $upahForCalc = min($upahKs, $BPJSK_UPAH_MAX);
                        $premi = round($upahForCalc * $skemaBpjsk->premi);
                        if ($premi > $BPJSK_PREMI_MAX) {
                            $premi = $BPJSK_PREMI_MAX;
                            $upahForCalc = $BPJSK_UPAH_MAX;
                        }

                        // Tanggungan karyawan rate += beban * 1%
                        $tkRate = $skemaBpjsk->tanggungan_karyawan + ($beban > 0 ? $beban * 0.01 : 0);

                        $tanggunganPerusahaan = round($upahForCalc * $skemaBpjsk->tanggungan_perusahaan);
                        $tanggunganKaryawan   = round($upahForCalc * $tkRate);
                    }

                    Bpjsk::updateOrCreate(
                        ['id_karyawan' => $karyawanId],
                        [
                            'no_jkn_peserta'       => $noJkn,
                            'beban_bpjsk'          => $beban,
                            'npp'                  => $npp,
                            'upah_didaftarkan'     => $upahKs,
                            'premi'                => $premi,
                            'tanggungan_perusahaan' => $tanggunganPerusahaan,
                            'tanggungan_karyawan'  => $tanggunganKaryawan,
                        ]
                    );

                    // ─── 8. PPh 21 ────────────────────────────────────────────────────
                    $identitas = $this->cleanStr($row[25] ?? '');
                    $ptkp      = strtoupper($this->cleanStr($row[26] ?? ''));
                    $kategori  = $ptkpToKategori[$ptkp] ?? 'A';

                    if (!empty($identitas) && !empty($ptkp)) {
                        Pph21::updateOrCreate(
                            ['id_karyawan' => $karyawanId],
                            [
                                'identitas' => $identitas,
                                'ptkp'      => $ptkp,
                                'kategori'  => $kategori,
                            ]
                        );
                    }

                    $this->imported++;
                });
            } catch (\Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum} (NIP: {$nip}): " . $e->getMessage();
                Log::error("KaryawanImport error baris {$rowNum}: " . $e->getMessage());
            }
        }
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function cleanStr(mixed $value): string
    {
        return trim((string) ($value ?? ''));
    }

    private function toNumber(mixed $value): float
    {
        if (is_null($value) || $value === '') return 0;
        // Remove thousand separators (.) if value is string
        $str = str_replace(['.', ','], ['', '.'], (string) $value);
        return (float) preg_replace('/[^0-9.]/', '', $str);
    }

    private function parseDate(mixed $value): ?string
    {
        if (empty($value)) return null;

        // If numeric (Excel serial date)
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $date->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $str = trim((string) $value);
        if (empty($str) || $str === '-') return null;

        // Try common formats
        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d', 'm/d/Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $str);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        // Last resort: strtotime
        $ts = strtotime($str);
        return $ts ? date('Y-m-d', $ts) : null;
    }
}
