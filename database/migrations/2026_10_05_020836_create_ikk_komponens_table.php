<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ikk_komponens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ikk_id')
                ->constrained('ikks')
                ->cascadeOnDelete();

            $table->string('kode_komponen');
            $table->string('nama_komponen');

            $table->decimal('bobot', 5, 2);

            $table->string('sumber_pembilang')->nullable();
            $table->string('sumber_penyebut')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['ikk_id', 'kode_komponen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ikk_komponens');
    }
};