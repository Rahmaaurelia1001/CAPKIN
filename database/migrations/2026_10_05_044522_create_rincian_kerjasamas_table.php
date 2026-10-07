<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rincian_kerjasamas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_bulanan_id')
                ->constrained('laporan_bulanans')
                ->cascadeOnDelete();

            $table->string('judul_kerjasama');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_kerjasamas');
    }
};