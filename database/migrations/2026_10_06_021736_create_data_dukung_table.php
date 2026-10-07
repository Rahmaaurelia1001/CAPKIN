<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_dukung', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_bulanan_id')
                ->nullable()
                ->constrained('laporan_bulanans')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('bulan')->nullable();

            $table->unsignedSmallInteger('tahun')->nullable();

            $table->string('nama_file');

            $table->string('path_file');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_dukung');
    }
};