<?php

namespace App\Repositories;

use App\Models\Kbm;
use App\Models\Siswa;

class KbmRepository
{
    public function getSchedule($role, $userId, $keyword = null)
    {
        $query = Kbm::with(['guru', 'walas']);

        if ($role == 'guru') {
            $query->where('idguru', $userId);
        } elseif ($role == 'siswa') {
            $siswa = Siswa::find($userId);
            if ($siswa && $siswa->kelas) {
                $query->where('idwalas', $siswa->kelas->idwalas);
            } else {
                return collect(); // Return empty collection if student has no class
            }
        }

        if ($keyword) {
            $lowerKeyword = strtolower($keyword);
            $query->where(function ($q) use ($lowerKeyword) {
                $q->whereHas('guru', function ($subQuery) use ($lowerKeyword) {
                    $subQuery->whereRaw('LOWER(nama) LIKE ?', ["%{$lowerKeyword}%"])
                             ->orWhereRaw('LOWER(mapel) LIKE ?', ["%{$lowerKeyword}%"]);
                })->orWhereHas('walas', function ($subQuery) use ($lowerKeyword) {
                    $subQuery->whereRaw('LOWER(nama_kelas) LIKE ?', ["%{$lowerKeyword}%"]);
                });
            });
        }

        return $query->get();
    }
}
