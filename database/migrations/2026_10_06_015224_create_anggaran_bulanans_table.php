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
    Schema::create('anggaran_bulanans', function (Blueprint $table) {
        $table->id();

        $table->foreignId('ikk_id')
            ->constrained('ikks')
            ->cascadeOnDelete();

        $table->unsignedTinyInteger('bulan');

        $table->unsignedSmallInteger('tahun');

        $table->foreignId('pagu_anggaran_id')
            ->constrained('pagu_anggarans')
            ->restrictOnDelete();

        $table->decimal('realisasi_anggaran', 20, 2)
            ->default(0);

        $table->text('keterangan')->nullable();

        $table->timestamps();

        $table->unique(
            ['ikk_id', 'bulan', 'tahun'],
            'anggaran_ikk_bulan_tahun_unique'
        );
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('anggaran_bulanans');
    }
};
