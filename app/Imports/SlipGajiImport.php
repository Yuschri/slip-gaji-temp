<?php

namespace App\Imports;

use App\Models\SlipGaji;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class SlipGajiImport implements ToModel, WithHeadingRow
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['nama_karyawan']) && empty($row['id_karyawan'])) {
            return null;
        }

        return new SlipGaji([
            'id_karyawan' => $row['id_karyawan'] ?? null,
            'nama_karyawan' => $row['nama_karyawan'],
            'tanggal_masuk' => $this->transformDate($row['tanggal_masuk'] ?? null),
            'divisi' => $row['divisi'] ?? '-',
            'klinik' => $row['klinik'] ?? '-',
            'no_wa' => $row['no_wa'] ?? '-',
            'nomor_rekening' => $row['nomor_rekening'] ?? '-',
            'thp' => $row['thp'] ?? 0,
            'gaji_pokok' => $row['gaji_pokok'] ?? 0,
            't_jabatan' => $row['t_jabatan'] ?? 0,
            't_profesi' => $row['t_profesi'] ?? 0,
            't_kehadiran' => $row['t_kehadiran'] ?? 0,
            't_kinerja' => $row['t_kinerja'] ?? 0,
            't_hari_raya' => $row['t_hari_raya'] ?? 0,
            'cuti' => $row['cuti'] ?? 0,
            'punishment' => $row['punishment'] ?? 0,
            'bpjstk' => $row['bpjstk'] ?? 0,
            'bpjs_kesehatan' => $row['bpjs_kesehatan'] ?? 0,
            'pph_21' => $row['pph_21'] ?? 0,
            'lembur' => $row['lembur'] ?? 0,
            'sedekah_rombongan' => $row['sedekah_rombongan'] ?? 0,
            'nominal_transfer' => $row['nominal_transfer'] ?? 0,
            'terlambat' => $row['terlambat'] ?? 0,
            'ijin_pulang_cepat' => $row['ijin_pulang_cepat'] ?? 0,
            'ijin_tidak_masuk' => $row['ijin_tidak_masuk'] ?? 0,
            'no_check_in_or_out' => $row['no_check_in_or_out'] ?? 0,
            'no_check_in_and_out' => $row['no_check_in_and_out'] ?? 0,
            'lain_lain' => $row['lain_lain'] ?? 0,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);
    }

    private function transformDate($value)
    {
        if (empty($value))
            return null;

        try {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        } catch (\ErrorException $e) {
            return Carbon::parse($value);
        }
    }
}
