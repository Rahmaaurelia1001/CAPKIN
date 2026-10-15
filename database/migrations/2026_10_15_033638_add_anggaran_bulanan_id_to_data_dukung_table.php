<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_dukung', function (Blueprint $table) {
            $table->foreignId('anggaran_bulanan_id')
                ->nullable()
                ->after('laporan_bulanan_id')
                ->constrained('anggaran_bulanans')
                ->cascadeOnDelete();

            $table->unique(
                'anggaran_bulanan_id',
                'data_dukung_anggaran_bulanan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('data_dukung', function (Blueprint $table) {
            $table->dropUnique('data_dukung_anggaran_bulanan_unique');
            $table->dropConstrainedForeignId('anggaran_bulanan_id');
        });
    }
};