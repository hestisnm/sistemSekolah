<?php
namespace App\Services;

use App\Repositories\SiswaRepository;
use App\Models\Siswa;

class SiswaService
{
    protected $repo;

    public function __construct(SiswaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function createSiswa(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateSiswa(Siswa $siswa, array $data)
    {
        return $this->repo->update($siswa, $data);
    }

    public function deleteSiswa(Siswa $siswa)
    {
        return $this->repo->delete($siswa);
    }
}
