<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KbmService;

class KbmController extends Controller
{
    protected $kbmService;

    public function __construct(KbmService $kbmService)
    {
        $this->kbmService = $kbmService;
    }
    //
    public function index(Request $request)
    {
        $role = session('admin_role');
        $with = ['guru', 'walas'];
        
        // Admin bisa memilih tampilan (guru/siswa)
        if ($role === 'admin') {
            $adminView = $request->get('view', 'guru');
            if ($adminView === 'guru') {
                // Butuh daftar murid: walas -> kelas -> siswa
                $with = ['guru', 'walas.kelas.siswa'];
            } else {
                $with = ['guru', 'walas'];
            }
        }

        // Mulai query dasar
        $query = \App\Models\Kbm::query()->with($with);

        // Jika role adalah guru, filter berdasarkan guru yang sedang login
        if ($role === 'guru') {
            $guru = \App\Models\Guru::where('id', session('admin_id'))->first();
            if ($guru) {
                $query->where('idguru', $guru->idguru);
            } else {
                // Jika data guru tidak ditemukan, kosongkan hasil agar aman
                $query->whereRaw('1 = 0');
            }
        }
        // Jika role adalah siswa, filter berdasarkan kelas (idwalas) siswa yang login
        elseif ($role === 'siswa') {
            $siswa = \App\Models\Siswa::where('admin_id', session('admin_id'))->first();
            if ($siswa) {
                $kelas = \App\Models\Kelas::where('idsiswa', $siswa->idsiswa)->first();
                if ($kelas) {
                    $query->where('idwalas', $kelas->idwalas);
                } else {
                    // Tidak terdaftar pada kelas manapun
                    $query->whereRaw('1 = 0');
                }
            } else {
                // Data siswa tidak ditemukan
                $query->whereRaw('1 = 0');
            }
        } elseif ($role === 'admin') {
            // Admin: hanya dukung filter Hari (tampilan dipilih via query 'view')
            if ($request->filled('hari')) {
                $query->where('hari', $request->get('hari'));
            }
        }

        $jadwals = $query->get();
        return view('jadwal.index', [
            'jadwals' => $jadwals,
            'adminView' => ($role === 'admin') ? $request->get('view', 'guru') : null,
        ]);
    }

    public function jadwalGuru($idguru)
    {
        $guru = \App\Models\Guru::with(['kbm.walas', 'kbm.kelas'])->findOrFail($idguru);
        return view('jadwal.guru', compact('guru'));
    }

    public function jadwalKelas($idwalas)
    {
        $kelas = \App\Models\Kelas::with(['kbm.guru'])->findOrFail($idwalas);
        return view('jadwal.kelas', compact('kelas'));
    }

    public function getJadwal(Request $request)
    {
        $jadwals = $this->kbmService->getScheduleForAjax($request);
        return response()->json($jadwals);
    }

}