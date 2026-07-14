<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pph21 extends Model
{
    protected $table = 'tb_pph21';
    protected $primaryKey = 'id_pph21';
    public $timestamps = true;

    protected $fillable = [
        'id_karyawan',
        'id_skema_pph21',
        'identitas',
        'ptkp',
    ];

    public function skema()
    {
        return $this->belongsTo(SkemaPph21::class, 'id_skema_pph21', 'id_skema_pph21');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}