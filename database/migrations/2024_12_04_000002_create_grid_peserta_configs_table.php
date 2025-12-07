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
        Schema::create('grid_peserta_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_lomba_id')->constrained('kelas_lomba')->onDelete('cascade');
            $table->integer('rows')->default(4);
            $table->integer('columns')->default(4);
            $table->timestamps();

            // Unique constraint untuk satu config per kelas
            $table->unique('kelas_lomba_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grid_peserta_configs');
    }
};
