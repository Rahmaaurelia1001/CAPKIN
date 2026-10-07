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
    Schema::create('rincian_n_s_p_k_s', function (Blueprint $table) {
        $table->id();

        $table->foreignId('laporan_bulanan_id')
            ->constrained('laporan_bulanans')
            ->cascadeOnDelete();

        $table->enum('jenis_nspk', [
            'Norma',
            'Standar',
            'Pedoman',
            'Kriteria',
        ]);

        $table->string('nama_nspk');

        $table->text('keterangan')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_n_s_p_k_s');
    }
};
