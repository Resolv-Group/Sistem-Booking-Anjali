<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kolaborasi;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function TherapistListView()
    {
        $allKolaborasis = Kolaborasi::all();

        $terapis = Karyawan::where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->with([
                'kolaborasi',
                'layanans',
                'sessions' => function ($q) {
                    $q->where('tanggal_sesi', '>=', now()->toDateString())
                        ->where('status', 'terbuka') // Match your screenshot 'terbuka'
                        ->with('bookings.bookingPatients');
                },
            ])
            ->get();

        $uniqueCities = Kolaborasi::pluck('kota_kolaborasi')->unique()->sort()->values();

        return view('pages.therapist.patient', compact('terapis', 'allKolaborasis', 'uniqueCities'));
    }
}
