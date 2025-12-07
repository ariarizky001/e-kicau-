<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\KelasLomba;
use App\Models\GridPesertaConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $query = Peserta::with('kelasLomba');

        // Filter by kelas lomba
        if ($request->has('kelas_lomba_id') && $request->kelas_lomba_id) {
            $query->where('kelas_lomba_id', $request->kelas_lomba_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('nama_burung', 'like', "%{$search}%")
                  ->orWhere('alamat_team', 'like', "%{$search}%");
            });
        }

        $peserta = $query->orderBy('nomor_urut')->paginate(25);
        $kelasLomba = KelasLomba::aktif()->orderBy('nomor_kelas')->get();

        return view('peserta.index', compact('peserta', 'kelasLomba'));
    }

    public function create()
    {
        $kelasLomba = KelasLomba::aktif()->withCount('peserta')->orderBy('nomor_kelas')->get();
        return view('peserta.create', compact('kelasLomba'));
    }

    public function store(Request $request)
    {
        Log::info('PesertaController@store called', [
            'method' => $request->method(),
            'wantsJson' => $request->wantsJson(),
            'all' => $request->all(),
        ]);

        $validated = $request->validate([
            'kelas_lomba_id' => 'required|exists:kelas_lomba,id',
            'nomor_urut' => 'nullable|integer|min:1',
            'nama_pemilik' => 'required|string|max:255',
            'nama_burung' => 'required|string|max:255',
            'alamat_team' => 'nullable|string|max:255',
            'nomor_gantangan' => 'nullable|string|max:50',
        ]);

        Log::info('Validation passed', ['validated' => $validated]);

        // Check batas peserta
        $kelasLomba = KelasLomba::withCount('peserta')->findOrFail($request->kelas_lomba_id);
        if ($kelasLomba->isFull()) {
            $errorMsg = "Kelas lomba ini sudah mencapai batas maksimal peserta ({$kelasLomba->batas_peserta} peserta). Saat ini sudah ada {$kelasLomba->peserta_count} peserta.";

            Log::warning('Kelas is full', ['errorMsg' => $errorMsg]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }

            return back()->withInput()->with('error', $errorMsg);
        }

        // Nomor urut akan di-generate otomatis oleh model boot method
        $peserta = Peserta::create($validated);

        Log::info('Peserta created successfully', ['peserta_id' => $peserta->id]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Peserta berhasil ditambahkan',
                'peserta' => $peserta
            ], 201);
        }

        return redirect()->route('peserta.index', ['kelas_lomba_id' => $request->kelas_lomba_id])
            ->with('success', 'Peserta berhasil ditambahkan');
    }

    public function edit(Peserta $peserta)
    {
        $kelasLomba = KelasLomba::aktif()->withCount('peserta')->orderBy('nomor_kelas')->get();
        return view('peserta.edit', compact('peserta', 'kelasLomba'));
    }

    public function update(Request $request, Peserta $peserta)
    {
        Log::info("UPDATE Request received for peserta ID: {$peserta->id}", [
            'body' => $request->all(),
            'current_nomor_urut' => $peserta->nomor_urut,
            'current_kelas_id' => $peserta->kelas_lomba_id,
            'nomor_urut_type' => gettype($request->input('nomor_urut')),
            'nomor_urut_value' => $request->input('nomor_urut'),
            'request_method' => $request->method()
        ]);

        // For PATCH request, use different validation (only validate provided fields)
        $rules = [
            'kelas_lomba_id' => 'nullable|exists:kelas_lomba,id',
            'nomor_urut' => 'nullable|numeric|min:1',
            'nama_pemilik' => 'nullable|string|max:255',
            'nama_burung' => 'nullable|string|max:255',
            'alamat_team' => 'nullable|string|max:255',
            'nomor_gantangan' => 'nullable|string|max:50',
        ];

        $validated = $request->validate($rules);

        Log::info("Validated data:", ['validated' => $validated, 'nomor_urut_in_validated' => $validated['nomor_urut'] ?? 'NOT SET']);

        // Filter hanya field yang diisi
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

        Log::info("Filtered data after array_filter:", ['validated' => $validated, 'nomor_urut_after_filter' => $validated['nomor_urut'] ?? 'NOT SET']);

        // Check if nomor_urut is unique within the same kelas (jika diubah)
        if (isset($validated['nomor_urut'])) {
            $newNomorUrut = $validated['nomor_urut'];
            $oldNomorUrut = $peserta->nomor_urut;

            Log::info("Attempting to change nomor_urut from {$oldNomorUrut} to {$newNomorUrut} for peserta ID {$peserta->id}");

            $existing = Peserta::where('kelas_lomba_id', $peserta->kelas_lomba_id)
                ->where('nomor_urut', $newNomorUrut)
                ->where('id', '!=', $peserta->id)
                ->first();

            if ($existing) {
                Log::warning("Target nomor_urut {$newNomorUrut} already occupied by peserta ID {$existing->id}");
                $message = 'Nomor urut sudah digunakan di kelas lomba ini';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->withInput()->with('error', $message);
            }
        } else {
            Log::warning("nomor_urut NOT in validated data after filter");
        }

        Log::info("Before update - Peserta state:", $peserta->toArray());

        if (!empty($validated)) {
            $peserta->update($validated);
            Log::info("After update executed successfully");
        } else {
            Log::warning("Validated data is empty, skipping update");
        }

        // Refresh model from database
        $peserta->refresh();
        Log::info("After refresh - Peserta state:", $peserta->toArray());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data peserta berhasil diubah',
                'peserta' => [
                    'id' => $peserta->id,
                    'nomor_urut' => $peserta->nomor_urut,
                    'kelas_lomba_id' => $peserta->kelas_lomba_id,
                    'nama_pemilik' => $peserta->nama_pemilik,
                    'nama_burung' => $peserta->nama_burung,
                    'alamat_team' => $peserta->alamat_team,
                    'nomor_gantangan' => $peserta->nomor_gantangan
                ],
                'debug' => [
                    'validated_data' => $validated,
                    'nomor_urut_in_db' => $peserta->nomor_urut,
                    'id' => $peserta->id
                ]
            ], 200);
        }

        return redirect()->route('peserta.index', ['kelas_lomba_id' => $peserta->kelas_lomba_id])
            ->with('success', 'Peserta berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $peserta = Peserta::findOrFail($id);

            $kelasLombaId = $peserta->kelas_lomba_id;
            $namaPemilik = $peserta->nama_pemilik ?? 'Unknown';
            $namaBurung = $peserta->nama_burung ?? 'Unknown';
            $nomorGantangan = $peserta->nomor_gantangan;

            Log::info("Starting delete for peserta ID: $id, Nama: $namaPemilik");

            // Instead of deleting, clear the peserta data but keep the slot (grid)
            $peserta->update([
                'nama_pemilik' => '',
                'nama_burung' => '',
                'alamat_team' => '',
                // Keep nomor_gantangan agar grid tetap ada
                // Keep nomor_urut untuk maintain grid structure
            ]);

            Log::info("Peserta ID: $id cleared successfully");

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Peserta '{$namaPemilik}' (Gantangan: {$nomorGantangan}) berhasil dihapus, grid tetap tersimpan",
                    'peserta_id' => $id
                ]);
            }

            return redirect()->route('peserta.index', ['kelas_lomba_id' => $kelasLombaId])
                ->with('success', "Peserta '{$namaPemilik}' berhasil dihapus, grid tetap tersimpan");
        } catch (\Exception $e) {
            Log::error("Error deleting peserta: " . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal menghapus peserta: ' . $e->getMessage());
        }
    }


    /**
     * Show grid-based input form for peserta
     */
    public function showGrid(KelasLomba $kelasLomba)
    {
        // Get or create default grid config
        $gridConfig = GridPesertaConfig::firstOrCreate(
            ['kelas_lomba_id' => $kelasLomba->id],
            ['rows' => 4, 'columns' => 4]
        );

        // Get existing peserta
        $existingPeserta = Peserta::where('kelas_lomba_id', $kelasLomba->id)
            ->orderBy('nomor_urut')
            ->get();

        // Create empty grid slots
        $totalSlots = $gridConfig->rows * $gridConfig->columns;
        $gridData = [];

        // Fill grid with existing data
        foreach ($existingPeserta as $p) {
            if ($p->nomor_urut <= $totalSlots) {
                $gridData[$p->nomor_urut - 1] = $p;
            }
        }

        // Fill remaining slots with empty objects
        for ($i = 0; $i < $totalSlots; $i++) {
            if (!isset($gridData[$i])) {
                $gridData[$i] = (object)[
                    'id' => null,
                    'nomor_urut' => $i + 1,
                    'nama_pemilik' => '',
                    'nama_burung' => '',
                    'alamat_team' => '',
                    'nomor_gantangan' => ''
                ];
            }
        }

        return view('peserta.grid', compact('kelasLomba', 'gridConfig', 'gridData'));
    }

    /**
     * Store grid-based peserta input
     */
    public function storeGrid(Request $request, KelasLomba $kelasLomba)
    {
        $gridConfig = GridPesertaConfig::where('kelas_lomba_id', $kelasLomba->id)->firstOrFail();
        $totalSlots = $gridConfig->rows * $gridConfig->columns;

        // Validate grid data - expecting slots[1..n][field] format
        $rules = [];
        for ($slot = 1; $slot <= $totalSlots; $slot++) {
            $rules["slots.{$slot}.nama_pemilik"] = 'nullable|string|max:255';
            $rules["slots.{$slot}.nama_burung"] = 'nullable|string|max:255';
            $rules["slots.{$slot}.alamat_team"] = 'nullable|string|max:255';
            $rules["slots.{$slot}.nomor_gantangan"] = 'nullable|string|max:50';
            $rules["slots.{$slot}.nomor_urut"] = 'required|integer';
        }

        $validated = $request->validate($rules);

        try {
            // Collect data that will be saved
            // IMPORTANT: Keep nomor_urut same as slot number to maintain grid structure
            $pesertaData = [];

            foreach ($validated['slots'] ?? [] as $slot => $item) {
                // Save ALL slots, but fill with empty if not provided
                $pesertaData[] = [
                    'kelas_lomba_id' => $kelasLomba->id,
                    'nomor_urut' => (int)$slot,  // Keep slot number as nomor_urut
                    'nama_pemilik' => trim($item['nama_pemilik'] ?? ''),
                    'nama_burung' => trim($item['nama_burung'] ?? ''),
                    'alamat_team' => trim($item['alamat_team'] ?? ''),
                    'nomor_gantangan' => trim($item['nomor_gantangan'] ?? $slot),
                ];
            }

            // Validate duplicate nomor_gantangan only for filled slots
            $filledSlots = array_filter($pesertaData, function($p) {
                return !empty($p['nama_pemilik']) || !empty($p['nama_burung']);
            });

            $nomor_gantangan_list = array_column($filledSlots, 'nomor_gantangan');
            $nomor_gantangan_list = array_filter($nomor_gantangan_list);

            if (count($nomor_gantangan_list) !== count(array_unique($nomor_gantangan_list))) {
                $message = 'Nomor gantangan tidak boleh ada yang duplikat dalam satu kelas!';

                if ($request->wantsJson()) {
                    return response()->json(['error' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // Validate duplicate nama_burung within the same kelas (only for filled slots)
            $nama_burung_list = array_column($filledSlots, 'nama_burung');
            $nama_burung_list = array_filter($nama_burung_list);

            if (count($nama_burung_list) !== count(array_unique($nama_burung_list))) {
                $message = 'Nama burung tidak boleh ada yang duplikat dalam satu kelas!';

                if ($request->wantsJson()) {
                    return response()->json(['error' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // Delete existing peserta for this kelas
            Peserta::where('kelas_lomba_id', $kelasLomba->id)->delete();

            // Reset auto increment untuk table peserta jika semua data sudah kosong
            $totalPesertaRemaining = Peserta::count();
            if ($totalPesertaRemaining == 0) {
                // Reset auto increment ke 1
                DB::statement('ALTER TABLE peserta AUTO_INCREMENT = 1');
            }

            // Insert all slots (including empty ones to maintain grid structure)
            $totalInserted = count($filledSlots);  // Count only filled slots for message
            foreach ($pesertaData as $data) {
                Peserta::create($data);
            }

            $successMessage = "Grid peserta berhasil disimpan! ($totalInserted peserta terdaftar)";

            // Return JSON for AJAX requests
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'total_inserted' => $totalInserted,
                    'status' => 'terisi'
                ], 200);
            }

            return redirect()->route('peserta.index', ['kelas_lomba_id' => $kelasLomba->id])
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            Log::error('storeGrid error: ' . $errorMessage);

            // Return JSON for AJAX requests
            if ($request->wantsJson()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Show settings page for grid configuration
     */
    public function gridSettings(KelasLomba $kelasLomba)
    {
        $gridConfig = GridPesertaConfig::firstOrCreate(
            ['kelas_lomba_id' => $kelasLomba->id],
            ['rows' => 4, 'columns' => 4]
        );

        // Get other kelas lomba for copy feature
        $otherKelasLomba = KelasLomba::where('id', '!=', $kelasLomba->id)
            ->where('status', 'aktif')
            ->withCount('peserta')
            ->orderBy('nomor_kelas')
            ->get();

        return view('peserta.grid-settings', compact('kelasLomba', 'gridConfig', 'otherKelasLomba'));
    }

    /**
     * Update grid configuration
     */
    public function updateGridConfig(Request $request, KelasLomba $kelasLomba)
    {
        $validated = $request->validate([
            'rows' => 'required|integer|min:1|max:10',
            'columns' => 'required|integer|min:1|max:10',
        ], [
            'rows.required' => 'Jumlah baris harus diisi',
            'rows.integer' => 'Jumlah baris harus berupa angka',
            'rows.min' => 'Jumlah baris minimal 1',
            'rows.max' => 'Jumlah baris maksimal 10',
            'columns.required' => 'Jumlah kolom harus diisi',
            'columns.integer' => 'Jumlah kolom harus berupa angka',
            'columns.min' => 'Jumlah kolom minimal 1',
            'columns.max' => 'Jumlah kolom maksimal 10',
        ]);

        $gridConfig = GridPesertaConfig::firstOrCreate(
            ['kelas_lomba_id' => $kelasLomba->id],
            ['rows' => 4, 'columns' => 4]
        );

        $gridConfig->update($validated);

        $totalSlots = $validated['rows'] * $validated['columns'];
        return redirect()->route('peserta.grid-settings', $kelasLomba)
            ->with('success', "Konfigurasi grid berhasil diupdate! ({$validated['rows']} baris x {$validated['columns']} kolom = {$totalSlots} slot)");
    }

    /**
     * Copy peserta from another kelas
     */
    public function copyFromKelas(Request $request, KelasLomba $kelasLomba)
    {
        $validated = $request->validate([
            'source_kelas_id' => 'required|exists:kelas_lomba,id|different:kelas_lomba_id',
        ], [
            'source_kelas_id.required' => 'Pilih kelas sumber terlebih dahulu',
            'source_kelas_id.exists' => 'Kelas sumber tidak ditemukan',
            'source_kelas_id.different' => 'Tidak boleh menyalin dari kelas yang sama',
        ]);

        try {
            // Get source peserta
            $sourcePeserta = Peserta::where('kelas_lomba_id', $validated['source_kelas_id'])
                ->orderBy('nomor_urut')
                ->get();

            if ($sourcePeserta->isEmpty()) {
                return back()->with('error', 'Kelas sumber tidak memiliki peserta untuk disalin');
            }

            // Delete existing peserta
            Peserta::where('kelas_lomba_id', $kelasLomba->id)->delete();

            // Copy peserta
            $nomor = 1;
            foreach ($sourcePeserta as $source) {
                Peserta::create([
                    'kelas_lomba_id' => $kelasLomba->id,
                    'nomor_urut' => $nomor,
                    'nama_pemilik' => $source->nama_pemilik,
                    'nama_burung' => $source->nama_burung,
                    'alamat_team' => $source->alamat_team,
                    'nomor_gantangan' => $source->nomor_gantangan,
                ]);
                $nomor++;
            }

            $sourceKelas = KelasLomba::find($validated['source_kelas_id']);
            return redirect()->route('peserta.grid', $kelasLomba)
                ->with('success', "Berhasil menyalin {$sourcePeserta->count()} peserta dari {$sourceKelas->nama_kelas}");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset grid (clear all peserta)
     */
    public function resetGrid(Request $request, KelasLomba $kelasLomba)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ], [
            'confirm.accepted' => 'Anda harus menerima konfirmasi untuk mereset grid',
        ]);

        try {
            $count = Peserta::where('kelas_lomba_id', $kelasLomba->id)->count();
            Peserta::where('kelas_lomba_id', $kelasLomba->id)->delete();

            return redirect()->route('peserta.grid', $kelasLomba)
                ->with('success', "Grid berhasil direset. {$count} peserta telah dihapus");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * API: Get peserta by kelas with pagination
     */
    public function apiPeserta(Request $request)
    {
        $kelasId = $request->kelas_lomba_id;
        $search = $request->search;

        $query = Peserta::with('kelasLomba')
            ->where('kelas_lomba_id', $kelasId)
            // Filter: Only show peserta yang memiliki nama_pemilik atau nama_burung (tidak kosong)
            ->where(function($q) {
                $q->where(DB::raw("TRIM(COALESCE(nama_pemilik, ''))"), '<>', '')
                  ->orWhere(DB::raw("TRIM(COALESCE(nama_burung, ''))"), '<>', '');
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('nama_burung', 'like', "%{$search}%")
                  ->orWhere('alamat_team', 'like', "%{$search}%");
            });
        }

        $peserta = $query->orderBy('nomor_urut')->get();
        $kelas = KelasLomba::find($kelasId);

        return response()->json([
            'peserta' => $peserta,
            'kelas' => $kelas ? [
                'id' => $kelas->id,
                'nomor_kelas' => $kelas->nomor_kelas,
                'nama_kelas' => $kelas->nama_kelas,
                'peserta_count' => $peserta->count(),
                'batas_peserta' => $kelas->batas_peserta
            ] : null,
            'pagination' => [
                'html' => '' // Empty pagination since we show all data
            ]
        ]);
    }

    /**
     * API: Get grid config
     */
    public function apiGridConfig(KelasLomba $kelasLomba)
    {
        $config = GridPesertaConfig::firstOrCreate(
            ['kelas_lomba_id' => $kelasLomba->id],
            ['rows' => 4, 'columns' => 4]
        );

        return response()->json([
            'config' => $config
        ]);
    }

    /**
     * API: Get grid data HTML for inline display
     */
    public function apiGridData(KelasLomba $kelasLomba)
    {
        $config = GridPesertaConfig::firstOrCreate(
            ['kelas_lomba_id' => $kelasLomba->id],
            ['rows' => 4, 'columns' => 4]
        );

        $peserta = Peserta::where('kelas_lomba_id', $kelasLomba->id)
            ->get()
            ->keyBy('nomor_urut');

        return response()->json([
            'config' => $config,
            'peserta' => $peserta->toArray(),
            'html' => view('peserta.grid-inline', [
                'kelasLomba' => $kelasLomba,
                'config' => $config,
                'peserta' => $peserta
            ])->render()
        ]);
    }

    /**
     * Get slot kosong (untuk dropdown gantangan)
     */
    public function getSlotKosong(Request $request)
    {
        $kelasId = $request->kelas_lomba_id;

        // Get grid config to know total slots
        $gridConfig = GridPesertaConfig::where('kelas_lomba_id', $kelasId)->first();
        if (!$gridConfig) {
            return response()->json(['error' => 'Grid config tidak ditemukan'], 404);
        }

        $totalSlots = $gridConfig->rows * $gridConfig->columns;

        // Get all nomor_gantangan yang sudah terpakai di kelas ini
        $usedGantangan = Peserta::where('kelas_lomba_id', $kelasId)
            ->where(function($q) {
                // Hanya hitung peserta yang terisi (ada nama_pemilik atau nama_burung)
                $q->where(DB::raw("TRIM(COALESCE(nama_pemilik, ''))"), '<>', '')
                  ->orWhere(DB::raw("TRIM(COALESCE(nama_burung, ''))"), '<>', '');
            })
            ->whereNotNull('nomor_gantangan')
            ->pluck('nomor_gantangan')
            ->map(fn($v) => intval($v))
            ->toArray();

        // Generate slot kosong: 1 to totalSlots yang tidak ada di usedGantangan
        $slotKosong = [];
        for ($i = 1; $i <= $totalSlots; $i++) {
            if (!in_array($i, $usedGantangan)) {
                $slotKosong[] = $i;
            }
        }

        return response()->json([
            'success' => true,
            'total_slots' => $totalSlots,
            'used_gantangan' => $usedGantangan,
            'slot_kosong' => $slotKosong
        ]);
    }

    /**
     * Update nomor gantangan peserta + auto reorder nomor_urut
     */
    public function updateGantangan(Request $request)
    {
        $validated = $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'gantangan_baru' => 'required|numeric|min:1',
            'kelas_lomba_id' => 'required|exists:kelas_lomba,id'
        ]);

        $peserta = Peserta::findOrFail($validated['peserta_id']);
        $kelasId = $validated['kelas_lomba_id'];
        $gantanganBaru = intval($validated['gantangan_baru']);
        $nomorUrutLama = $peserta->nomor_urut;

        Log::info("updateGantangan called", [
            'peserta_id' => $peserta->id,
            'gantangan_lama' => $peserta->nomor_gantangan,
            'gantangan_baru' => $gantanganBaru,
            'nomor_urut_lama' => $nomorUrutLama
        ]);

        // Verify peserta belongs to this kelas
        if ($peserta->kelas_lomba_id != $kelasId) {
            return response()->json(['success' => false, 'message' => 'Peserta tidak sesuai dengan kelas'], 403);
        }

        // Check if gantangan is within valid range
        $gridConfig = GridPesertaConfig::where('kelas_lomba_id', $kelasId)->first();
        if (!$gridConfig) {
            return response()->json(['success' => false, 'message' => 'Grid config tidak ditemukan'], 404);
        }

        $totalSlots = $gridConfig->rows * $gridConfig->columns;
        if ($gantanganBaru < 1 || $gantanganBaru > $totalSlots) {
            return response()->json(['success' => false, 'message' => "Nomor gantangan harus antara 1 dan $totalSlots"], 422);
        }

        // Check if gantangan already used by another peserta
        $existingGantangan = Peserta::where('kelas_lomba_id', $kelasId)
            ->where('id', '!=', $peserta->id)
            ->where('nomor_gantangan', $gantanganBaru)
            ->where(function($q) {
                // Hanya check peserta yang terisi
                $q->where(DB::raw("TRIM(COALESCE(nama_pemilik, ''))"), '<>', '')
                  ->orWhere(DB::raw("TRIM(COALESCE(nama_burung, ''))"), '<>', '');
            })
            ->first();

        if ($existingGantangan) {
            Log::warning("Slot $gantanganBaru already occupied", ['occupied_by' => $existingGantangan->id]);
            return response()->json([
                'success' => false,
                'message' => "Nomor gantangan $gantanganBaru sudah digunakan peserta lain"
            ], 422);
        }

        // Check if slot (nomor_urut) already occupied by another peserta
        $existingAtSlot = Peserta::where('kelas_lomba_id', $kelasId)
            ->where('id', '!=', $peserta->id)
            ->where('nomor_urut', $gantanganBaru)
            ->first();

        if ($existingAtSlot) {
            // Jika ada peserta lain di slot tujuan, kita swap posisinya
            Log::info("Slot $gantanganBaru occupied by peserta {$existingAtSlot->id}, akan di-swap");

            // Swap nomor_urut
            $existingAtSlot->update(['nomor_urut' => $nomorUrutLama]);
            Log::info("Peserta {$existingAtSlot->id} moved to slot $nomorUrutLama");
        }

        // Update gantangan dan nomor_urut peserta yang di-move
        $peserta->update([
            'nomor_gantangan' => $gantanganBaru,
            'nomor_urut' => $gantanganBaru  // Auto-reorder: nomor_urut = gantangan baru
        ]);

        $peserta->refresh();
        Log::info("Peserta {$peserta->id} updated", [
            'nomor_gantangan' => $peserta->nomor_gantangan,
            'nomor_urut' => $peserta->nomor_urut
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nomor gantangan berhasil diubah dan otomatis masuk ke urutan ' . $gantanganBaru,
            'peserta' => [
                'id' => $peserta->id,
                'nomor_urut' => $peserta->nomor_urut,
                'nomor_gantangan' => $peserta->nomor_gantangan,
                'nama_pemilik' => $peserta->nama_pemilik,
                'nama_burung' => $peserta->nama_burung
            ]
        ]);
    }

}
