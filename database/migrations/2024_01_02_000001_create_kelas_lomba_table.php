<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelas_lomba', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kelas', 10)->unique(); // 1, 2, 3A, 7, 8, dll
            $table->string('nama_kelas'); // MURAI BATU REMAJA 75K
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->integer('batas_peserta')->nullable(); // Batas jumlah peserta per kelas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_lomba');
    }
};

