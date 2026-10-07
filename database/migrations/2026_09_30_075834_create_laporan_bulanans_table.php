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
        Schema::create('laporan_bulanans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ikk_id')
                ->constrained('ikks')
                ->restrictOnDelete();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->foreignId('pic_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');

            $table->decimal('realisasi', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->string('bukti_dukung')->nullable();

            $table->string('status')->default('draft');

            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_bulanans');
    }
};
