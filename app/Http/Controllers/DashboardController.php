<?php

namespace App\Http\Controllers;

use App\Models\KelasLomba;
use App\Models\Peserta;
use App\Models\User;
use App\Models\Juri;

class DashboardController extends Controller
{
    public function index()
    {
        $kelasLomba = KelasLomba::withCount('peserta')
            ->orderBy('id', 'asc')
            ->get();

        $totalPeserta = Peserta::count();
        $totalJuri = Juri::count();
        $totalUser = User::count();

        return view('dashboard', compact('kelasLomba', 'totalPeserta', 'totalJuri', 'totalUser'));
    }
}

