<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlipGaji extends Model
{
    use HasFactory;

    protected $table = 'tb_slip_gaji';
    protected $primaryKey = 'id_slip';

    protected $fillable = [
        'id_karyawan',
        'thp',
        'gaji_pokok',
        't_pengalaman_kerja',
        't_jabatan',
        't_profesi',
        't_operasional',
        't_kehadiran',
        't_kinerja',
        't_hari_raya',
        'prosentase_gaji',
        'jumlah_hari_gabung',
        'nominal_lembur',
        'fee_beautician',
        'lain_lain',
        'punishment',
        'bpjs_tk',
        'bpjs_kesehatan',
        'pph_21',
        'potongan_bpjs_tk',
        'potongan_bpjs_kesehatan',
        'potongan_pph_21',
        'sedekah_rombongan',
        'potongan_lainnya',
        'nominal_transfer',
        'id_kehadiran',
        'kehadiran_lainnya',
        'bulan',
        'tahun',
    ];

    protected $casts = [
        'thp' => 'decimal:2',
        'gaji_pokok' => 'decimal:2',
        't_pengalaman_kerja' => 'decimal:2',
        't_jabatan' => 'decimal:2',
        't_profesi' => 'decimal:2',
        't_operasional' => 'decimal:2',
        't_kehadiran' => 'decimal:2',
        't_kinerja' => 'decimal:2',
        't_hari_raya' => 'decimal:2',
        'prosentase_gaji' => 'decimal:2',
        'jumlah_hari_gabung' => 'integer',
        'nominal_lembur' => 'decimal:2',
        'fee_beautician' => 'decimal:2',
        'lain_lain' => 'decimal:2',
        'punishment' => 'decimal:2',
        'bpjs_tk' => 'decimal:2',
        'bpjs_kesehatan' => 'decimal:2',
        'pph_21' => 'decimal:2',
        'potongan_bpjs_tk' => 'decimal:2',
        'potongan_bpjs_kesehatan' => 'decimal:2',
        'potongan_pph_21' => 'decimal:2',
        'sedekah_rombongan' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'nominal_transfer' => 'decimal:2',
        'kehadiran_lainnya' => 'integer',
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];

    // --- Relationships ---

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function kehadiran()
    {
        return $this->belongsTo(Kehadiran::class, 'id_kehadiran', 'id_kehadiran');
    }

    public function getNamaKaryawanAttribute()
    {
        return $this->karyawan ? $this->karyawan->nama_karyawan : '';
    }

    public function getTanggalMasukAttribute()
    {
        return $this->karyawan ? $this->karyawan->tanggal_masuk : null;
    }

    public function getDivisiAttribute()
    {
        return ($this->karyawan && $this->karyawan->divisi) ? $this->karyawan->divisi->nama_divisi : '';
    }

    public function getKlinikAttribute()
    {
        return $this->karyawan ? $this->karyawan->cabang : '';
    }

    public function getNoWaAttribute()
    {
        return $this->karyawan ? $this->karyawan->no_wa : '';
    }

    public function getNomorRekeningAttribute()
    {
        return $this->karyawan ? $this->karyawan->nomor_rekening : '';
    }

    // Kehadiran virtual fields
    public function getCutiAttribute()
    {
        return $this->kehadiran ? $this->kehadiran->cuti : 0;
    }

    public function getTerlambatAttribute()
    {
        return $this->kehadiran ? $this->kehadiran->terlambat : 0;
    }

    public function getIjinPulangCepatAttribute()
    {
        return $this->kehadiran ? $this->kehadiran->ijin_pulang_cepat : 0;
    }

    public function getIjinTidakMasukAttribute()
    {
        return $this->kehadiran ? $this->kehadiran->ijin_tidak_masuk : 0;
    }

    public function getNoCheckInOrOutAttribute()
    {
        return $this->kehadiran ? $this->kehadiran->no_check_in_or_out : 0;
    }

    public function getNoCheckInAndOutAttribute()
    {
        return $this->kehadiran ? $this->kehadiran->no_check_in_and_out : 0;
    }

    // lembur/bpjstk backward compatibility accessors/mutators
    public function getBpjstkAttribute()
    {
        return $this->bpjs_tk;
    }

    public function setBpjstkAttribute($value)
    {
        $this->attributes['bpjs_tk'] = $value;
    }

    public function getLemburAttribute()
    {
        return $this->nominal_lembur;
    }

    public function setLemburAttribute($value)
    {
        $this->attributes['nominal_lembur'] = $value;
    }
}
