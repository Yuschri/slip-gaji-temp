<?php

namespace App\Repositories;

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

class KaryawanRepository
{
    /**
     * Get all employees with division and position relations.
     */
    public function all()
    {
        return Karyawan::with(['divisi', 'jabatan'])->get();
    }

    /**
     * Find employee by ID.
     */
    public function find(int $id)
    {
        return Karyawan::with(['divisi', 'jabatan', 'gaji', 'potongan'])->findOrFail($id);
    }

    /**
     * Create a new employee.
     */
    public function create(array $data)
    {
        return Karyawan::create($data);
    }

    /**
     * Update employee by ID.
     */
    public function update(int $id, array $data)
    {
        $karyawan = $this->find($id);
        $karyawan->update($data);
        return $karyawan;
    }

    /**
     * Delete employee by ID.
     */
    public function delete(int $id)
    {
        $karyawan = $this->find($id);
        return $karyawan->delete();
    }

    /**
     * Update or create salary and deduction data in one transaction.
     */
    public function updateOrCreateKompensasi(int $karyawanId, array $gajiData, array $potonganData): void
    {
        DB::transaction(function () use ($karyawanId, $gajiData, $potonganData) {
            $karyawan = $this->find($karyawanId);

            $karyawan->gaji()->updateOrCreate(
                ['id_karyawan' => $karyawanId],
                $gajiData
            );

            $karyawan->potongan()->updateOrCreate(
                ['id_karyawan' => $karyawanId],
                $potonganData
            );
        });
    }
}
