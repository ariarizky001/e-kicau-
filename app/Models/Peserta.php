<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'kelas_lomba_id',
        'nomor_urut',
        'nama_pemilik',
        'nama_burung',
        'alamat_team',
        'nomor_gantangan',
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
    ];

    /**
     * Get the kelas lomba that owns the peserta
     */
    public function kelasLomba(): BelongsTo
    {
        return $this->belongsTo(KelasLomba::class, 'kelas_lomba_id');
    }

    /**
     * Boot method to auto-generate nomor_urut only when not explicitly set
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($peserta) {
            // Only auto-generate if nomor_urut is not already set (for manual create, not grid)
            if (is_null($peserta->nomor_urut) || $peserta->nomor_urut === 0) {
                $maxNomor = static::where('kelas_lomba_id', $peserta->kelas_lomba_id)
                    ->max('nomor_urut');
                $peserta->nomor_urut = ($maxNomor ?? 0) + 1;
            }
        });
    }
}

