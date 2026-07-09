<?php

namespace App\Services;

use App\Repositories\SlipGajiRepository;
use App\Models\Kehadiran;
use App\Imports\SlipGajiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SlipGajiService
{
    protected $slipGajiRepository;

    public function __construct(SlipGajiRepository $slipGajiRepository)
    {
        $this->slipGajiRepository = $slipGajiRepository;
    }

    public function getAll()
    {
        return $this->slipGajiRepository->all();
    }

    public function getUniqueKlinik()
    {
        return $this->slipGajiRepository->getUniqueKlinik();
    }

    public function getUniqueTahun()
    {
        return $this->slipGajiRepository->getUniqueTahun();
    }

    public function findById($id)
    {
        return $this->slipGajiRepository->find($id);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create or update Kehadiran record first
            $kehadiran = Kehadiran::updateOrCreate(
                [
                    'id_karyawan' => $data['id_karyawan'],
                    'bulan' => $data['bulan'],
                    'tahun' => $data['tahun']
                ],
                [
                    'cuti' => $data['cuti'] ?? 0,
                    'lembur' => $data['lembur_kali'] ?? 0, // lembur count
                    'lembur_menit' => $data['lembur_menit'] ?? 0, // lembur count
                    'terlambat' => $data['terlambat'] ?? 0,
                    'terlambat_menit' => $data['terlambat_menit'] ?? 0,
                    'ijin_pulang_cepat' => $data['ijin_pulang_cepat'] ?? 0,
                    'ijin_tidak_masuk' => $data['ijin_tidak_masuk'] ?? 0,
                    'no_check_in_or_out' => $data['no_check_in_or_out'] ?? 0,
                    'no_check_in_and_out' => $data['no_check_in_and_out'] ?? 0,
                ]
            );

            // 2. Set id_kehadiran in the slip gaji data
            $data['id_kehadiran'] = $kehadiran->id_kehadiran;

            // map bpjstk to bpjs_tk and lembur to nominal_lembur
            $data['bpjs_tk'] = $data['bpjstk'] ?? 0;
            $data['nominal_lembur'] = $data['lembur'] ?? 0;

            $data['t_operasional'] = $data['t_operasional'] ?? $data['t__operasional'] ?? $data['t_ operasional'] ?? 0;

            // 3. Store the slip gaji
            return $this->slipGajiRepository->create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $slip = $this->findById($id);

            // 1. Update the Kehadiran record
            $kehadiran = Kehadiran::updateOrCreate(
                [
                    'id_karyawan' => $data['id_karyawan'] ?? $slip->id_karyawan,
                    'bulan' => $data['bulan'] ?? $slip->bulan,
                    'tahun' => $data['tahun'] ?? $slip->tahun
                ],
                [
                    'cuti' => $data['cuti'] ?? 0,
                    'lembur' => $data['lembur_kali'] ?? 0,
                    'lembur_menit' => $data['lembur_menit'] ?? 0,
                    'terlambat' => $data['terlambat'] ?? 0,
                    'terlambat_menit' => $data['terlambat_menit'] ?? 0,
                    'ijin_pulang_cepat' => $data['ijin_pulang_cepat'] ?? 0,
                    'ijin_tidak_masuk' => $data['ijin_tidak_masuk'] ?? 0,
                    'no_check_in_or_out' => $data['no_check_in_or_out'] ?? 0,
                    'no_check_in_and_out' => $data['no_check_in_and_out'] ?? 0,
                ]
            );

            // 2. Set id_kehadiran
            $data['id_kehadiran'] = $kehadiran->id_kehadiran;

            // map bpjstk to bpjs_tk and lembur to nominal_lembur
            $data['bpjs_tk'] = $data['bpjstk'] ?? 0;
            $data['nominal_lembur'] = $data['lembur'] ?? 0;

            $data['t_operasional'] = $data['t_operasional'] ?? $data['t__operasional'] ?? $data['t_ operasional'] ?? 0;

            // 3. Update the slip gaji
            return $this->slipGajiRepository->update($id, $data);
        });
    }

    public function delete($id)
    {
        return $this->slipGajiRepository->delete($id);
    }

    public function import($file, $bulan, $tahun)
    {
        return Excel::import(new SlipGajiImport($bulan, $tahun), $file);
    }

    public function generatePdf($id)
    {
        $slip = $this->findById($id);
        $terbilang = $this->terbilang($slip->nominal_transfer) . ' Rupiah';

        return Pdf::loadView('pages.slip-gaji.slip_gaji_export', compact('slip', 'terbilang'))
            ->setPaper('a4', 'portrait');
    }

    private function terbilang($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilang($nilai / 10) . " Puluh" . $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang($nilai / 100) . " Ratus" . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang($nilai / 1000) . " Ribu" . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang($nilai / 1000000) . " Juta" . $this->terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->terbilang($nilai / 1000000000) . " Milyar" . $this->terbilang(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->terbilang($nilai / 1000000000000) . " Triliun" . $this->terbilang(fmod($nilai, 1000000000000));
        }
        return $temp;
    }
}
