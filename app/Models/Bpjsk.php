<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bpjsk extends Model
{
    protected $table = 'tb_bpjsk';
    protected $primaryKey = 'id_bpjsk';
    public $timestamps = true;

    protected $fillable = [
        'id_karyawan',
        'beban_bpjsk',
        'no_jkn_pekerja',
        'no_jkn_peserta',
        'npp',
        'upah_didaftarkan',
        'premi',
        'tanggungan_perusahaan',
        'tanggungan_karyawan',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}