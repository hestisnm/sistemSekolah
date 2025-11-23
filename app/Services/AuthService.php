<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\GuruRepository;
use App\Repositories\SiswaRepository;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{
    protected $adminRepo;
    protected $guruRepo;
    protected $siswaRepo;

    public function __construct(AdminRepository $adminRepo, GuruRepository $guruRepo, SiswaRepository $siswaRepo)
    {
        $this->adminRepo = $adminRepo;
        $this->guruRepo = $guruRepo;
        $this->siswaRepo = $siswaRepo;
    }

    public function login(array $credentials)
    {
        $user = $this->adminRepo->findByUsername($credentials['username']);

        if (!$user) {
            throw new Exception('Username tidak ditemukan!');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw new Exception('Password salah!');
        }

        session(['username' => $user->username, 'role' => $user->role]);

        if ($user->role == 'guru') {
            $guru = $this->guruRepo->findOrCreateByAdminId($user->id, $user->username);
            session(['guru_id' => $guru->idguru]);
        } elseif ($user->role == 'siswa') {
            $siswa = $this->siswaRepo->findOrCreateByAdminId($user->id, $user->username);
            session(['siswa_id' => $siswa->idsiswa]);
        } else {
            session(['admin_id' => $user->id]);
        }

        return true;
    }

    public function register(array $data)
    {
        $adminId = $this->adminRepo->create($data);

        if ($data['role'] == 'guru') {
            $this->guruRepo->findOrCreateByAdminId($adminId, $data['username']);
        } elseif ($data['role'] == 'siswa') {
            $this->siswaRepo->findOrCreateByAdminId($adminId, $data['username']);
        }

        return $adminId;
    }
}
