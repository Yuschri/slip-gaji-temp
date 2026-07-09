<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan extends Model
{
    use HasFactory;

    protected $table = 'tb_potongan';
    protected $primaryKey = 'id_potongan';

    protected $fillable = [
        'id_karyawan',
        'potongan_sedekah_rombongan',
    ];

    protected $casts = [
        'potongan_sedekah_rombongan' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
