<?php

namespace App\Http\Controllers;
use App\Models\siswa;
use Illuminate\Http\Request;

class siswaController extends Controller
{
    //
    public function home()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('login');
        }
        $siswa = siswa::all();
        return view('home', compact('siswa'));
    }
    public function siswaDashboard()
    {
        if (!session()->has('admin_id') || session('admin_role') !== 'siswa') {
            return redirect()->route('login');
        }
        return view('siswa.dashboard');
    }
    
    public function create()
    {
        return view('siswa.create');
    }
    public function store(Request $request)
    {
        siswa::create($request->only('nama', 'tb', 'bb'));
        return redirect()->route('home');
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
        return redirect()->route('home');
    }
    public function destroy($id)
    {
        $siswa = siswa::findOrFail($id);
        $siswa->delete();
        return redirect()->route('home');
    }

}
