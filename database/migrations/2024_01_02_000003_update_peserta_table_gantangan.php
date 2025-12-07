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
        Schema::table('peserta', function (Blueprint $table) {
            // Drop kolom J jika ada
            if (Schema::hasColumn('peserta', 'nilai_j')) {
                $table->dropColumn('nilai_j');
            }

            // Ubah kolom G menjadi nomor_gantangan (string) jika ada
            if (Schema::hasColumn('peserta', 'nilai_g')) {
                $table->dropColumn('nilai_g');
            }

            // Tambah kolom nomor_gantangan jika belum ada
            if (!Schema::hasColumn('peserta', 'nomor_gantangan')) {
                $table->string('nomor_gantangan', 50)->nullable()->after('alamat_team');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            // Kembalikan kolom J
            $table->decimal('nilai_j', 5, 2)->nullable();

            // Kembalikan kolom G
            $table->dropColumn('nomor_gantangan');
            $table->decimal('nilai_g', 5, 2)->nullable();
        });
    }
};

