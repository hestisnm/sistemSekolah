<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Services\SiswaService;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    protected $service;

    public function __construct(SiswaService $service)
    {
        $this->service = $service;
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

    public function store(StoreSiswaRequest $request)
    {
        $this->service->createSiswa($request->validated());
        return redirect()->route('home')->with('success', 'Data siswa berhasil ditambahkan!');
    }


    public function edit($id)
    {
        $siswa = siswa::findOrFail($id);
        return view('siswa.edit', compact('siswa'));
    }

    public function update(UpdateSiswaRequest $request, Siswa $siswa)
    {
        $this->service->updateSiswa($siswa, $request->validated());
        return redirect()->route('home')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Siswa $siswa)
    {
        $this->service->deleteSiswa($siswa);
        return redirect()->route('home')->with('success', 'Data siswa berhasil dihapus!');
    }

    public function getData()
    {
        $siswa = Siswa::all();
        return response()->json($siswa);
    }

    public function search(Request $request)
    {
        $keyword = strtolower($request->input('q'));
        $siswa = Siswa::whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"])
            ->get();
        return response()->json($siswa);
    }
}
