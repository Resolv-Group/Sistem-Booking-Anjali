<?php

namespace App\Http\Controllers;

use App\Models\Kolaborasi;

class KolaborasiController extends Controller
{
    //

    public function index()
    {
        $cabangs = Kolaborasi::all();

        return view('pages.cabang.index', compact('cabangs'));
    }

    public function menuIndex($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::where('id', $id_kolaborasi)->first();

        return view('pages.cabang.menu', compact('kolaborasi'));
    }
}
