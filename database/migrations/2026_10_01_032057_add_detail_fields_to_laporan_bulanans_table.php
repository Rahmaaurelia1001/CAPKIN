<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->text('narasi_capaian')->nullable();
            $table->text('kendala')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->decimal('realisasi_anggaran', 18, 2)->nullable();
            $table->json('detail_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropColumn([
                'narasi_capaian',
                'kendala',
                'tindak_lanjut',
                'realisasi_anggaran',
                'detail_data',
            ]);
        });
    }
};