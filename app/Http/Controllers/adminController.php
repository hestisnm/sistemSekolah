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
        $role = session('role');
        $data = null;
        $jadwals = collect(); // biar tidak error kalau belum ada

    if ($role == 'guru') {
        $guruId = session('guru_id');
        if ($guruId) {
            $data = DB::table('dataguru')->where('idguru', $guruId)->first();

            // 🔹 Ambil jadwal yang sesuai guru login
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

    } elseif ($role == 'siswa') {
        $siswaId = session('siswa_id');
        if ($siswaId) {
            $data = DB::table('datasiswa')->where('idsiswa', $siswaId)->first();

            // 🔹 Cari kelas siswa lewat tabel datakelas
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

    } else {
        // 🔹 Admin lihat semua jadwal
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

    $daftarSiswa = DB::table('datasiswa')->get();

        return view('home', compact('data', 'daftarSiswa', 'jadwals'));
    }



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

            $username = $request->username;
            $password = $request->password;
            
            // Cek di tabel dataadmin
            $user = DB::table('dataadmin')
                    ->where('username', $username)
                    ->first();
            
            if (!$user) {
                return redirect()->back()->with('error', 'Username tidak ditemukan!');
            }

            if (!Hash::check($password, $user->password)) {
                return redirect()->back()->with('error', 'Password salah!');
            }
            
            // Jika role guru
            if ($user->role == 'guru') {
                $guru = DB::table('dataguru')
                        ->where('idguru', $user->id)  // Menggunakan idguru sebagai kunci relasi
                        ->first();
                
                if (!$guru) {
                    // Jika data guru tidak ditemukan, buat data guru baru
                    $guruData = [
                        'idguru' => $user->id,
                        'admin_id' => $user->id,  // Menambahkan admin_id yang diperlukan
                        'nama' => $user->username,
                        'mapel' => 'Umum',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    
                    try {
                        DB::table('dataguru')->insert($guruData);
                    } catch (\Exception $e) {
                        Log::error('Gagal menyimpan data guru: ' . $e->getMessage());
                        return redirect()->back()->with('error', 'Gagal membuat data guru baru: ' . $e->getMessage());
                    }
                    
                    session([
                        'username' => $user->username,
                        'role' => 'guru',
                        'guru_id' => $user->id
                    ]);
                } else {
                    session([
                        'username' => $user->username,
                        'role' => 'guru',
                        'guru_id' => $guru->idguru
                    ]);
                }
                return redirect()->route('home');
            } 
            // Jika role siswa
            elseif ($user->role == 'siswa') {
                $siswa = DB::table('datasiswa')
                         ->where('idsiswa', $user->id)  // Menggunakan idsiswa sebagai kunci relasi
                         ->first();
                
                if (!$siswa) {
                    // Jika data siswa tidak ditemukan, buat data siswa baru
                    $siswaData = [
                        'idsiswa' => $user->id,
                        'nama' => $user->username,
                        'tb' => 0,
                        'bb' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    
                    DB::table('datasiswa')->insert($siswaData);
                    
                    session([
                        'username' => $user->username,
                        'role' => 'siswa',
                        'siswa_id' => $user->id
                    ]);
                } else {
                    session([
                        'username' => $user->username,
                        'role' => 'siswa',
                        'siswa_id' => $siswa->idsiswa
                    ]);
                }
                return redirect()->route('home');
            } else {
                // Untuk role admin
                session([
                    'username' => $user->username,
                    'role' => 'admin'
                ]);
                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        session()->forget(['username', 'role']);
        return redirect()->route('landing');
    }

    public function formRegister()
    {
        return view('register');
    }

    public function prosesRegister(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:50|unique:dataadmin,username',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:admin,guru,siswa',
            ]);
            
            // Insert ke tabel dataadmin
            $userId = DB::table('dataadmin')->insertGetId([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Jika role guru, buat juga di tabel dataguru
            if ($request->role == 'guru') {
                DB::table('dataguru')->insert([
                    'id' => $userId,
                    'nama' => $request->username, // Default nama sama dengan username
                    'mapel' => 'Umum', // Default mapel
                ]);
            }
            // Jika role siswa, buat juga di tabel datasiswa
            elseif ($request->role == 'siswa') {
                DB::table('datasiswa')->insert([
                    'id' => $userId,
                    'nama' => $request->username, // Default nama sama dengan username
                    'tb' => 0, // Default tinggi badan
                    'bb' => 0, // Default berat badan
                ]);
            }
            return redirect()->back()->with('error', 'Registrasi berhasil!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Registrasi gagal: ' . $e->getMessage());
        }
    }


}


