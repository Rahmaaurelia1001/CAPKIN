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
    Schema::create('rincian_pelatihans', function (Blueprint $table) {
        $table->id();

        $table->foreignId('laporan_bulanan_id')
            ->constrained('laporan_bulanans')
            ->cascadeOnDelete();

        $table->string('nama_pelatihan');

        $table->enum('sumber_dana', ['PNBP', 'RM']);

        $table->unsignedInteger('jumlah_peserta')->default(0);
        $table->unsignedInteger('jumlah_lulus')->default(0);
        $table->unsignedInteger('jumlah_laki_laki')->default(0);
        $table->unsignedInteger('jumlah_perempuan')->default(0);

        $table->timestamps();
    });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_pelatihans');
    }
};
