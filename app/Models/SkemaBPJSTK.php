<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkemaBPJSTK extends Model
{
    protected $table = 'tb_skema_bpjstk';
    protected $primaryKey = 'id_skema_bpjstk';
    public $timestamps = true;

    protected $fillable = [
        'iuran_jkk',
        'iuran_jkm',
        'pemberi_kerja',
        'tenaga_kerja',
        'is_active',
    ];

    public function bpjstk()
    {
        return $this->hasMany(Bpjstk::class, 'id_skema_bpjstk', 'id_skema_bpjstk');
    }
}
