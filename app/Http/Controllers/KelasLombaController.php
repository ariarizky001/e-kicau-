<?php

namespace App\Http\Controllers;

use App\Models\KelasLomba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelasLombaController extends Controller
{
    public function index(Request $request)
    {
        $query = KelasLomba::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_kelas', 'like', "%{$search}%")
                  ->orWhere('nama_kelas', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $kelasLomba = $query->withCount('peserta')->orderBy('id', 'asc')->paginate(15);

        return view('kelas-lomba.index', compact('kelasLomba'));
    }

    public function create()
    {
        return view('kelas-lomba.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kelas' => 'required|string|max:10|unique:kelas_lomba,nomor_kelas',
            'nama_kelas' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'batas_peserta' => 'nullable|integer|min:1',
        ]);

        KelasLomba::create($validated);

        return redirect()->route('kelas-lomba.index')
            ->with('success', 'Kelas lomba berhasil ditambahkan');
    }

    public function edit(KelasLomba $kelasLomba)
    {
        return view('kelas-lomba.edit', compact('kelasLomba'));
    }

    public function update(Request $request, KelasLomba $kelasLomba)
    {
        $validated = $request->validate([
            'nomor_kelas' => 'required|string|max:10|unique:kelas_lomba,nomor_kelas,' . $kelasLomba->id,
            'nama_kelas' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'batas_peserta' => 'nullable|integer|min:1',
        ]);

        // Validate that batas_peserta is not less than current peserta count
        $currentPesertaCount = $kelasLomba->peserta()->count();
        if ($request->batas_peserta !== null && $request->batas_peserta < $currentPesertaCount) {
            return back()->withInput()
                ->with('error', "Batas peserta tidak boleh kurang dari jumlah peserta saat ini ({$currentPesertaCount})");
        }

        $kelasLomba->update($validated);

        return redirect()->route('kelas-lomba.index')
            ->with('success', 'Kelas lomba berhasil diupdate');
    }

    public function destroy(KelasLomba $kelasLomba)
    {
        // Check if kelas has peserta dengan nama_pemilik atau nama_burung yang terisi
        $filledPeserta = $kelasLomba->peserta()
            ->where(function($query) {
                $query->whereNotNull('nama_pemilik')
                      ->where('nama_pemilik', '<>', '')
                      ->orWhereNotNull('nama_burung')
                      ->where('nama_burung', '<>', '');
            })
            ->count();

        if ($filledPeserta > 0) {
            $message = 'Tidak dapat menghapus kelas lomba yang sudah memiliki peserta';

            // If AJAX request, return JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return redirect()->route('kelas-lomba.index')
                ->with('error', $message);
        }

        $kelasLomba->delete();

        $message = 'Kelas lomba berhasil dihapus';

        // If AJAX request, return JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('kelas-lomba.index')
            ->with('success', $message);
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('kelas-lomba.import');
    }

    /**
     * Handle import from Excel/CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $errors = [];
        $successCount = 0;
        $skipCount = 0;

        try {
            if ($extension === 'csv') {
                $data = $this->readCsv($file);
            } else {
                // Try to use Laravel Excel if available
                if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                    return $this->importWithLaravelExcel($file);
                } else {
                    return redirect()->route('kelas-lomba.import')
                        ->with('error', 'Package Laravel Excel belum terinstall. Silakan install dengan: composer require maatwebsite/excel');
                }
            }

            // Process CSV data
            foreach ($data as $index => $row) {
                if ($index === 0) continue; // Skip header

                // Skip empty rows
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }

                try {
                    $validator = Validator::make([
                        'nomor_kelas' => $row[0] ?? null,
                        'nama_kelas' => $row[1] ?? null,
                        'status' => strtolower($row[2] ?? 'aktif'),
                        'batas_peserta' => !empty($row[3]) ? (int)$row[3] : null,
                    ], [
                        'nomor_kelas' => 'required|string|max:10',
                        'nama_kelas' => 'required|string|max:255',
                        'status' => 'nullable|in:aktif,nonaktif',
                        'batas_peserta' => 'nullable|integer|min:1',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Baris " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                        $skipCount++;
                        continue;
                    }

                    $data = $validator->validated();
                    if (empty($data['status'])) {
                        $data['status'] = 'aktif';
                    }

                    // Check if exists
                    $existing = KelasLomba::where('nomor_kelas', $data['nomor_kelas'])->first();
                    if ($existing) {
                        $existing->update($data);
                    } else {
                        KelasLomba::create($data);
                    }
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                    $skipCount++;
                }
            }

            $message = "Import selesai! Berhasil: {$successCount} kelas";
            if ($skipCount > 0) {
                $message .= ", Dilewati: {$skipCount} kelas";
            }

            if (!empty($errors)) {
                return redirect()->route('kelas-lomba.import')
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('kelas-lomba.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('kelas-lomba.import')
                ->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    /**
     * Import using Laravel Excel
     */
    protected function importWithLaravelExcel($file)
    {
        try {
            $import = new \App\Imports\KelasLombaImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);

            $successCount = $import->getSuccessCount();
            $skipCount = $import->getSkipCount();
            $errors = $import->getErrors();

            $message = "Import selesai! Berhasil: {$successCount} kelas";
            if ($skipCount > 0) {
                $message .= ", Dilewati: {$skipCount} kelas";
            }

            if (!empty($errors)) {
                return redirect()->route('kelas-lomba.import')
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('kelas-lomba.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('kelas-lomba.import')
                ->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    /**
     * Read CSV file
     */
    protected function readCsv($file)
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $data[] = $row;
        }

        fclose($handle);
        return $data;
    }

    /**
     * Download template Excel/CSV
     */
    public function downloadTemplate()
    {
        $headers = [
            'nomor_kelas',
            'nama_kelas',
            'status',
            'batas_peserta'
        ];

        $sampleData = [
            ['1', 'MURAI BATU REMAJA 75K', 'aktif', '24'],
            ['2', 'MURAI BATU A RAHAYU 220K', 'aktif', '24'],
            ['3A', 'MURAI BURSA A SURAKARTAN 75K', 'aktif', '24'],
            ['7', 'KACER SURAKARTAN 75K', 'aktif', '24'],
            ['8', 'CENDET SURAKARTAN 75K', 'aktif', '24'],
            ['9', 'MURAI BATU B MEMBER SHC 550K', 'aktif', '24'],
            ['10', 'MURAI REMAJA KASUNANAN 120K', 'aktif', '24'],
            ['11', 'CUCAK HIJAU KASUNANAN 120K', 'aktif', '24'],
            ['12', 'MURAI BATU B RAHAYU 220K', 'aktif', '24'],
        ];

        // Try to use Laravel Excel if available
        if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
            try {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray {
                        protected $headers;
                        protected $data;

                        public function __construct($headers, $data)
                        {
                            $this->headers = $headers;
                            $this->data = $data;
                        }

                        public function array(): array
                        {
                            return array_merge([$this->headers], $this->data);
                        }
                    },
                    'template_import_kelas_lomba.xlsx'
                );
            } catch (\Exception $e) {
                // Fallback to CSV
            }
        }

        // Fallback: create CSV
        $filename = 'template_import_kelas_lomba.csv';
        $file = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($file, $headers);

        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($file, $row);
        }

        rewind($file);
        $content = stream_get_contents($file);
        fclose($file);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Store kelas lomba via inline form (AJAX)
     */
    public function storeInline(Request $request)
    {
        $validated = $request->validate([
            'nomor_kelas' => 'required|string|max:10|unique:kelas_lomba,nomor_kelas',
            'nama_kelas' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'batas_peserta' => 'nullable|integer|min:1',
        ], [
            'nomor_kelas.required' => 'Nomor kelas harus diisi',
            'nomor_kelas.unique' => 'Nomor kelas sudah ada',
            'nama_kelas.required' => 'Nama kelas harus diisi',
            'status.required' => 'Status harus dipilih',
        ]);

        $kelas = KelasLomba::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kelas lomba berhasil ditambahkan',
            'data' => $kelas,
        ], 201);
    }
}
