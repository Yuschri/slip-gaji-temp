<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SkemaBPJSK extends Model
{
    protected $table = 'tb_skema_bpjsk';
    protected $primaryKey = 'id_skema_bpjsk';
    public $timestamps = true;

    protected $fillable = [
        'id_karyawan',
        'premi',
        'tanggungan_perusahaan',
        'tanggungan_karyawan',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
