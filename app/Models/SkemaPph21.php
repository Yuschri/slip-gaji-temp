<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkemaPph21 extends Model
{
    protected $table = 'tb_skema_pph21';
    protected $primaryKey = 'id_skema_pph21';
    public $timestamps = true;

    protected $fillable = [
        'golongan',
        'batas_uang',
        'persen',
    ];

    public function pph21()
    {
        return $this->hasMany(Pph21::class, 'id_skema_pph21', 'id_skema_pph21');
    }
}
