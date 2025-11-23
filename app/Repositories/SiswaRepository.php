<?php
namespace App\Repositories;

use App\Models\Siswa;
use App\Models\Admin;

class SiswaRepository
{
    public function create(array $data)
    {
        $admin = Admin::create([
            'username' => $data['nama'],
            'password' => bcrypt($data['nama']), // Using name as a temporary password
            'role' => 'siswa',
        ]);

        $siswa = Siswa::create([
            'admin_id' => $admin->id,
            'nama' => $data['nama'],
            'tb' => $data['tb'],
            'bb' => $data['bb'],
        ]);

        return $siswa;
    }

    public function update(Siswa $siswa, array $data)
    {
        $siswa->update($data);
        return $siswa;
    }

    public function delete(Siswa $siswa)
    {
        $siswa->delete();
    }

    public function findOrCreateByAdminId(int $adminId, string $username): Siswa
    {
        $siswa = Siswa::where('admin_id', $adminId)->first();

        if (!$siswa) {
            $siswa = Siswa::create([
                'admin_id' => $adminId,
                'nama' => $username,
                'tb' => 0,
                'bb' => 0,
            ]);
        }

        return $siswa;
    }
}
