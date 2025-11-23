<?php

namespace App\Repositories;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminRepository
{
    public function findByUsername(string $username)
    {
        return DB::table('dataadmin')->where('username', $username)->first();
    }

    public function create(array $data): int
    {
        return DB::table('dataadmin')->insertGetId([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
