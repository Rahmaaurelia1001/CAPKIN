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
        Schema::create('ikk_sumber_realisasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ikk_id')
                ->constrained('ikks')
                ->cascadeOnDelete();

            $table->string('sumber_tipe');

            $table->string('sumber_tabel');

            $table->string('sumber_field');

            $table->timestamps();

            $table->unique(
                'ikk_id',
                'ikk_sumber_realisasi_ikk_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikk_sumber_realisasi');
    }
};