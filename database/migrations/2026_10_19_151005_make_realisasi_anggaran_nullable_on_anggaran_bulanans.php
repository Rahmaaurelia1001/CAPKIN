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
        Schema::table('anggaran_bulanans', function (Blueprint $table) {
            $table->decimal('realisasi_anggaran', 20, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggaran_bulanans', function (Blueprint $table) {
            $table->decimal('realisasi_anggaran', 20, 2)->default(0)->change();
        });
    }
};
