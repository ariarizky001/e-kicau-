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
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_lomba_id')->constrained('kelas_lomba')->onDelete('cascade');
            $table->integer('nomor_urut'); // Auto-number per kelas
            $table->string('nama_pemilik'); // NAMA PESERTA
            $table->string('nama_burung'); // NAMA BURUNG
            $table->string('alamat_team')->nullable(); // Alamat/Team
            $table->string('nomor_gantangan', 50)->nullable(); // Kolom G (nomor gantangan burung)
            $table->timestamps();

            $table->index(['kelas_lomba_id', 'nomor_urut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};

