<?php

namespace App\Repositories;

use App\Models\Guru;
use Illuminate\Support\Facades\DB;

class GuruRepository
{
    public function findByAdminId(int $adminId): ?Guru
    {
        return Guru::where('admin_id', $adminId)->first();
    }

    public function findOrCreateByAdminId(int $adminId, string $nama, string $mapel): Guru
    {
        $guru = Guru::where('admin_id', $adminId)->first();

        if (!$guru) {
            $guru = Guru::create([
                'admin_id' => $adminId,
                'nama' => $nama,
                'mapel' => $mapel,
            ]);
        }

        return $guru;
    }
}
