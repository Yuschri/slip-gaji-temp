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
        'nik',
        'nama_karyawan',
        'tanggal_lahir',
        'tanggal_masuk',
        'id_divisi',
        'id_jabatan',
        'no_wa',
        'nomor_rekening',
        'cabang',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
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

    public function bpjsk()
    {
        return $this->hasOne(Bpjstk::class, 'id_karyawan', 'id_karyawan');
    }

    public function bpjstk()
    {
        return $this->hasOne(Bpjstk::class, 'id_karyawan', 'id_karyawan');
    }
}
