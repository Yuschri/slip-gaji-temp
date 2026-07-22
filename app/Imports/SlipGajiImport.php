<?php

namespace App\Imports;

use App\Models\SlipGaji;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\SkemaPph21;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class SlipGajiImport implements ToCollection, WithStartRow
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = (int)$bulan;
        $this->tahun = (int)$tahun;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nip = trim($row[0] ?? '');
            if (empty($nip)) {
                continue;
            }

            // Find employee
            $karyawan = Karyawan::with(['divisi', 'jabatan', 'gaji', 'potongan', 'bpjstk', 'bpjsk', 'pph21'])
                ->where('nip', $nip)
                ->first();

            // 1. Karyawan harus aktif, ketika karyawan isActive = false jangan tambahkan data gaji
            if (!$karyawan || !$karyawan->is_active) {
                continue;
            }

            // Extract excel columns
            $t_kehadiran = (float)($row[1] ?? 0);
            $t_kinerja = (float)($row[2] ?? 0);
            $t_hari_raya = (float)($row[3] ?? 0);
            $fee_beautician = (float)($row[4] ?? 0);
            $nominal_lembur = (float)($row[5] ?? 0);
            $punishment = (float)($row[6] ?? 0);
            $lembur_kali = (int)($row[7] ?? 0);
            $lembur_menit = (int)($row[8] ?? 0);
            $terlambat_kali = (int)($row[9] ?? 0);
            $terlambat_menit = (int)($row[10] ?? 0);
            $ijin_pulang_awal = (int)($row[11] ?? 0);
            $ijin_tidak_masuk = (int)($row[12] ?? 0);
            $no_checkin_or_checkout = (int)($row[13] ?? 0);
            $no_checkin_and_checkout = (int)($row[14] ?? 0);
            $cuti = (int)($row[15] ?? 0);
            $kehadiran_lainnya = (int)($row[16] ?? 0);

            // Base employee values
            $gaji_pokok = (float)($karyawan->gaji->gaji_pokok ?? 0);
            $t_pengalaman_kerja = (float)($karyawan->gaji->t_pengalaman_kerja ?? 0);
            $t_jabatan = (float)($karyawan->gaji->t_jabatan ?? 0);
            $t_profesi = (float)($karyawan->gaji->t_profesi ?? 0);
            $t_operasional = (float)($karyawan->gaji->t_operasional ?? 0);
            
            $sedekah_rombongan = (float)($karyawan->potongan->potongan_sedekah_rombongan ?? 0);
            $bpjstk_perusahaan = (float)($karyawan->bpjstk->total_iuran ?? 0);
            $bpjsk_perusahaan = (float)($karyawan->bpjsk->premi ?? 0);
            $bpjstk_karyawan = (float)($karyawan->bpjstk->tenaga_kerja ?? 0);
            $bpjsk_karyawan = (float)($karyawan->bpjsk->tanggungan_karyawan ?? 0);
            
            $potongan_lainnya = 0;
            $pendapatan_lainnya = 0;
            $penyesuaian_gaji_lalu = 0;

            // Total THP before pro-rata / training adjustments
            $thpFull = $gaji_pokok + $t_pengalaman_kerja + $t_jabatan + $t_profesi + $t_kehadiran + $t_kinerja +
                       $t_hari_raya + $t_operasional + $fee_beautician + $nominal_lembur + $pendapatan_lainnya;

            // default subtotal penerimaan
            $subtotal_penerimaan = $thpFull;

            // Pro-rata / training calculation
            if ($karyawan->tanggal_masuk) {
                $tglMasuk = Carbon::parse($karyawan->tanggal_masuk)->startOfDay();
                $akhirTraining = $tglMasuk->copy()->addMonths(3)->subDay();
                $cutoff = (int)($karyawan->periode_cut_off ?? 21);

                // Periode berbasis cutoff
                $periodeAkhir = Carbon::create($this->tahun, $this->bulan, $cutoff)->startOfDay();
                $periodeAwal = $periodeAkhir->copy()->subMonthNoOverflow()->addDay();

                $totalHariPeriode = $periodeAwal->diffInDays($periodeAkhir) + 1;

                if ($tglMasuk->gt($periodeAkhir)) {
                    $subtotal_penerimaan = 0;
                } else {
                    $tglMulaiHitung = $tglMasuk->gt($periodeAwal) ? $tglMasuk->copy() : $periodeAwal->copy();

                    // Skenario A: Masa Transisi - akhirTraining di dalam periode
                    if ($akhirTraining->gte($tglMulaiHitung) && $akhirTraining->lt($periodeAkhir)) {
                        $hariTraining = $tglMulaiHitung->diffInDays($akhirTraining) + 1;
                        $gajiTraining = ($hariTraining / $totalHariPeriode) * 0.8 * $thpFull;

                        $tglMulaiLulus = $akhirTraining->copy()->addDay();
                        $hariLulus = $tglMulaiLulus->diffInDays($periodeAkhir) + 1;
                        $gajiLulus = ($hariLulus / $totalHariPeriode) * 1.0 * $thpFull;

                        $subtotal_penerimaan = $gajiTraining + $gajiLulus;
                    }
                    // Skenario B: Full training / masih dalam training
                    elseif ($periodeAkhir->lte($akhirTraining)) {
                        $hariKerja = $tglMulaiHitung->diffInDays($periodeAkhir) + 1;
                        if ($hariKerja === $totalHariPeriode) {
                            $subtotal_penerimaan = 0.8 * $thpFull;
                        } else {
                            $subtotal_penerimaan = ($hariKerja / $totalHariPeriode) * 0.8 * $thpFull;
                        }
                    }
                    // Skenario C: Sudah lulus training
                    else {
                        $hariKerja = $tglMulaiHitung->diffInDays($periodeAkhir) + 1;
                        if ($hariKerja === $totalHariPeriode) {
                            $subtotal_penerimaan = $thpFull;
                        } else {
                            $subtotal_penerimaan = ($hariKerja / $totalHariPeriode) * 1.0 * $thpFull;
                        }
                    }
                }
            }

            $subtotal_penerimaan = round($subtotal_penerimaan);

            // Calculate totalGaji for PPh21
            $totalDeductions = $punishment + $sedekah_rombongan + $potongan_lainnya;
            $totalGaji = $subtotal_penerimaan - $totalDeductions + $bpjstk_perusahaan + $bpjsk_perusahaan - $bpjstk_karyawan - $bpjsk_karyawan;
            if ($totalGaji < 0) {
                $totalGaji = 0;
            }

            // Calculate PPh21
            $kategori = $karyawan->pph21->kategori ?? null;
            $pph21 = 0;
            if ($kategori) {
                $skema = SkemaPph21::where('golongan', $kategori)
                    ->where('batas_uang', '<=', $totalGaji)
                    ->orderBy('batas_uang', 'desc')
                    ->first();
                $ter = $skema ? (float)$skema->persen : 0;
                $pph21 = round($totalGaji * $ter);
            }

            // Calculate final net transfer (total_diterima)
            $total_diterima = $subtotal_penerimaan - $totalDeductions + $bpjstk_perusahaan + $bpjsk_perusahaan - $bpjstk_karyawan - $bpjsk_karyawan - $pph21;
            if ($total_diterima < 0) {
                $total_diterima = 0;
            }

            // Create/update Kehadiran
            $kehadiran = Kehadiran::updateOrCreate(
                [
                    'id_karyawan' => $karyawan->id_karyawan,
                    'bulan' => $this->bulan,
                    'tahun' => $this->tahun
                ],
                [
                    'cuti' => $cuti,
                    'lembur' => $lembur_kali,
                    'lembur_menit' => $lembur_menit,
                    'terlambat' => $terlambat_kali,
                    'terlambat_menit' => $terlambat_menit,
                    'ijin_pulang_cepat' => $ijin_pulang_awal,
                    'ijin_tidak_masuk' => $ijin_tidak_masuk,
                    'no_check_in_or_out' => $no_checkin_or_checkout,
                    'no_check_in_and_out' => $no_checkin_and_checkout,
                ]
            );

            // Create/update Slip Gaji
            SlipGaji::updateOrCreate(
                [
                    'id_karyawan' => $karyawan->id_karyawan,
                    'bulan' => $this->bulan,
                    'tahun' => $this->tahun,
                ],
                [
                    'gaji_pokok' => $gaji_pokok,
                    't_pengalaman_kerja' => $t_pengalaman_kerja,
                    't_jabatan' => $t_jabatan,
                    't_profesi' => $t_profesi,
                    't_operasional' => $t_operasional,
                    't_kehadiran' => $t_kehadiran,
                    't_kinerja' => $t_kinerja,
                    't_hari_raya' => $t_hari_raya,
                    'fee_beautician' => $fee_beautician,
                    'nominal_lembur' => $nominal_lembur,
                    'pendapatan_lainnya' => $pendapatan_lainnya,
                    'penyesuaian_gaji_lalu' => $penyesuaian_gaji_lalu,
                    'subtotal_penerimaan' => $subtotal_penerimaan,
                    'bpjstk_perusahaan' => $bpjstk_perusahaan,
                    'bpjsk_perusahaan' => $bpjsk_perusahaan,
                    'punishment' => $punishment,
                    'sedekah_rombongan' => $sedekah_rombongan,
                    'potongan_lainnya' => $potongan_lainnya,
                    'bpjstk_karyawan' => $bpjstk_karyawan,
                    'bpjsk_karyawan' => $bpjsk_karyawan,
                    'pph21' => $pph21,
                    'lembur_kali' => $lembur_kali,
                    'lembur_menit' => $lembur_menit,
                    'terlambat_kali' => $terlambat_kali,
                    'terlambat_menit' => $terlambat_menit,
                    'ijin_pulang_awal' => $ijin_pulang_awal,
                    'ijin_tidak_masuk' => $ijin_tidak_masuk,
                    'no_checkin_or_checkout' => $no_checkin_or_checkout,
                    'no_checkin_and_checkout' => $no_checkin_and_checkout,
                    'cuti' => $cuti,
                    'kehadiran_lainnya' => $kehadiran_lainnya,
                    'total_diterima' => $total_diterima,
                    'is_resign' => false,
                    'tanggal_resign' => null,
                ]
            );
        }
    }
}
