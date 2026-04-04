<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlipGaji extends Model
{
    use HasFactory;

    protected $table = 'tb_slip_gaji';
    protected $primaryKey = 'id_slip';

    protected $fillable = [
        'id_karyawan',
        'nama_karyawan',
        'tanggal_masuk',
        'divisi',
        'klinik',
        'no_wa',
        'nomor_rekening',
        'thp',
        'gaji_pokok',
        't_jabatan',
        't_profesi',
        't_kehadiran',
        't_kinerja',
        't_hari_raya',
        'cuti',
        'punishment',
        'bpjstk',
        'bpjs_kesehatan',
        'pph_21',
        'lembur',
        'sedekah_rombongan',
        'nominal_transfer',
        'terlambat',
        'ijin_pulang_cepat',
        'ijin_tidak_masuk',
        'no_check_in_or_out',
        'no_check_in_and_out',
        'lain_lain',
        'bulan',
        'tahun',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'thp' => 'decimal:2',
        'gaji_pokok' => 'decimal:2',
        't_jabatan' => 'decimal:2',
        't_profesi' => 'decimal:2',
        't_kehadiran' => 'decimal:2',
        't_kinerja' => 'decimal:2',
        't_hari_raya' => 'decimal:2',
        'punishment' => 'decimal:2',
        'bpjstk' => 'decimal:2',
        'bpjs_kesehatan' => 'decimal:2',
        'pph_21' => 'decimal:2',
        'lembur' => 'decimal:2',
        'sedekah_rombongan' => 'decimal:2',
        'nominal_transfer' => 'decimal:2',
        'lain_lain' => 'decimal:2',
        'cuti' => 'integer',
        'terlambat' => 'integer',
        'ijin_pulang_cepat' => 'integer',
        'ijin_tidak_masuk' => 'integer',
        'no_check_in_or_out' => 'integer',
        'no_check_in_and_out' => 'integer',
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];
}
