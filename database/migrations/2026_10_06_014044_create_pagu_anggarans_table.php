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
    Schema::create('pagu_anggarans', function (Blueprint $table) {
        $table->id();

        $table->foreignId('ikk_id')
            ->constrained('ikks')
            ->cascadeOnDelete();

        $table->unsignedSmallInteger('tahun');

        $table->unsignedInteger('nomor_revisi');

        $table->decimal('nilai_pagu', 20, 2);

        $table->unsignedTinyInteger('berlaku_mulai_bulan');

        $table->text('keterangan')->nullable();

        $table->timestamps();

        $table->unique(
            ['ikk_id', 'tahun', 'nomor_revisi'],
            'pagu_ikk_tahun_revisi_unique'
        );
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('pagu_anggarans');
    }
};
