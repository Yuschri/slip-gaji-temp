<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'tb_karyawan';
    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'nip',
        'nama_karyawan',
        'tanggal_masuk',
        'id_divisi',
        'id_jabatan',
        'no_wa',
        'nomor_rekening',
        'cabang',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function gaji()
    {
        return $this->hasOne(Gaji::class, 'id_karyawan', 'id_karyawan');
    }

    public function potongan()
    {
        return $this->hasOne(Potongan::class, 'id_karyawan', 'id_karyawan');
    }
}
