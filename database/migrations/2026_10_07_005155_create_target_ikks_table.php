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
    Schema::create('target_ikks', function (Blueprint $table) {
        $table->id();

        $table->foreignId('ikk_periode_id')
            ->constrained('ikk_periodes')
            ->cascadeOnDelete();

        $table->unsignedSmallInteger('tahun');

        $table->decimal('nilai_target', 20, 2);

        $table->timestamps();

        $table->unique(
            ['ikk_periode_id', 'tahun'],
            'target_ikk_periode_tahun_unique'
        );
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('target_ikks');
    }
};
