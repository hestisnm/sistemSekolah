<?php

namespace App\Repositories;

use App\Models\Guru;
use Illuminate\Support\Facades\DB;

class GuruRepository
{
    public function findOrCreateByAdminId(int $adminId, string $username): Guru
    {
        $guru = Guru::where('admin_id', $adminId)->first();

        if (!$guru) {
            $guru = Guru::create([
                'admin_id' => $adminId,
                'nama' => $username,
                'mapel' => 'Umum',
            ]);
        }

        return $guru;
    }
}
