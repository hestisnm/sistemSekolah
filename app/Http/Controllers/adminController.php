<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class adminController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
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


    public function loginPost(LoginRequest $request)
    {
        try {
            $this->authService->login($request->validated());
            return redirect()->route('home');
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



    public function prosesRegister(RegisterRequest $request)
    {
        try {
            $this->authService->register($request->validated());
            return back()->with('success', 'Registrasi berhasil!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
