<?php

namespace App\Repositories;

use App\Models\Jabatan;

class JabatanRepository
{
    /**
     * Get all positions.
     */
    public function all()
    {
        return Jabatan::all();
    }

    /**
     * Find position by ID.
     */
    public function find(int $id)
    {
        return Jabatan::findOrFail($id);
    }
}
