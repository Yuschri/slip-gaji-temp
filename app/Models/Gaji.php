<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    protected $table = 'tb_gaji';
    protected $primaryKey = 'id_gaji';

    protected $fillable = [
        'id_karyawan',
        'gaji_pokok',
        't_pengalaman_kerja',
        't_jabatan',
        't_profesi',
        't_kehadiran',
        't_kinerja',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        't_pengalaman_kerja' => 'decimal:2',
        't_jabatan' => 'decimal:2',
        't_profesi' => 'decimal:2',
        't_kehadiran' => 'decimal:2',
        't_kinerja' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
