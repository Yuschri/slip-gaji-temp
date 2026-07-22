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
        'bulan',
        'tahun',
        'id_karyawan',
        'gaji_pokok',
        't_pengalaman_kerja',
        't_jabatan',
        't_profesi',
        't_operasional',
        't_kehadiran',
        't_kinerja',
        't_hari_raya',
        'fee_beautician',
        'nominal_lembur',
        'pendapatan_lainnya',
        'penyesuaian_gaji_lalu',
        'subtotal_penerimaan',
        'bpjstk_perusahaan',
        'bpjsk_perusahaan',
        'punishment',
        'sedekah_rombongan',
        'potongan_lainnya',
        'bpjstk_karyawan',
        'bpjsk_karyawan',
        'pph21',
        'lembur_kali',
        'lembur_menit',
        'terlambat_kali',
        'terlambat_menit',
        'ijin_pulang_awal',
        'ijin_tidak_masuk',
        'no_checkin_or_checkout',
        'no_checkin_and_checkout',
        'cuti',
        'kehadiran_lainnya',
        'total_diterima',
        'is_resign',
        'tanggal_resign',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        't_pengalaman_kerja' => 'decimal:2',
        't_jabatan' => 'decimal:2',
        't_profesi' => 'decimal:2',
        't_operasional' => 'decimal:2',
        't_kehadiran' => 'decimal:2',
        't_kinerja' => 'decimal:2',
        't_hari_raya' => 'decimal:2',
        'fee_beautician' => 'decimal:2',
        'nominal_lembur' => 'decimal:2',
        'pendapatan_lainnya' => 'decimal:2',
        'penyesuaian_gaji_lalu' => 'decimal:2',
        'subtotal_penerimaan' => 'decimal:2',
        'bpjstk_perusahaan' => 'decimal:2',
        'bpjsk_perusahaan' => 'decimal:2',
        'punishment' => 'decimal:2',
        'sedekah_rombongan' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'bpjstk_karyawan' => 'decimal:2',
        'bpjsk_karyawan' => 'decimal:2',
        'pph21' => 'decimal:2',
        'lembur_kali' => 'integer',
        'lembur_menit' => 'integer',
        'terlambat_kali' => 'integer',
        'terlambat_menit' => 'integer',
        'ijin_pulang_awal' => 'integer',
        'ijin_tidak_masuk' => 'integer',
        'no_checkin_or_checkout' => 'integer',
        'no_checkin_and_checkout' => 'integer',
        'cuti' => 'integer',
        'kehadiran_lainnya' => 'integer',
        'total_diterima' => 'decimal:2',
        'is_resign' => 'boolean',
        'tanggal_resign' => 'date',
    ];

    // --- Relationships ---

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    // --- Virtual Accessors / Aliases for View & Export Compatibility ---

    public function getIdGajiAttribute()
    {
        return $this->id_slip;
    }

    public function getNamaKaryawanAttribute()
    {
        return $this->karyawan ? $this->karyawan->nama_karyawan : '';
    }

    public function getNipAttribute()
    {
        return $this->karyawan ? $this->karyawan->nip : '';
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

    public function getThpAttribute()
    {
        return $this->total_diterima;
    }

    public function getNominalTransferAttribute()
    {
        return $this->total_diterima;
    }

    public function getLainLainAttribute()
    {
        return $this->pendapatan_lainnya;
    }

    public function getBpjsTkAttribute()
    {
        return $this->bpjstk_perusahaan;
    }

    public function getBpjsKesehatanAttribute()
    {
        return $this->bpjsk_perusahaan;
    }

    public function getPotonganBpjsTkAttribute()
    {
        return $this->bpjstk_karyawan;
    }

    public function getPotonganBpjsKesehatanAttribute()
    {
        return $this->bpjsk_karyawan;
    }

    public function getPph21Attribute()
    {
        return $this->attributes['pph21'] ?? 0;
    }

    public function getPph21CalculatedAttribute()
    {
        return $this->attributes['pph21'] ?? 0;
    }

    public function getPotonganPph21Attribute()
    {
        return $this->attributes['pph21'] ?? 0;
    }

    public function getTerlambatAttribute()
    {
        return $this->terlambat_kali;
    }

    public function getIjinPulangCepatAttribute()
    {
        return $this->ijin_pulang_awal;
    }

    public function getNoCheckInOrOutAttribute()
    {
        return $this->no_checkin_or_checkout;
    }

    public function getNoCheckInAndOutAttribute()
    {
        return $this->no_checkin_and_checkout;
    }

    /**
     * Virtual relationship-like property for Kehadiran compatibility in views/PDFs.
     */
    public function getKehadiranAttribute()
    {
        return (object) [
            'id_kehadiran' => null,
            'cuti' => $this->cuti,
            'lembur' => $this->lembur_kali,
            'lembur_menit' => $this->lembur_menit,
            'terlambat' => $this->terlambat_kali,
            'terlambat_menit' => $this->terlambat_menit,
            'ijin_pulang_cepat' => $this->ijin_pulang_awal,
            'ijin_tidak_masuk' => $this->ijin_tidak_masuk,
            'no_check_in_or_out' => $this->no_checkin_or_checkout,
            'no_check_in_and_out' => $this->no_checkin_and_checkout,
        ];
    }
}
