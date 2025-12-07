<?php

namespace App\Http\Controllers;

use App\Models\Juri;
use Illuminate\Http\Request;

class JuriController extends Controller
{
    public function index()
    {
        $juris = Juri::orderBy('id', 'asc')->paginate(15);
        return view('juri.index', compact('juris'));
    }

    public function create()
    {
        return view('juri.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_juri' => 'required|string|max:255',
        ], [
            'nama_juri.required' => 'Nama juri harus diisi',
        ]);

        Juri::create($validated);

        return redirect()->route('juri.index')->with('success', 'Juri berhasil ditambahkan');
    }

    public function edit(Juri $juri)
    {
        return view('juri.edit', compact('juri'));
    }

    public function update(Request $request, Juri $juri)
    {
        $validated = $request->validate([
            'nama_juri' => 'required|string|max:255',
        ], [
            'nama_juri.required' => 'Nama juri harus diisi',
        ]);

        $juri->update($validated);

        return redirect()->route('juri.index')->with('success', 'Juri berhasil diupdate');
    }

    public function destroy(Juri $juri)
    {
        $juri->delete();

        return redirect()->route('juri.index')->with('success', 'Juri berhasil dihapus');
    }

    /**
     * Store juri via inline form (AJAX)
     */
    public function storeInline(Request $request)
    {
        $validated = $request->validate([
            'nama_juri' => 'required|string|max:255',
        ], [
            'nama_juri.required' => 'Nama juri harus diisi',
        ]);

        $juri = Juri::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Juri berhasil ditambahkan',
            'data' => $juri,
        ], 201);
    }
}
