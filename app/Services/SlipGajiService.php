<?php

namespace App\Services;

use App\Repositories\SlipGajiRepository;
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

    /**
     * Clean numeric/Rupiah inputs (remove dots/commas if formatted).
     */
    public function cleanRupiah($value)
    {
        if ($value === null || $value === '') return 0;
        if (is_numeric($value)) return (float) $value;
        $clean = str_replace('.', '', (string) $value);
        $clean = preg_replace('/[^0-9\.-]/', '', $clean);
        return (float) ($clean ?: 0);
    }

    /**
     * Map request input data to tb_slip_gaji columns.
     */
    public function mapInputToSlipData(array $data)
    {
        return [
            'bulan' => (string) ($data['bulan'] ?? date('n')),
            'tahun' => (string) ($data['tahun'] ?? date('Y')),
            'id_karyawan' => $data['id_karyawan'],
            'gaji_pokok' => $this->cleanRupiah($data['gaji_pokok'] ?? 0),
            't_pengalaman_kerja' => $this->cleanRupiah($data['t_pengalaman_kerja'] ?? 0),
            't_jabatan' => $this->cleanRupiah($data['t_jabatan'] ?? 0),
            't_profesi' => $this->cleanRupiah($data['t_profesi'] ?? 0),
            't_operasional' => $this->cleanRupiah($data['t_operasional'] ?? $data['t__operasional'] ?? 0),
            't_kehadiran' => $this->cleanRupiah($data['t_kehadiran'] ?? 0),
            't_kinerja' => $this->cleanRupiah($data['t_kinerja'] ?? 0),
            't_hari_raya' => $this->cleanRupiah($data['t_hari_raya'] ?? 0),
            'fee_beautician' => $this->cleanRupiah($data['fee_beautician'] ?? 0),
            'nominal_lembur' => $this->cleanRupiah($data['nominal_lembur'] ?? $data['lembur'] ?? 0),
            'pendapatan_lainnya' => $this->cleanRupiah($data['pendapatan_lainnya'] ?? $data['lain_lain'] ?? 0),
            'penyesuaian_gaji_lalu' => $this->cleanRupiah($data['penyesuaian_gaji_lalu'] ?? 0),
            'subtotal_penerimaan' => $this->cleanRupiah($data['subtotal_penerimaan'] ?? $data['calculated_penerimaan'] ?? 0),
            'bpjstk_perusahaan' => $this->cleanRupiah($data['bpjstk_perusahaan'] ?? $data['bpjstk'] ?? $data['bpjs_tk'] ?? 0),
            'bpjsk_perusahaan' => $this->cleanRupiah($data['bpjsk_perusahaan'] ?? $data['bpjsk'] ?? $data['bpjs_kesehatan'] ?? 0),
            'punishment' => $this->cleanRupiah($data['punishment'] ?? 0),
            'sedekah_rombongan' => $this->cleanRupiah($data['sedekah_rombongan'] ?? 0),
            'potongan_lainnya' => $this->cleanRupiah($data['potongan_lainnya'] ?? 0),
            'bpjstk_karyawan' => $this->cleanRupiah($data['bpjstk_karyawan'] ?? $data['potongan_bpjs_tk'] ?? 0),
            'bpjsk_karyawan' => $this->cleanRupiah($data['bpjsk_karyawan'] ?? $data['potongan_bpjs_kesehatan'] ?? 0),
            'pph21' => $this->cleanRupiah($data['pph21'] ?? $data['potongan_pph_21'] ?? $data['pph_21'] ?? 0),
            'lembur_kali' => (int) ($data['lembur_kali'] ?? 0),
            'lembur_menit' => (int) ($data['lembur_menit'] ?? 0),
            'terlambat_kali' => (int) ($data['terlambat_kali'] ?? $data['terlambat'] ?? 0),
            'terlambat_menit' => (int) ($data['terlambat_menit'] ?? 0),
            'ijin_pulang_awal' => (int) ($data['ijin_pulang_awal'] ?? $data['ijin_pulang_cepat'] ?? 0),
            'ijin_tidak_masuk' => (int) ($data['ijin_tidak_masuk'] ?? 0),
            'no_checkin_or_checkout' => (int) ($data['no_checkin_or_checkout'] ?? $data['no_check_in_or_out'] ?? 0),
            'no_checkin_and_checkout' => (int) ($data['no_checkin_and_checkout'] ?? $data['no_check_in_and_out'] ?? 0),
            'cuti' => (int) ($data['cuti'] ?? 0),
            'kehadiran_lainnya' => (int) ($data['kehadiran_lainnya'] ?? 0),
            'total_diterima' => $this->cleanRupiah($data['total_diterima'] ?? $data['nominal_transfer'] ?? $data['thp'] ?? 0),
            'is_resign' => !empty($data['is_resign']),
            'tanggal_resign' => !empty($data['tanggal_resign']) ? $data['tanggal_resign'] : null,
        ];
    }

    public function store(array $data)
    {
        $slipData = $this->mapInputToSlipData($data);
        $slip = $this->slipGajiRepository->create($slipData);

        if (!empty($slipData['is_resign']) && !empty($slipData['id_karyawan'])) {
            \App\Models\Karyawan::where('id_karyawan', $slipData['id_karyawan'])
                ->update(['is_active' => false]);
        }

        return $slip;
    }

    public function update($id, array $data)
    {
        $slipData = $this->mapInputToSlipData($data);
        $slip = $this->slipGajiRepository->update($id, $slipData);

        if (!empty($slipData['is_resign']) && !empty($slipData['id_karyawan'])) {
            \App\Models\Karyawan::where('id_karyawan', $slipData['id_karyawan'])
                ->update(['is_active' => false]);
        }

        return $slip;
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
        $terbilang = $this->terbilang($slip->total_diterima) . ' Rupiah';

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
