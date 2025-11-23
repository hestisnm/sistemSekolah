<?php

namespace App\Repositories;

use App\Models\konten;

class KontenRepository
{
    public function getAll()
    {
        return konten::all();
    }

    public function findById($id)
    {
        return konten::findOrFail($id);
    }
}
