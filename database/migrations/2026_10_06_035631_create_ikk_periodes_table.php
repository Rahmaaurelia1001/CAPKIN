<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ikk_periodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ikk_id')
                ->constrained('ikks')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('tahun_mulai');

            $table->timestamps();

            $table->unique(
                ['ikk_id', 'tahun_mulai'],
                'ikk_periode_ikk_tahun_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ikk_periodes');
    }
};