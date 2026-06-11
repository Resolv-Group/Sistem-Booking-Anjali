<?php

namespace App\Http\Controllers;

use App\Models\Kolaborasi;

use Illuminate\Http\Request;

class KolaborasiController extends Controller
{
    //

    public function index()
    {
        $cabangs = Kolaborasi::all();

        return view('pages.cabang.index', compact('cabangs'));
    }

    public function create()
    {
        return view('pages.cabang.cabang-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kolaborasi' => 'required|string|max:255',
            'alamat_kolaborasi' => 'nullable|string',
            'kota_kolaborasi' => 'nullable|string|max:100',
            'no_telp_kolaborasi' => 'nullable|string|max:50',
            'email_kolaborasi' => 'nullable|email|max:100',
            'homecare_harga' => 'required|numeric|min:0',
        ]);

        foreach (['alamat_kolaborasi', 'kota_kolaborasi', 'no_telp_kolaborasi', 'email_kolaborasi'] as $field) {
            $validated[$field] = $validated[$field] ?? '';
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $kolaborasi = Kolaborasi::create($validated);

        return redirect()->route('admin-global.cabang.menu', $kolaborasi->id)
            ->with('success', 'Cabang baru berhasil ditambahkan.');
    }

    public function menuIndex($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        $layananCount = \App\Models\Layanan::where('kolaborasi_id', $id_kolaborasi)
            ->where('status', 'Tersedia')
            ->count();

        $terapisCount = \App\Models\Karyawan::where('kolaborasi_id', $id_kolaborasi)
            ->where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->count();

        return view('pages.cabang.menu', compact('kolaborasi', 'layananCount', 'terapisCount'));
    }

    public function edit($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        return view('pages.cabang.edit', compact('kolaborasi'));
    }

    public function update(Request $request, $id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        
        $validated = $request->validate([
            'nama_kolaborasi' => 'required|string|max:255',
            'alamat_kolaborasi' => 'nullable|string',
            'kota_kolaborasi' => 'nullable|string|max:100',
            'no_telp_kolaborasi' => 'nullable|string|max:50',
            'email_kolaborasi' => 'nullable|email|max:100',
            'homecare_harga' => 'required|numeric|min:0',
        ]);

        $kolaborasi->update($validated);

        return redirect()->route('admin-global.cabang.menu', $kolaborasi->id)
            ->with('success', 'Pengaturan cabang berhasil diperbarui.');
    }
}
