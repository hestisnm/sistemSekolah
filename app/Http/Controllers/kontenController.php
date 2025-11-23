<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\KontenRepository;

class kontenController extends Controller
{
    protected $kontenRepo;

    public function __construct(KontenRepository $kontenRepo)
    {
        $this->kontenRepo = $kontenRepo;
    }
    //
    public function landing()
    {
        $konten = $this->kontenRepo->getAll();
        return view('landing', compact('konten'));
    }
    public function detil($id)
    {
        $datakonten = $this->kontenRepo->findById($id);
        return view('detil', compact('datakonten'));
    }

}
