<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class adminController extends Controller
{
    public function index()
    {
        if (!session()->has('username')) {
            return redirect()->route('login');
        }

        $role = session('role');
        $data = null;
        $jadwals = collect();
return view('home', compact('data', 'jadwals'));


        // --------------------------
        // ROLE GURU
        // --------------------------
        if ($role == 'guru') {

            $guruId = session('guru_id');

            if ($guruId) {
                $data = DB::table('dataguru')->where('idguru', $guruId)->first();

                $jadwals = DB::table('datakbm')
                    ->join('dataguru', 'datakbm.idguru', '=', 'dataguru.idguru')
                    ->join('datawalas', 'datakbm.idwalas', '=', 'datawalas.idwalas')
                    ->select(
                        'datakbm.*',
                        'dataguru.nama as nama_guru',
                        'dataguru.mapel as mapel_guru',
                        'datawalas.nama_kelas as nama_kelas',
                        'datawalas.tahun_ajaran as tahun_ajaran'
                    )
                    ->where('datakbm.idguru', $guruId)
                    ->get();
            }

        }

        // --------------------------
        // ROLE SISWA
        // --------------------------
        elseif ($role == 'siswa') {

            $siswaId = session('siswa_id');

            if ($siswaId) {
                $data = DB::table('datasiswa')->where('idsiswa', $siswaId)->first();

                $kelas = DB::table('datakelas')->where('idsiswa', $siswaId)->first();

                if ($kelas) {
                    $jadwals = DB::table('datakbm')
                        ->join('dataguru', 'datakbm.idguru', '=', 'dataguru.idguru')
                        ->join('datawalas', 'datakbm.idwalas', '=', 'datawalas.idwalas')
                        ->select(
                            'datakbm.*',
                            'dataguru.nama as nama_guru',
                            'dataguru.mapel as mapel_guru',
                            'datawalas.nama_kelas as nama_kelas',
                            'datawalas.tahun_ajaran as tahun_ajaran'
                        )
                        ->where('datakbm.idwalas', $kelas->idwalas)
                        ->get();
                }
            }

        }

        // --------------------------
        // ROLE ADMIN (INI YANG PENTING)
        // --------------------------
        else {

            // Admin lihat semua siswa
            $daftarSiswa = DB::table('datasiswa')->get();

            // Admin lihat semua jadwal
            $jadwals = DB::table('datakbm')
                ->join('dataguru', 'datakbm.idguru', '=', 'dataguru.idguru')
                ->join('datawalas', 'datakbm.idwalas', '=', 'datawalas.idwalas')
                ->select(
                    'datakbm.*',
                    'dataguru.nama as nama_guru',
                    'dataguru.mapel as mapel_guru',
                    'datawalas.nama_kelas as nama_kelas',
                    'datawalas.tahun_ajaran as tahun_ajaran'
                )
                ->get();
        }

        return view('home', compact('data', 'daftarSiswa', 'jadwals'));
    }



    // ========================================================
    // LOGIN
    // ========================================================
    public function formLogin()
    {
        return view('login');
    }


    public function loginPost(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = DB::table('dataadmin')
                ->where('username', $request->username)
                ->first();

            if (!$user) {
                return back()->with('error', 'Username tidak ditemukan!');
            }

            if (!Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Password salah!');
            }

            // --------------------------
            // LOGIN GURU
            // --------------------------
            if ($user->role == 'guru') {

                $guru = DB::table('dataguru')
                    ->where('admin_id', $user->id)
                    ->first();

                // Jika dataguru tidak ada → buat otomatis
                if (!$guru) {
                    DB::table('dataguru')->insert([
                        'admin_id' => $user->id,
                        'nama' => $user->username,
                        'mapel' => 'Umum',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $guru = DB::table('dataguru')
                        ->where('admin_id', $user->id)
                        ->first();
                }

                session([
                    'username' => $user->username,
                    'role' => 'guru',
                    'guru_id' => $guru->idguru
                ]);

                return redirect()->route('home');
            }

            // --------------------------
            // LOGIN SISWA
            // --------------------------
            elseif ($user->role == 'siswa') {

                $siswa = DB::table('datasiswa')
                    ->where('admin_id', $user->id)
                    ->first();

                // Jika data siswa tidak ada → buat otomatis
                if (!$siswa) {
                    DB::table('datasiswa')->insert([
                        'admin_id' => $user->id,
                        'nama' => $user->username,
                        'tb' => 0,
                        'bb' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $siswa = DB::table('datasiswa')
                        ->where('admin_id', $user->id)
                        ->first();
                }

                session([
                    'username' => $user->username,
                    'role' => 'siswa',
                    'siswa_id' => $siswa->idsiswa
                ]);

                return redirect()->route('home');
            }

            // --------------------------
            // LOGIN ADMIN
            // --------------------------
          else {
    // Untuk role admin
    session([
        'username' => $user->username,
        'role' => 'admin',
        'admin_id' => $user->id     // ⬅ WAJIB ADA!
    ]);
    return redirect()->route('home');
}


         

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }



    // ========================================================
    // LOGOUT
    // ========================================================
    public function logout()
    {
        session()->flush();
        return redirect()->route('landing');
    }



    // ========================================================
    // REGISTER
    // ========================================================
    public function formRegister()
    {
        return view('register');
    }



    public function prosesRegister(Request $request)
    {
        try {

            $request->validate([
                'username' => 'required|unique:dataadmin,username',
                'password' => 'required|min:8',
                'role' => 'required|in:admin,guru,siswa'
            ]);

            // Simpan ke dataadmin → dapat admin_id
            $adminId = DB::table('dataadmin')->insertGetId([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Buat data guru/siswa sesuai role
            if ($request->role == 'guru') {
                DB::table('dataguru')->insert([
                    'admin_id' => $adminId,
                    'nama' => $request->username,
                    'mapel' => 'Umum'
                ]);
            } elseif ($request->role == 'siswa') {
                DB::table('datasiswa')->insert([
                    'admin_id' => $adminId,
                    'nama' => $request->username,
                    'tb' => 0,
                    'bb' => 0
                ]);
            }

            return back()->with('error', 'Registrasi berhasil!');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
