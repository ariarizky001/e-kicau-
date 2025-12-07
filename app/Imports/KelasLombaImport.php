<?php

namespace App\Imports;

use App\Models\KelasLomba;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;

class KelasLombaImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $successCount = 0;
    protected $skipCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Skip empty rows
                if (empty($row['nomor_kelas']) && empty($row['nama_kelas'])) {
                    continue;
                }

                // Validate row data
                $validator = Validator::make([
                    'nomor_kelas' => $row['nomor_kelas'] ?? null,
                    'nama_kelas' => $row['nama_kelas'] ?? null,
                    'status' => $row['status'] ?? 'aktif',
                    'batas_peserta' => $row['batas_peserta'] ?? null,
                ], [
                    'nomor_kelas' => 'required|string|max:10',
                    'nama_kelas' => 'required|string|max:255',
                    'status' => 'nullable|in:aktif,nonaktif',
                    'batas_peserta' => 'nullable|integer|min:1',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Baris " . ($row->getIndex() + 2) . ": " . implode(', ', $validator->errors()->all());
                    $this->skipCount++;
                    continue;
                }

                $data = $validator->validated();
                
                // Normalize status
                if (empty($data['status'])) {
                    $data['status'] = 'aktif';
                } else {
                    $data['status'] = strtolower($data['status']);
                    if (!in_array($data['status'], ['aktif', 'nonaktif'])) {
                        $data['status'] = 'aktif';
                    }
                }

                // Check if nomor_kelas already exists
                $existing = KelasLomba::where('nomor_kelas', $data['nomor_kelas'])->first();
                
                if ($existing) {
                    // Update existing
                    $existing->update([
                        'nama_kelas' => $data['nama_kelas'],
                        'status' => $data['status'],
                        'batas_peserta' => $data['batas_peserta'] ?? null,
                    ]);
                    $this->successCount++;
                } else {
                    // Create new
                    KelasLomba::create($data);
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                $this->errors[] = "Baris " . ($row->getIndex() + 2) . ": " . $e->getMessage();
                $this->skipCount++;
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getSkipCount(): int
    {
        return $this->skipCount;
    }
}

