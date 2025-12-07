<?php

namespace App\Http\Controllers;

class PengaturanEventController extends Controller
{
    public function index()
    {
        return view('pengaturan-event.index');
    }

    public function update()
    {
        return redirect()->route('pengaturan-event.index')->with('success', 'Pengaturan event berhasil diupdate');
    }
}

