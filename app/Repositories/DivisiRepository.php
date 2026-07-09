<?php

namespace App\Repositories;

use App\Models\Divisi;

class DivisiRepository
{
    /**
     * Get all divisions.
     */
    public function all()
    {
        return Divisi::all();
    }

    /**
     * Find division by ID.
     */
    public function find(int $id)
    {
        return Divisi::findOrFail($id);
    }
}
