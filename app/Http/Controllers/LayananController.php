<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kolaborasi;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Show the therapist list for assigning layanan.
     */
    public function index($id_kolaborasi)
    {
        $kolaborasiId = Kolaborasi::where('id', $id_kolaborasi)->first();
        // Mengambil data terapis aktif dengan hitungan layanan yang mereka miliki
        $therapists = Karyawan::where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->where('kolaborasi_id', $id_kolaborasi)
            ->withCount('layanans') // Ini akan menghasilkan atribut 'layanans_count'
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->nama_karyawan,
                    'role' => 'Spesialis Akupunktur',
                    'rating' => $t->nilai_review ?? '5.0',
                    'reviews' => 120,
                    'count' => $t->layanans_count,
                    'status' => 'Tersedia hari ini',
                    'image' => $t->fotoUrlOrDefault(),
                ];
            })
            ->values();

        return view('pages.cabang.menu.assign-layanan.therapist-list', compact('therapists', 'kolaborasiId'));
    }

    /**
     * Show the assign layanan page for a specific therapist.
     */
    public function assignLayanan($id_kolaborasi, $id_karyawan)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        $karyawan = Karyawan::findOrFail($id_karyawan);

        $activeLayananIds = $karyawan->layanans()->pluck('layanan.id')->toArray();

        $layanans = Layanan::where('kolaborasi_id', $id_kolaborasi)
            ->where('status', 'Tersedia')
            ->get()
            ->map(function (Layanan $layanan) {
                return [
                    'id' => $layanan->id,
                    'nama_layanan' => $layanan->nama,
                    'harga' => number_format($layanan->base_harga, 0, ',', '.'),
                    'deskripsi' => $layanan->deskripsi,
                    'durasi' => '-',
                    'status' => $layanan->status,
                ];
            });

        // Pass $karyawan as $therapist so the view can display the profile
        $therapist = $karyawan;

        return view('pages.cabang.menu.assign-layanan.assign-layanan', compact(
            'layanans',
            'kolaborasi',
            'karyawan',
            'activeLayananIds',
            'therapist'
        ));
    }

    /**
     * Sync layanan assignments for a therapist.
     */
    public function assignLayananStore(Request $request, $id_kolaborasi, $id_karyawan)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        $karyawan = Karyawan::findOrFail($id_karyawan);

        // Validate the incoming layanan_ids
        $validated = $request->validate([
            'layanan_ids' => 'nullable|array',
            'layanan_ids.*' => 'exists:layanan,id',
        ]);

        // Sync the selected layanan to the therapist (pivot table)
        $karyawan->layanans()->sync($validated['layanan_ids'] ?? []);

        return redirect()
            ->route('admin-global.assign-layanan', [$id_kolaborasi, $id_karyawan])
            ->with('success', 'Layanan berhasil diperbarui!');
    }

    // ─── LAYANAN CRUD ─────────────────────────────────────────────

    /**
     * Show the layanan list for a specific kolaborasi.
     */
    public function layananIndex($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        $layanans = Layanan::where('kolaborasi_id', $id_kolaborasi)
            ->orderBy('nama')
            ->get();

        return view('pages.cabang.menu.layanan.layanan-menu', compact('kolaborasi', 'layanans'));
    }

    /**
     * Show the create layanan form.
     */
    public function layananCreate($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        return view('pages.cabang.menu.layanan.layanan-create', compact('kolaborasi'));
    }

    /**
     * Store a new layanan.
     */
    public function layananStore(Request $request, $id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'base_harga' => 'required|numeric|min:0',
            'homecare_harga' => 'nullable|numeric|min:0',
            'diskon_persentase' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        $validated['kolaborasi_id'] = $kolaborasi->id;
        $validated['created_by'] = auth()->id();

        Layanan::create($validated);

        return redirect()
            ->route('admin-global.layanan', $id_kolaborasi)
            ->with('success', 'Layanan berhasil ditambahkan!');
    }

    /**
     * Show the detail / edit layanan form.
     */
    public function layananDetail($id_kolaborasi, $id_layanan)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        $layanan = Layanan::where('kolaborasi_id', $id_kolaborasi)
            ->findOrFail($id_layanan);

        return view('pages.cabang.menu.layanan.layanan-detail', compact('kolaborasi', 'layanan'));
    }

    /**
     * Update an existing layanan.
     */
    public function layananUpdate(Request $request, $id_kolaborasi, $id_layanan)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        $layanan = Layanan::where('kolaborasi_id', $id_kolaborasi)
            ->findOrFail($id_layanan);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'base_harga' => 'required|numeric|min:0',
            'homecare_harga' => 'nullable|numeric|min:0',
            'diskon_persentase' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        $validated['updated_by'] = auth()->id();

        $layanan->update($validated);

        return redirect()
            ->route('admin-global.layanan', $id_kolaborasi)
            ->with('success', 'Layanan berhasil diperbarui!');
    }

    /**
     * Delete a layanan.
     */
    public function layananDestroy($id_kolaborasi, $id_layanan)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        $layanan = Layanan::where('kolaborasi_id', $id_kolaborasi)
            ->findOrFail($id_layanan);

        $layanan->delete();

        return redirect()
            ->route('admin-global.layanan', $id_kolaborasi)
            ->with('success', 'Layanan berhasil dihapus!');
    }
}
