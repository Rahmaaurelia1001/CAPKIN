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
        Schema::table('ikks', function (Blueprint $table) {
            $table->foreignId('sasaran_kegiatan_id')
                ->nullable()
                ->after('id')
                ->constrained('sasaran_kegiatans')
                ->restrictOnDelete();

            $table->string('jenis_perhitungan')
                ->default('tunggal')
                ->after('satuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ikks', function (Blueprint $table) {
            $table->dropForeign(['sasaran_kegiatan_id']);
            $table->dropColumn([
                'sasaran_kegiatan_id',
                'jenis_perhitungan',
            ]);
        });
    }
};