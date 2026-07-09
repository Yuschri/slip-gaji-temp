<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    use HasFactory;

    protected $table = 'tb_kehadiran';
    protected $primaryKey = 'id_kehadiran';

    protected $fillable = [
        'id_karyawan',
        'cuti',
        'lembur',
        'lembur_menit',
        'terlambat',
        'terlambat_menit',
        'ijin_pulang_cepat',
        'ijin_tidak_masuk',
        'no_check_in_or_out',
        'no_check_in_and_out',
        'bulan',
        'tahun',
    ];

    protected $casts = [
        'cuti' => 'integer',
        'lembur' => 'integer',
        'lembur_menit' => 'decimal:2',
        'terlambat' => 'integer',
        'terlambat_menit' => 'decimal:2',
        'ijin_pulang_cepat' => 'integer',
        'ijin_tidak_masuk' => 'integer',
        'no_check_in_or_out' => 'integer',
        'no_check_in_and_out' => 'integer',
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
