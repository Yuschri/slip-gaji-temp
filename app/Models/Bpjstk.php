<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bpjstk extends Model
{
    protected $table = 'tb_bpjstk';
    protected $primaryKey = 'id_bpjstk';
    public $timestamps = true;

    protected $fillable = [
        'id_karyawan',
        'no_referensi',
        'tanggal_kepesertaan',
        'upah_didaftarkan',
        'iuran_jkk',
        'iuran_jkm',
        'pemberi_kerja',
        'tenaga_kerja',
        'total_iuran',
    ];

    public function skema()
    {
        return $this->belongsTo(SkemaBpjstk::class, 'id_skema_bpjstk', 'id_skema_bpjstk');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}