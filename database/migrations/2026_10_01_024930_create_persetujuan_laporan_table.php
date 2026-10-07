<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persetujuan_laporan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_bulanan_id')
                ->constrained('laporan_bulanans')
                ->cascadeOnDelete();

            // Tahap 1 = pengajuan PIC
            // Tahap 2 = Kabid/Kabag
            // Tahap 3 = Katim Umper
            // Tahap 4 = Kabag Umum
            $table->unsignedTinyInteger('tahap');

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Contoh: diajukan, disetujui, dikembalikan
            $table->string('keputusan');

            $table->text('catatan')->nullable();

            $table->timestamp('diproses_pada')->nullable();

            $table->timestamps();

            $table->index([
                'laporan_bulanan_id',
                'tahap',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persetujuan_laporan');
    }
};