<?php

namespace App\Repositories;

use App\Models\SlipGaji;

class SlipGajiRepository
{
    /**
     * Get all slip gaji records.
     */
    public function all()
    {
        // Eager load karyawan for performance when listing
        return SlipGaji::with('karyawan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
    }

    /**
     * Find slip gaji by ID.
     */
    public function find(int $id)
    {
        return SlipGaji::with('karyawan')->findOrFail($id);
    }

    /**
     * Create a new slip gaji record.
     */
    public function create(array $data)
    {
        return SlipGaji::create($data);
    }

    /**
     * Update slip gaji by ID.
     */
    public function update(int $id, array $data)
    {
        $slip = $this->find($id);
        $slip->update($data);
        return $slip;
    }

    /**
     * Delete slip gaji by ID.
     */
    public function delete(int $id)
    {
        $slip = $this->find($id);
        return $slip->delete();
    }

    /**
     * Get unique klinik/cabang names.
     */
    public function getUniqueKlinik()
    {
        return \App\Models\Karyawan::distinct()->pluck('cabang')->filter()->values();
    }

    /**
     * Get unique years of slip gaji.
     */
    public function getUniqueTahun()
    {
        return SlipGaji::distinct()->pluck('tahun')->sortDesc()->values();
    }
}
