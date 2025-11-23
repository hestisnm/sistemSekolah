<?php

namespace App\Http\Controllers;

use App\Models\siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class siswaController extends Controller
{
    

    // ========================
    //  HALAMAN HOME SISWA
    
    // ========================
    public function home()
    {
        $idSiswa = session('siswa_id');

        $siswa = siswa::find($idSiswa);

        $dataKbm = DB::table('datakbm')
            ->join('dataguru', 'datakbm.idguru', '=', 'dataguru.idguru')
            ->select(
                'dataguru.namaguru',
                'dataguru.mapel',
                'datakbm.hari',
                'datakbm.mulai',
                'datakbm.selesai'
            )
            ->where('datakbm.idwalas', '=', $siswa->idwalas)
            ->orderBy('datakbm.hari')
            ->get();

        return view('home', compact('dataKbm'));
    }


    // ========================
    //  CRUD (KHUSUS ADMIN)
    // ========================

    public function index()
    {
        $siswa = siswa::all();
        return view('siswa.index', compact('siswa'));
    }

    public function create()
    {
        return view('siswa.create');
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'nama' => 'required',
        'tb' => 'required|numeric',
        'bb' => 'required|numeric',
    ]);

    $validated['admin_id'] = session('admin_id'); // ← tambahkan ini

    return redirect()->route('home')->with('success', 'Data berhasil ditambahkan');
}


    public function edit($id)
    {
        $siswa = siswa::findOrFail($id);
        return view('siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $siswa = siswa::findOrFail($id);
        $siswa->update($request->only('nama', 'tb', 'bb'));
        return redirect()->route('siswa.index');
    }

    public function destroy($id)
    {
        $siswa = siswa::findOrFail($id);
        $siswa->delete();
        return redirect()->route('siswa.index');
    }
}
