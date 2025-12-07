<?php

namespace App\Http\Controllers;

class FormNominasiController extends Controller
{
    public function index()
    {
        return view('form-nominasi.index');
    }

    public function create()
    {
        return view('form-nominasi.create');
    }

    public function store()
    {
        return redirect()->route('form-nominasi.index')->with('success', 'Nominasi berhasil ditambahkan');
    }
}

