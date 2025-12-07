<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Juri extends Model
{
    /** @use HasFactory<\Database\Factories\JuriFactory> */
    use HasFactory;

    protected $table = 'juris';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_juri',
    ];
}
