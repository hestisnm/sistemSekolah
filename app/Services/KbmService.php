<?php

namespace App\Services;

use App\Repositories\KbmRepository;

class KbmService
{
    protected $kbmRepo;

    public function __construct(KbmRepository $kbmRepo)
    {
        $this->kbmRepo = $kbmRepo;
    }

    public function getScheduleForAjax($request)
    {
        $role = session('role');
        $userId = null;

        if ($role == 'guru') {
            $userId = session('guru_id');
        } elseif ($role == 'siswa') {
            $userId = session('siswa_id');
        }

        $keyword = $request->input('q');

        return $this->kbmRepo->getSchedule($role, $userId, $keyword);
    }
}
