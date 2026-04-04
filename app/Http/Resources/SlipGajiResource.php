<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlipGajiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_slip' => $this->id_slip,
            'id_karyawan' => $this->id_karyawan,
            'nama_karyawan' => $this->nama_karyawan,
            'tanggal_masuk' => $this->tanggal_masuk ? $this->tanggal_masuk->format('Y-m-d') : null,
            'divisi' => $this->divisi,
            'klinik' => $this->klinik,
            'no_wa' => $this->no_wa,
            'nomor_rekening' => $this->nomor_rekening,
            'thp' => $this->thp,
            'gaji_pokok' => $this->gaji_pokok,
            't_jabatan' => $this->t_jabatan,
            't_profesi' => $this->t_profesi,
            't_kehadiran' => $this->t_kehadiran,
            't_kinerja' => $this->t_kinerja,
            't_hari_raya' => $this->t_hari_raya,
            'cuti' => $this->cuti,
            'punishment' => $this->punishment,
            'bpjstk' => $this->bpjstk,
            'bpjs_kesehatan' => $this->bpjs_kesehatan,
            'pph_21' => $this->pph_21,
            'lembur' => $this->lembur,
            'sedekah_rombongan' => $this->sedekah_rombongan,
            'nominal_transfer' => $this->nominal_transfer,
            'terlambat' => $this->terlambat,
            'ijin_pulang_cepat' => $this->ijin_pulang_cepat,
            'ijin_tidak_masuk' => $this->ijin_tidak_masuk,
            'no_check_in_or_out' => $this->no_check_in_or_out,
            'no_check_in_and_out' => $this->no_check_in_and_out,
            'lain_lain' => $this->lain_lain,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
