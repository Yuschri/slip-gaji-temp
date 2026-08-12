<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\SkemaPph21;
use App\Models\SlipGaji;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SlipGajiImport implements ToCollection, WithStartRow, SkipsEmptyRows
{
    protected $bulan;
    protected $tahun;
    public int $imported = 0;
    public int $skipped = 0;
    public array $errors = [];

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
     * Start from row 5 (row 4 headers, row 5 example data).
     */
    public function startRow(): int
    {
        return 5;
    }

    /**
     * Column index mapping (0-based):
     *  0  = NIP
     *  1  = Nama Karyawan
     *  2  = Tunjangan Kehadiran
     *  3  = Tunjangan Kinerja
     *  4  = Tunjangan Hari Raya
     *  5  = Fee Beautician
     *  6  = Nominal Lembur
     *  7  = Pendapatan Lainnya
     *  8  = Penyesuaian Gaji Lalu
     *  9  = Punishment
     * 10  = Potongan Lainnya
     * 11  = Lembur (Kali)
     * 12  = Lembur (Menit)
     * 13  = Terlambat (Kali)
     * 14  = Terlambat (Menit)
     * 15  = Ijin Pulang Cepat
     * 16  = Ijin Tidak Masuk
     * 17  = No Check In/Out
     * 18  = No Check In & Out
     * 19  = Cuti (Hari)
     * 20  = Kehadiran Lainnya
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + $this->startRow();

            $nip = $this->cleanStr($row[0] ?? '');
            $namaKaryawan = $this->cleanStr($row[1] ?? '');

            if ($this->shouldSkipRow($nip, $namaKaryawan)) {
                continue;
            }

            try {
                DB::transaction(function () use ($row, $nip, $namaKaryawan) {
                    $karyawan = Karyawan::with(['gaji', 'potongan', 'bpjstk', 'bpjsk', 'pph21'])
                        ->where('nip', $nip)
                        ->first();

                    if (!$karyawan && !empty($namaKaryawan)) {
                        $karyawan = Karyawan::with(['gaji', 'potongan', 'bpjstk', 'bpjsk', 'pph21'])
                            ->where('nama_karyawan', $namaKaryawan)
                            ->first();
                    }

                    if (!$karyawan) {
                        throw new \RuntimeException('Karyawan tidak ditemukan. Pastikan NIP/Nama sesuai data master.');
                    }

                    $tKehadiran = $this->toNumber($row[2] ?? 0);
                    $tKinerja = $this->toNumber($row[3] ?? 0);
                    $tHariRaya = $this->toNumber($row[4] ?? 0);
                    $feeBeautician = $this->toNumber($row[5] ?? 0);
                    $nominalLembur = $this->toNumber($row[6] ?? 0);
                    $pendapatanLainnya = $this->toNumber($row[7] ?? 0);
                    $penyesuaianGajiLalu = $this->toNumber($row[8] ?? 0);
                    $punishment = $this->toNumber($row[9] ?? 0);
                    $potonganLainnya = $this->toNumber($row[10] ?? 0);

                    $lemburKali = $this->toInt($row[11] ?? 0);
                    $lemburMenit = $this->toInt($row[12] ?? 0);
                    $terlambatKali = $this->toInt($row[13] ?? 0);
                    $terlambatMenit = $this->toInt($row[14] ?? 0);
                    $ijinPulangCepat = $this->toInt($row[15] ?? 0);
                    $ijinTidakMasuk = $this->toInt($row[16] ?? 0);
                    $noCheckInOrOut = $this->toInt($row[17] ?? 0);
                    $noCheckInAndOut = $this->toInt($row[18] ?? 0);
                    $cuti = $this->toInt($row[19] ?? 0);
                    $kehadiranLainnya = $this->toInt($row[20] ?? 0);

                    $gajiPokok = (float) ($karyawan->gaji->gaji_pokok ?? 0);
                    $tPengalamanKerja = (float) ($karyawan->gaji->t_pengalaman_kerja ?? 0);
                    $tJabatan = (float) ($karyawan->gaji->t_jabatan ?? 0);
                    $tProfesi = (float) ($karyawan->gaji->t_profesi ?? 0);
                    $tOperasional = (float) ($karyawan->gaji->t_operasional ?? 0);

                    $sedekahRombongan = (float) ($karyawan->potongan->potongan_sedekah_rombongan ?? 0);
                    $bpjstkPerusahaan = (float) ($karyawan->bpjstk->total_iuran ?? 0);
                    $bpjskPerusahaan = (float) ($karyawan->bpjsk->premi ?? 0);
                    $bpjstkKaryawan = (float) ($karyawan->bpjstk->tenaga_kerja ?? 0);
                    $bpjskKaryawan = (float) ($karyawan->bpjsk->tanggungan_karyawan ?? 0);

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
                            $prorataBase
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

                    // Nominal transfer mengikuti perhitungan form:
                    // base saat ini - potongan langsung - PPh21 - BPJS perusahaan (TK + Kes).
                    $nominalTransfer = $totalGaji - $totalPotonganLangsung - $pph21 - $bpjstkPerusahaan - $bpjskPerusahaan;
                    $totalDiterima = max(0, $nominalTransfer);

                    Kehadiran::updateOrCreate(
                        [
                            'id_karyawan' => $karyawan->id_karyawan,
                            'bulan' => $this->bulan,
                            'tahun' => $this->tahun,
                        ],
                        [
                            'cuti' => $cuti,
                            'lembur' => $lemburKali,
                            'lembur_menit' => $lemburMenit,
                            'terlambat' => $terlambatKali,
                            'terlambat_menit' => $terlambatMenit,
                            'ijin_pulang_cepat' => $ijinPulangCepat,
                            'ijin_tidak_masuk' => $ijinTidakMasuk,
                            'no_check_in_or_out' => $noCheckInOrOut,
                            'no_check_in_and_out' => $noCheckInAndOut,
                        ]
                    );

                    SlipGaji::updateOrCreate(
                        [
                            'id_karyawan' => $karyawan->id_karyawan,
                            'bulan' => (string) $this->bulan,
                            'tahun' => (string) $this->tahun,
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

                    $this->imported++;
                });
            } catch (\Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum} (NIP: {$nip}): " . $e->getMessage();
                Log::error("SlipGajiImport error baris {$rowNum}: " . $e->getMessage());
            }
        }
    }

    private function shouldSkipRow(string $nip, string $namaKaryawan): bool
    {
        if ($nip === '' && $namaKaryawan === '') {
            return true;
        }

        $nipUpper = strtoupper($nip);
        $namaUpper = strtoupper($namaKaryawan);

        if (
            str_starts_with($nipUpper, '*') ||
            str_contains($nipUpper, 'NIP') ||
            str_contains($namaUpper, 'NAMA KARYAWAN')
        ) {
            return true;
        }

        return false;
    }

    private function cleanStr(mixed $value): string
    {
        return trim((string) ($value ?? ''));
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

    private function toInt(mixed $value): int
    {
        return (int) round($this->toNumber($value));
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

    /**
     * Match calculation behavior used in manual slip creation.
     */
    private function hitungGajiPerPeriode(?Carbon $tanggalMasuk, int $cutoff, float $thpFull): float
    {
        if (!$tanggalMasuk || $thpFull <= 0) {
            return $thpFull;
        }

        $cutoff = in_array($cutoff, [15, 21], true) ? $cutoff : 21;

        $tglMasuk = $tanggalMasuk->copy()->startOfDay();
        $akhirTraining = $tglMasuk->copy()->addMonths(3)->subDay();

        $periodeAkhir = Carbon::create((int) $this->tahun, (int) $this->bulan, $cutoff)->startOfDay();
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
