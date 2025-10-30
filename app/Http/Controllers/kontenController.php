<?php

namespace App\Http\Controllers;
use App\Models\konten;

use Illuminate\Http\Request;

class kontenController extends Controller
{
    //
    public function landing()
    {
        $konten = konten::all();
        return view('landing', compact('konten'));
    }
    public function detil($id)
    {
        $datakonten = konten::findOrFail($id);
        return view('detil', compact('datakonten'));
    }

}
