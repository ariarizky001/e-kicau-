<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KelasLomba extends Model
{
    use HasFactory;

    protected $table = 'kelas_lomba';

    protected $fillable = [
        'nomor_kelas',
        'nama_kelas',
        'status',
        'batas_peserta',
    ];

    protected $casts = [
        'status' => 'string',
        'batas_peserta' => 'integer',
    ];

    /**
     * Check if kelas has reached participant limit
     * Only count peserta yang terisi (memiliki nama_pemilik atau nama_burung)
     */
    public function isFull(): bool
    {
        if ($this->batas_peserta === null) {
            return false; // No limit set
        }

        // Count only peserta yang terisi (tidak kosong)
        $count = $this->peserta()
            ->where(function($q) {
                $q->whereNotNull('nama_pemilik')
                  ->where('nama_pemilik', '!=', '');
            })
            ->orWhere(function($q) {
                $q->whereNotNull('nama_burung')
                  ->where('nama_burung', '!=', '');
            })
            ->count();

        return $count >= $this->batas_peserta;
    }

    /**
     * Get remaining slots
     * Only count peserta yang terisi (tidak kosong)
     */
    public function getRemainingSlots(): ?int
    {
        if ($this->batas_peserta === null) {
            return null; // No limit
        }

        // Count only peserta yang terisi (tidak kosong)
        $count = $this->peserta()
            ->where(function($q) {
                $q->whereNotNull('nama_pemilik')
                  ->where('nama_pemilik', '!=', '');
            })
            ->orWhere(function($q) {
                $q->whereNotNull('nama_burung')
                  ->where('nama_burung', '!=', '');
            })
            ->count();

        return max(0, $this->batas_peserta - $count);
    }

    /**
     * Get all peserta for this kelas lomba
     */
    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class, 'kelas_lomba_id');
    }

    /**
     * Get grid configuration for this kelas lomba
     */
    public function gridConfig(): HasOne
    {
        return $this->hasOne(GridPesertaConfig::class, 'kelas_lomba_id');
    }

    /**
     * Get active kelas lomba
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}

