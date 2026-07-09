<?php

namespace App\Repositories;

use App\Models\Karyawan;

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
     * Update or create salary information for an employee.
     */
    public function updateOrCreateGaji(int $karyawanId, array $data)
    {
        $karyawan = $this->find($karyawanId);
        return $karyawan->gaji()->updateOrCreate(
            ['id_karyawan' => $karyawanId],
            $data
        );
    }

    /**
     * Update or create potongan information for an employee.
     */
    public function updateOrCreatePotongan(int $karyawanId, array $data)
    {
        $karyawan = $this->find($karyawanId);
        return $karyawan->potongan()->updateOrCreate(
            ['id_karyawan' => $karyawanId],
            $data
        );
    }
}
