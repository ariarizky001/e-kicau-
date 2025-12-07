<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GridPesertaConfig extends Model
{
    use HasFactory;

    protected $table = 'grid_peserta_configs';

    protected $fillable = [
        'kelas_lomba_id',
        'rows',
        'columns',
    ];

    protected $casts = [
        'rows' => 'integer',
        'columns' => 'integer',
    ];

    /**
     * Get the kelas lomba
     */
    public function kelasLomba(): BelongsTo
    {
        return $this->belongsTo(KelasLomba::class, 'kelas_lomba_id');
    }
}
