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
        Schema::table('kelas_lomba', function (Blueprint $table) {
            // Hanya tambah jika belum ada
            if (!Schema::hasColumn('kelas_lomba', 'batas_peserta')) {
                $table->integer('batas_peserta')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_lomba', function (Blueprint $table) {
            $table->dropColumn('batas_peserta');
        });
    }
};

