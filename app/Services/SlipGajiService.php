<?php

namespace App\Services;

use App\Models\SlipGaji;
use App\Imports\SlipGajiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SlipGajiService
{
    public function getAll()
    {
        return SlipGaji::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
    }

    public function getUniqueKlinik()
    {
        return SlipGaji::distinct()->pluck('klinik')->filter()->values();
    }

    public function getUniqueTahun()
    {
        return SlipGaji::distinct()->pluck('tahun')->sortDesc()->values();
    }

    public function findById($id)
    {
        return SlipGaji::findOrFail($id);
    }

    public function store(array $data)
    {
        return SlipGaji::create($data);
    }

    public function update($id, array $data)
    {
        $slip = $this->findById($id);
        $slip->update($data);
        return $slip;
    }

    public function delete($id)
    {
        $slip = $this->findById($id);
        return $slip->delete();
    }

    public function import($file, $bulan, $tahun)
    {
        return Excel::import(new SlipGajiImport($bulan, $tahun), $file);
    }

    public function generatePdf($id)
    {
        $slip = $this->findById($id);
        $terbilang = $this->terbilang($slip->nominal_transfer) . ' Rupiah';

        return Pdf::loadView('pages.slip-gaji.slip_gaji_export', compact('slip', 'terbilang'))
            ->setPaper('a4', 'portrait');
    }

    private function terbilang($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilang($nilai / 10) . " Puluh" . $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang($nilai / 100) . " Ratus" . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang($nilai / 1000) . " Ribu" . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang($nilai / 1000000) . " Juta" . $this->terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->terbilang($nilai / 1000000000) . " Milyar" . $this->terbilang(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->terbilang($nilai / 1000000000000) . " Triliun" . $this->terbilang(fmod($nilai, 1000000000000));
        }
        return $temp;
    }
}
