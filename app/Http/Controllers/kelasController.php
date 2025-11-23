<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'idwalas' => 'required|exists:datawalas,idwalas',
            'idsiswa' => 'required|exists:datasiswa,idsiswa',
        ]);

        // Get the wali kelas data
        $waliKelas = DB::table('datawalas')
            ->join('dataguru', 'datawalas.idguru', '=', 'dataguru.idguru')
            ->where('datawalas.idwalas', $request->idwalas)
            ->first();

        if (!$waliKelas) {
            return back()->with('error', 'Data wali kelas tidak ditemukan!');
        }

        // Check if the student is already in a class
        $existingKelas = Kelas::where('idsiswa', $request->idsiswa)->first();
        if ($existingKelas) {
            return back()->with('error', 'Siswa sudah terdaftar di kelas ' . $existingKelas->namakelas);
        }

        try {
            // Create new class record
            Kelas::create([
                'idwalas' => $request->idwalas,
                'idsiswa' => $request->idsiswa,
                'namakelas' => $waliKelas->namakelas, // Make sure this matches your database column name
            ]);

            return back()->with('success', 'Siswa berhasil ditambahkan ke kelas ' . $waliKelas->namakelas);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
