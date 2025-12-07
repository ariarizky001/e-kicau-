<?php

namespace App\Http\Controllers;

class RekapLaporanController extends Controller
{
    public function index()
    {
        return view('rekap-laporan.index');
    }

    public function export()
    {
        // Logic untuk export laporan
        return redirect()->route('rekap-laporan.index')->with('success', 'Laporan berhasil diekspor');
    }
}

