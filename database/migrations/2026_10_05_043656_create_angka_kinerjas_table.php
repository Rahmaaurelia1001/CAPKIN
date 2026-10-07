<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angka_kinerjas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_bulanan_id')
                ->constrained('laporan_bulanans')
                ->cascadeOnDelete();

            $table->foreignId('ikk_komponen_id')
                ->constrained('ikk_komponens')
                ->cascadeOnDelete();

            $table->decimal('pembilang', 15, 2)->nullable();
            $table->decimal('penyebut', 15, 2)->nullable();

            $table->timestamps();

            $table->unique([
                'laporan_bulanan_id',
                'ikk_komponen_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angka_kinerjas');
    }
};